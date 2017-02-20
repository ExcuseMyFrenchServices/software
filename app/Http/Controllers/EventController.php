<?php namespace App\Http\Controllers;

use App\Assignment;
use App\Client;
use App\Event;
use App\Http\Requests\Event\CreateEventRequest;
use App\Http\Requests\Event\TimesheetRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Role;
use App\Services\AvailableUsers;
use App\Services\UsersMissions;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
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
        $events = Event::where('event_date', '>=', date('Y-m-d 05:00:00', strtotime('+11 hour')))->get()->sortBy('event_date');

        return view('event.index')->with(compact('events'));
    }

    public function indexPast(Request $request)
    {
        $range = $request->input('date-range') ?: date('Y-m');

        $events = Event::all()->filter(function ($event) use ($range) {
            return substr($event->event_date, 0, 7) == $range && $event->event_date < date('Y-m-d 05:00:00', strtotime('+11 hour'));
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
        $start_time = array_values(array_filter(array_flatten($request->input('start_times'))));

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
            'event_date'    => new DateTime($request->input('event_date').$start_time[0]),
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

        $start_time = array_values(array_filter(array_flatten($request->input('start_times'))));
        $old_start_time = $event->start_time;

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
        $event->event_date      = new DateTime($request->input('event_date').$start_time[0]);

        $event->save();

        // Update assignments for removed hours
        Assignment::where('event_id', $event->id)->each(function ($assignment) use ($event,$old_start_time) 
        {
            if (!in_array($assignment->time, $event->start_time)) 
            {
                $time_diff = array_diff_assoc($event->start_time, $old_start_time);
                for ($i=0; $i < count($event->start_time); $i++) 
                { 
                    if($assignment->time == $old_start_time[$i])
                    {
                        $assignment->time = $event->start_time[$i];
                        $assignment->save();

                        Mail::send('emails.assignment-update', ['event' => $assignment->event, 'assignment' => $assignment], function($message) use ($assignment) {
                            $message->to($assignment->user->profile->email)->subject('Important : Event Start Time Updated !');
                        });
                    }
                }
            }
            else
            {
                Mail::send('emails.event-update', ['event' => $assignment->event, 'assignment' => $assignment], function($message) use ($assignment) {
                    $message->to($assignment->user->profile->email)->subject('Important : Event Updated');
                }); 
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

        $client = Client::join('events', 'events.client_id','=','clients.id')
                            ->where('events.id','=', $eventId)
                            ->first();                 
        
        $userMissions = new UsersMissions;                    
        $availableService = new AvailableUsers;

        $available = $availableService->get($event, $time);

        $unavailable = User::all()->diff($available);

        $roles = Role::all();


        return view('event.staff')->with(compact('event','time', 'available', 'unavailable', 'roles', 'client', 'userMissions'));
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

        $assignment = Assignment::where('event_id',$event->id)->where('user_id',$userId)->first();
        $assignment->notification = true;
        $assignment->save();

        Mail::send('emails.admin-notification', ['event' => $event, 'assignment' => $assignment], function($message) use ($assignment) {
            $message->to($assignment->user->profile->email)->subject("Admin Notification");
        });

        Session::flash('success', 'The admin was sent successfully');
        
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
                return $event->event_date >= date('Y-m-d 05:00:00', strtotime('+11 hour'));
            })->sortBy('event_date');

            return view('event.index')->with(compact('events', 'user', 'assignments'));
        }

        $events = Event::where('event_date', '>=', date('Y-m-d 05:00:00', strtotime('+11 hour')))->get()->sortBy('event_date');

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
        $admin = User::find($event->admin_id);

        Mail::send('emails.notify-client', ['event' => $event, 'client' => $event->client, 'assignments' => $event->assignments, 'admin'=>$admin], function($message) use ($event) {
            $message->to($event->client->email)->cc('thomasleclercq90010@gmail.com')->subject('Event Confirmation');
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
        $event = Event::find($eventId);

        for($i = 0; $i < $request->assignment_number; $i++)
        {
            $id = 'id-'.$i;
            $assignment = Assignment::find($request->$id);

            $start_time = $assignment->id.'-start-time';
            $hours = $assignment->id.'-hours';
            $break = $assignment->id.'-break';

            $assignment->start_time = $request->$start_time;
            $assignment->hours = $request->$hours;
            $assignment->break = $request->$break;
            $assignment->save();
        }
        //Add a report from the admin of the event to an event
        
        $event->report = $request->input('report');
        $event->save();

        return redirect('event/' . $eventId);
    }

    public function confirmStartTime(Request $request, $eventId)
    {
        $event = Event::find($eventId);

        $assignment = Assignment::find($request->input('assignment_id'));
        $old_assignment_time = $assignment->time;
        $assignment->time = $request->input('confirmed_start_time');
        $assignment->start_time_confirmation = true;

        if(count($event->start_time) > 1)
        {
            //Look for the key of the time changed in the array 
            $start_time_key = array_search($old_assignment_time, $event->start_time);
            //Prepare a new entry for the array $event->start_time
            $new_time = array($start_time_key => $request->input('confirmed_start_time') );
            //Replace the old start time by the new
            $event->start_time = array_replace($event->start_time, $new_time);
        }
        else
        {
            $event->start_time = array(0 => $request->input('confirmed_start_time'));
        }

        $event->save();
        $assignment->save();
        return redirect()->back();
    }

    public function changeUserRole()
    {
        if(Auth::user()->role_id == 1)
        {
            $user = User::find(Auth::user()->id);
            $user->role_id = 11;
            $user->save();
            return redirect('/events/101');
            
        }

        if(Auth::user()->role_id == 11)
        {
            $user = User::find(Auth::user()->id);
            $user->role_id = 1;
            $user->save();
            return redirect('/event/');
        }
    }

    public function briefMonthReport()
    {
        $year = date('Y');
        $last_year = $year-1;
        $last_last_year = $last_year-1;

        $month = date('m');

        $this_year_report = [];
        for($i = 1; $i <= 12; $i++)
        {
            $last_month_date = date($year.'.'.$i.'.01');
            $i++;
            $this_month_date = date($year.'.'.$i.'.01');
            $i--;   

            $this_year_report[$i] = $year_events = DB::table('events')
                                            ->select('assignments.user_id')
                                            ->join('assignments','assignments.event_id','=','events.id')
                                            ->where('events.event_date','>=',$last_month_date)
                                            ->where('events.event_date','<',$this_month_date)
                                            ->count();    
        }

        $last_year_report = [];
        for($i = 1; $i <= 12; $i++)
        {
            $last_month_date = date($last_year.'.'.$i.'.01');
            $i++;
            $this_month_date = date($last_year.'.'.$i.'.01');
            $i--; 

            $last_year_report[$i] = $year_events = DB::table('events')
                                            ->select('assignments.user_id')
                                            ->join('assignments','assignments.event_id','=','events.id')
                                            ->where('events.event_date','>=',$last_month_date)
                                            ->where('events.event_date','<',$this_month_date)
                                            ->count();                                  
        }   

        $last_last_year_report = [];
        for($i = 1; $i <= 12; $i++)
        {
            $last_month_date = date($last_last_year.'.'.$i.'.01');
            $i++;
            $this_month_date = date($last_last_year.'.'.$i.'.01');
            $i--; 

            $last_last_year_report[$i] = $year_events = DB::table('events')
                                            ->select('assignments.user_id')
                                            ->join('assignments','assignments.event_id','=','events.id')
                                            ->where('events.event_date','>=',$last_month_date)
                                            ->where('events.event_date','<',$this_month_date)
                                            ->count();                                  
        }              

        return view('reports.monthOverview')->with(compact('last_last_year_report','last_year_report', 'this_year_report','month','year','last_year','last_last_year'));

    }

    public function monthReport($year, $month)
    {
        $date = $year.'-'.$month.'-01';
        $written_date = date('F Y', strtotime($date));

        $next_month = $month+1;
        $last_month = $month-1;

        $next_year = $year+1;
        $last_year = $year-1;

        $next_report_month = $year.'.'.$next_month.'.01';
        $days_numbers_in_month = date('t', strtotime($date));

        $events = DB::table('events')
                    ->select('clients.name',DB::raw('count(DISTINCT events.id) as events_number'), DB::raw('count(assignments.user_id) as staff_number'), DB::raw('count(DISTINCT events.event_date) as days_worked'))
                    ->join('assignments','assignments.event_id', '=', 'events.id')
                    ->join('clients','clients.id','=','events.client_id')
                    ->where('events.event_date','>=',$date)
                    ->where('events.event_date','<',$next_report_month)
                    ->groupBy('clients.name')
                    ->orderBy('events_number', 'DESC')
                    ->get();  

        $total_events = DB::table('events')
                    ->select(DB::raw('count(DISTINCT events.id) as events_number'), DB::raw('count(assignments.user_id) as staff_number'), DB::raw('count(DISTINCT events.event_date) as days_worked'))
                    ->join('assignments','assignments.event_id', '=', 'events.id')
                    ->join('clients','clients.id','=','events.client_id')
                    ->where('events.event_date','>=',$date)
                    ->where('events.event_date','<',$next_report_month)
                    ->get();   

        return view('reports.month-report')->with(compact('events','total_events','written_date','year','month', 'last_month', 'next_month', 'last_year', 'next_year', 'days_numbers_in_month'));
    }
}