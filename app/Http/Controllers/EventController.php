<?php namespace App\Http\Controllers;

use App\Assignment;
use App\Client;
use App\Event;
use App\Http\Requests\Event\CreateEventRequest;
use App\Http\Requests\Event\TimesheetRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Role;
use App\Services\AvailableUsers;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use DateTime;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['confirmAssistance']
        ]);

        $this->middleware('admin', [
            'only' => ['create', 'destroy', 'indexPast']
        ]);
    }

    public function index()
    {
        $events = Event::where('event_date', '>=', date('Y-m-d'))->get()->sortBy('event_date');

        return view('event.index')->with(compact('events'));
    }

    public function indexPast(Request $request)
    {
        $range = $request->input('date-range') ?: date('Y-m');

        $events = Event::all()->filter(function ($event) use ($range) {
            return substr($event->event_date, 0, 7) == $range && $event->event_date < date('Y-m-d');
        })->sortByDesc('event_date');

        return view('event.index')->with(compact('events', 'range'));
    }

    public function create()
    {
        $clients = Client::all()->sortBy('name');
        $roles = Role::all();

        return view('event.create')->with(compact('clients', 'roles'));
    }

    public function store(CreateEventRequest $request)
    {
        $event = Event::create([
            'event_name'    => $request->input('event_name'),
            'client_id'     => $request->input('client'),
            'finish_time'   => $request->input('finish_time'),
            'number_staff'  => $request->input('number_staff'),
            'address'       => $request->input('address'),
            'uniform'       => $request->input('uniform'),
            'glasses'       => $request->input('glasses') == 'on',
            'soft_drinks'   => $request->input('soft_drinks') == 'on',
            'bar'           => $request->input('bar') == 'on',
            'notes'         => $request->input('notes'),
            'start_time'    => array_values(array_filter(array_flatten($request->input('start_times')))),
            'booking_date'  => new DateTime($request->input('booking_date')),
            'event_date'    => new DateTime($request->input('event_date')),
        ]);

        return redirect('event/' . $event->id);
    }

    public function show($eventId)
    {
        $event = Event::find($eventId);

        return view('event.detail')->with(compact('event'));
    }

    public function edit($eventId)
    {
        $event = Event::find($eventId);

        $clients = Client::all()->sortBy('name');

        return view('event.create')->with(compact('event', 'clients'));
    }

    public function update(UpdateEventRequest $request, $eventId)
    {
        $event = Event::find($eventId);

        $event->event_name      = $request->input('event_name');
        $event->client_id       = $request->input('client');
        $event->finish_time     = $request->input('finish_time');
        $event->number_staff    = $request->input('number_staff');
        $event->address         = $request->input('address');
        $event->uniform         = $request->input('uniform');
        $event->glasses         = $request->input('glasses') == 'on';
        $event->soft_drinks     = $request->input('soft_drinks') == 'on';
        $event->bar             = $request->input('bar') == 'on';
        $event->notes           = $request->input('notes');
        $event->start_time      = array_values(array_filter(array_flatten($request->input('start_times'))));
        $event->booking_date    = new DateTime($request->input('booking_date'));
        $event->event_date      = new DateTime($request->input('event_date'));

        $event->save();

        // Delete assignments for removed hours
        Assignment::where('event_id', $event->id)->each(function ($assignment) use ($event) {
            if (!in_array($assignment->time, $event->start_time)) {
                $assignment->delete();
            }
        });

        Session::flash('success', 'Event successfully updated');

        return redirect()->back();
    }

    public function copy($eventId)
    {
        $event = Event::find($eventId);

        $event->id              = null;
        $event->start_time      = [];
        $event->finish_time     = null;
        $event->number_staff    = null;
        $event->booking_date    = null;
        $event->event_date      = null;

        $clients = Client::all()->sortBy('name');

        return view('event.create')->with(compact('event', 'clients'));
    }

    public function destroy($eventId)
    {
        $event = Event::find($eventId);

        $event->delete();

        return redirect('event');
    }

    public function staff($eventId, $time)
    {
        $event = Event::find($eventId);

        $availableService = new AvailableUsers;

        $available = $availableService->get($event, $time);

        $unavailable = User::all()->diff($available);

        $roles = Role::all();

        return view('event.staff')->with(compact('event', 'time', 'available', 'unavailable', 'roles'));
    }

    public function assign(Request $request, $eventId)
    {
        $event = Event::find($eventId);

        foreach($request->except(['_token', 'time']) as $key => $value) {
            Assignment::create([
                'event_id'  => $event->id,
                'time'      => $request->input('time'),
                'user_id'   => $key,
                'status'    => 'pending',
                'hash'      =>  str_random(15)
            ]);
        }

        return redirect('event/' . $event->id);
    }

    public function setAdmin($eventId, $userId)
    {
        $event = Event::find($eventId);

        $event->admin_id = $userId;

        $event->save();

        return redirect()->back();
    }

    public function events($userId)
    {
        $user = User::find($userId);

        // Only admins can see other people events
        if ($user->id != Auth::user()->id && Auth::user()->role_id != 1) {
            return redirect('/');
        }

        if ($user->role_id != 1) {
            $assignments = Assignment::where('user_id', $userId)->get();

            $events = $assignments->map(function($assignment) {
                return Event::find($assignment->event_id);
            })->filter(function ($event) {
                return $event->event_date >= date('Y-m-d');
            })->sortBy('event_date');

            return view('event.index')->with(compact('events', 'user', 'assignments'));
        }

        $events = Event::where('event_date', '>=', date('Y-m-d'))->get()->sortBy('event_date');

        return view('event.index')->with(compact('events'));
    }

    public function confirmAssistance($hash)
    {
        $assignment = Assignment::where('hash', $hash)->get()->first();

        if (!$assignment) {
            return redirect('/');
        }

        Auth::loginUsingId($assignment->user_id);

        $assignment->status = 'confirmed';
        $assignment->hash = '';

        $assignment->save();

        return redirect('event/' . $assignment->event_id);
    }

    public function notifyClient($eventId)
    {
        $event = Event::find($eventId);

        Mail::send('emails.notify-client', ['event' => $event, 'client' => $event->client, 'assignments' => $event->assignments], function($message) use ($event) {
            $message->to($event->client->email)->subject('Event Confirmation');
        });

        $event->client_notification = true;
        $event->save();

        Session::flash('success', 'The notification was sent successfully');

        return redirect()->back();
    }

    public function timesheetIndex()
    {
        $events = Event::where('admin_id', '=', Auth::user()->id)->where('event_date', '<=', date('Y-m-d'))->get()->sortByDesc('event_date');

        return view('event.timesheet-index')->with(compact('events'));
    }

    public function getTimesheet(TimesheetRequest $request, $eventId)
    {
        $event = Event::find($eventId);

        return view('event.timesheet')->with(compact('event'));
    }

    public function saveTimesheet(TimesheetRequest $request, $eventId)
    {
        foreach($request->except(['_token']) as $assignment => $hours) {
            $assignment = Assignment::find($assignment);
            $assignment->hours = $hours;
            $assignment->save();
        }

        return redirect('event/' . $eventId);
    }
}