<?php namespace App\Http\Controllers;

use App\Assignment;
use App\Availability;
use App\Client;
use App\Event;
use App\Uniform;
use App\Stock;
use App\OutStock;
use App\BarEvent;
use App\Modification;
use App\Http\Requests\Event\CreateEventRequest;
use App\Http\Requests\Event\TimesheetRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Http\Controllers\ModificationsController;
use App\Role;
use App\Services\AvailableUsers;
use App\Services\UsersMissions;
use App\Services\stockItems;
use App\Services\Modifications;
use App\Services\UserChecker;
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
        $uniforms = Uniform::all();

        $beers = [];
        $whines = [];
        $spirits = [];
        $cocktails = [];
        $shots = [];
        $ingredients = [];
        $softs = [];
        $bars = [];
        $furnitures = [];

        return view('event.create')->with(compact('clients', 'roles', 'uniforms','beers','whines','spirits','cocktails','shots','ingredients','softs','bars','furnitures','event'));
    }

    public function preBooking(Request $request)
    {
        $start_time = [];
        $start_time[] = $request->input('start_time');
        $date = date_format(date_create($request->input('event_date')),'Y-m-d');
        $start = date_format(date_create($request->input('start_time')),'H:i:s');
        $event_date = $date." ".$start;

        $event = Event::create([
            'client_id'     =>  21,
            'event_name'    =>  "pre-booking event",
            'event_type'    =>  "pre-booking event",
            'number_staff'  =>  $request->input('staffNeeded'),
            'event_date'    =>  $event_date,
            'start_time'    =>  $start_time,
            'finish_time'   =>  $request->input('finish_time')
        ]);

        $event->save();

        return redirect(url('event/'.$event->id));
    }

    public function store(CreateEventRequest $request)
    {
        $start_time = array_values(array_filter(array_flatten($request->input('start_times'))));

        // Add an hour to the event date so the past_event function can filters more effectively
        if(count($start_time) > 0)
        {
            $hour = $start_time[0];
        }
        else
        {
            if($request->input('guest_arrival') === null)
            {
                $hour = "00:00";
            }
            else
            {
                $hour = $request->input('guest_arrival');
            }
        }

        $event = Event::create([
            'event_name'        => $request->input('event_name'),
            'event_type'        => $request->input('event_type'),
            'guest_arrival_time'=> $request->input('guest_arrival_time'),
            'guest_number'      => $request->input('guest_number'),
            'client_id'         => $request->input('client'),
            'finish_time'       => $request->input('finish_time'),
            'number_staff'      => $request->input('number_staff'),
            'address'           => $request->input('address'),
            'details'           => $request->input('details'),
            'uniform'           => $request->input('uniform'),
            'bar'               => $request->input('bar') == "on",
            'notes'             => $request->input('notes'),
            'start_time'        => array_values(array_filter(array_flatten($request->input('start_times')))),
            'booking_date'      => new DateTime($request->input('booking_date')),
            'event_date'        => new DateTime($request->input('event_date').$hour),
        ]);

        if($request->input('bar') == 'on')
        {   
            // Drinks creation
            EventController::outStockBarItems('beers',$request,$event->id);
            EventController::outStockBarItems('whine',$request,$event->id);
            EventController::outStockBarItems('spirits',$request,$event->id);
            EventController::outStockBarItems('cocktails',$request,$event->id);
            EventController::outStockBarItems('shots',$request,$event->id);
            
            // Supplies 
            EventController::outStockBarItems('softs',$request,$event->id);
            EventController::outStockBarItems('ingredients',$request,$event->id);

            // Equipment
            EventController::outStockBarItems('bars',$request,$event->id);
            EventController::outStockBarItems('furnitures',$request,$event->id);

            $bar_event = BarEvent::create([
                'event_id'          => $event->id,
                'private'           => $request->input('privateNumber'),
                'bar_back'          => $request->input('barBackNumber'),
                'bar_runner'        => $request->input('barRunnerNumber'),
                'classic_bartender' => $request->input('classicBartenderNumber'),
                'cocktail_bartender' => $request->input('cocktailBartenderNumber'),
                'flair_bartender'   => $request->input('flairBartenderNumber'),
                'mixologist'        => $request->input('mixologistNumber'),
                'glass_type'        => $request->input('glassChoice'),
                'ice'               => $request->input('ice') == 'on',
                'bar_number'        => $request->input('barNumber'),
                'notes'             => $request->input('barNotes'),
            ]);
        }

        //Allow the admin to add a file that all staf can access
        $file = request()->file('event_file');
        if(!empty($file))
        {
            $ext = $file->guessClientExtension();
            if($ext == '.jpeg' || $ext == '.png')
            {
                $ext = '.jpg';
            }
            $file->move('files/',$event->id.'.'.$ext);
        }

        return redirect('event/' . $event->id);
    }

    public function show($eventId)
    {
        $event = Event::find($eventId);

        $previous_event = Event::where('event_date', '>=', date('Y-m-d 05:00:00', strtotime('+11 hour')))->where('event_date','<',$event->event_date)->orderBy("event_date","DESC")->limit(1)->first();
        $next_event = Event::where('event_date', '>=', date('Y-m-d 05:00:00', strtotime('+11 hour')))->where('event_date','>',$event->event_date)->orderBy('event_date','ASC')->limit(1)->first();

        $uniform = Uniform::find($event->uniform);

        $modificationService = new Modifications($event);
        $modifications = $modificationService->getLast($event);

        if($event->bar != 0)
        {
            function getBarItems($item,$event)
            {
                return $items = OutStock::where('event_id','=',$event->id)->where('category','=',$item)->get();
            }

            $beers = getBarItems('beers',$event);
            $whines = getBarItems('whine',$event);
            $spirits = getBarItems('spirits',$event);
            $cocktails = getBarItems('cocktails',$event);
            $shots = getBarItems('shots',$event);
            $softs = getBarItems('softs',$event);
            $ingredients = getBarItems('ingredients',$event);
            $furnitures = getBarItems('furnitures',$event);
        
            return view('event.detail')->with(compact('event','previous_event','next_event','uniform','beers','whines','spirits','cocktails','shots','softs','ingredients','furnitures','modifications'));
        }
       return view('event.detail')->with(compact('event','previous_event','next_event','uniform','modifications'));
    }

    public function showModifications($eventId)
    {
        $event = Event::find($eventId);

        $modificationService = new Modifications($event);
        $modifications = $modificationService->get($event);

        return view('event.modifications')->with(compact('event','modifications'));
    }

    public function backUp($eventId, $modificationId)
    {
        $event = Event::find($eventId);
        
        $modification = Modification::find($modificationId);
        if($modification->old_value != "")
        {
            $modificationService = new Modifications($event);
            $modifications = $modificationService->backUp($event,$modificationId);
        }
        return redirect()->back();
    }

    public function edit($eventId)
    {
        $event = Event::find($eventId);

        $clients = Client::all()->sortBy('name');
        $uniforms = Uniform::all();

        $beers = EventController::fetchItems('beers',$eventId);
        $whines = EventController::fetchItems('whine',$eventId);
        $spirits = EventController::fetchItems('spirits',$eventId);
        $cocktails = EventController::fetchItems('cocktails',$eventId);
        $shots = EventController::fetchItems('shots',$eventId);
        $ingredients = EventController::fetchItems('ingredients',$eventId);
        $softs = EventController::fetchItems('softs',$eventId);
        $bars = EventController::fetchItems('bars',$eventId);
        $furnitures = EventController::fetchItems('furnitures',$eventId);

        
        return view('event.create')->with(compact('event', 'clients', 'uniforms','beers','whines','spirits','cocktails','shots','ingredients','softs','bars','furnitures'));
    }

    public function update(UpdateEventRequest $request, $eventId)
    {
        $event = Event::find($eventId);
        $uniform = Uniform::find((int)$request->input('uniform'));
        if($event->bar == 0 && $request->input('bar') == 'on' && $event->barEvent === null)
        {
            $bar_event = BarEvent::create([
                'event_id'          => $event->id,
            ]);
            $event->bar = 1;
            $event->save();
        }
        $modification = new Modifications($event);

        $start_time = array_values(array_filter(array_flatten($request->input('start_times'))));
        // Keep track of old values to know exactly what have been updated after with checkUpdates

        $event->event_name          = $request->input('event_name');
        $event->event_type          = $request->input('event_type');
        $event->client_id           = $request->input('client');
        $event->guest_arrival_time  = $request->input('guest_arrival_time');
        $event->guest_number        = $request->input('guest_number');
        $event->finish_time         = $request->input('finish_time');
        $event->number_staff        = $request->input('number_staff');
        $event->address             = $request->input('address');
        $event->details             = $request->input('details');
        $event->uniform             = $request->input('uniform');
        $event->notes               = $request->input('notes');
        $event->start_time          = array_values(array_filter(array_flatten($request->input('start_times'))));
        $event->booking_date        = new DateTime($request->input('booking_date'));
        $event->event_date          = new DateTime($request->input('event_date').$start_time[0]);

        $barEvent = $event->barEvent;
        // BAR EVENT HANDLER
        if($request->input('bar') == 'on')
        {
            // Drinks creation
            EventController::outStockBarItems('beers',$request,$event->id);
            EventController::outStockBarItems('whine',$request,$event->id);
            EventController::outStockBarItems('spirits',$request,$event->id);
            EventController::outStockBarItems('cocktails',$request,$event->id);
            EventController::outStockBarItems('shots',$request,$event->id);
            
            // Supplies 
            EventController::outStockBarItems('softs',$request,$event->id);
            EventController::outStockBarItems('ingredients',$request,$event->id);

            // Equipment
            EventController::outStockBarItems('bars',$request,$event->id);
            EventController::outStockBarItems('furnitures',$request,$event->id);
            if($event->bar && $barEvent !== null)
            {
                $barEvent->private           = $request->input('privateNumber');
                $barEvent->bar_back          = $request->input('barBackNumber');
                $barEvent->bar_runner        = $request->input('barRunnerNumber');
                $barEvent->classic_bartender = $request->input('classicBartenderNumber');
                $barEvent->cocktail_bartender = $request->input('cocktailBartenderNumber');
                $barEvent->flair_bartender   = $request->input('falirBartenderNumber');
                $barEvent->mixologist        = $request->input('mixologistNumber');
                $barEvent->glass_type        = $request->input('glassChoice');
                $barEvent->ice               = $request->input('ice') == 'on';
                $barEvent->bar_number        = $request->input('barNumber');
                $barEvent->notes             = $request->input('barNotes');
                $barEvent->save();
            }
        }

        $event->save();
        $event = Event::find($eventId);
        // Create a new Modification object in case something has changed
        $modification->checkUpdates($event);


        // FILE HANDLER
        if(file_exists('files/'.$event->id.'.jpg'))
        {
            $file = Illuminate\Support\Facades\File::get('files/'.$event->id.'.jpg');
        }
        elseif(file_exists('files/'.$event->id.'.pdf'))
        {
            $file = Illuminate\Support\Facades\File::get('files/'.$event->id.'.pdf');
        }
        else
        {
            $file = "";
        }

        $updated = $request->input('notify-all');

        // Update assignments for removed hours
        Assignment::where('event_id', $event->id)->each(function ($assignment) use ($event,$uniform,$updated,$modification,$start_time) 
        {
            if (!in_array($assignment->time, $event->start_time)) 
            {
                for ($i=0; $i < count($event->start_time); $i++) 
                { 
                    if($assignment->time == $start_time[$i])
                    {
                        $assignment->time = $event->start_time[$i];
                        $assignment->save();

                        Mail::send('emails.assignment-update', ['event' => $assignment->event, 'assignment' => $assignment, 'uniform'=>$uniform], function($message) use ($assignment) {
                            $message->to($assignment->user->profile->email)->subject('Important : Event Start Time Updated !');
                        });

                    }
                }
            }
            elseif($updated == 'on')
            {
                $subject = $modification->emailSubject();

                Mail::send('emails.event-update', ['event' => $assignment->event, 'assignment' => $assignment, 'uniform'=>$uniform], function($message) use ($assignment,$subject) {
                    $message->to($assignment->user->profile->email)->subject($subject);
                }); 
            }
        });

        Session::flash('success', 'Event successfully updated');

        return redirect()->back();
    }

    public function copy($eventId)
    {
        $event = Event::find($eventId);

        $beers = EventController::fetchItems('beers',$eventId);
        $whines = EventController::fetchItems('whine',$eventId);
        $spirits = EventController::fetchItems('spirits',$eventId);
        $cocktails = EventController::fetchItems('cocktails',$eventId);
        $shots = EventController::fetchItems('shots',$eventId);
        $ingredients = EventController::fetchItems('ingredients',$eventId);
        $softs = EventController::fetchItems('softs',$eventId);
        $bars = EventController::fetchItems('bars',$eventId);
        $furnitures = EventController::fetchItems('furnitures',$eventId);

        $event->id              = null;
        $event->start_time      = [];
        $event->finish_time     = null;
        $event->number_staff    = null;
        $event->booking_date    = null;
        $event->event_date      = null;

        $clients = Client::all()->sortBy('name');
        $uniforms = Uniform::all();


        return view('event.create')->with(compact('event', 'clients', 'uniforms','beers','whines','spirits','cocktails','shots','ingredients','softs','bars','furnitures'));
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

        $temp_user = User::where('username','smember')->first();

        $roles = Role::all();


        return view('event.staff')->with(compact('event','time', 'available', 'unavailable', 'roles', 'client', 'userMissions','temp_user'));
    }

    public function assign(Request $request, $eventId)
    {
        $event = Event::find($eventId);
        $modification = new Modifications($event);

        foreach($request->except(['_token', 'time']) as $key => $value) {
            $user = User::find($key);
            $userChecker = new UserChecker();
            $userChecker->checkUserMission($user->id);

            Assignment::create([
                'event_id'  => $event->id,
                'time'      => $request->input('time'),
                'user_id'   => $key,
                'status'    => 'pending',
                'hash'      =>  str_random(15)
            ]);

            $modification->create($event->id,'added staff: '.$user->profile->first_name." ".$user->profile->last_name,'',$user->id);
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

        $uniform = Uniform::find($event->uniform);

        if(file_exists('files/'.$event->id.'.jpg'))
        {
            $file = Illuminate\Support\Facades\File::get('files/'.$event->id.'.jpg');
        }
        elseif(file_exists('files/'.$event->id.'.pdf'))
        {
            $file = Illuminate\Support\Facades\File::get('files/'.$event->id.'.pdf');
        }
        else
        {
            $file = "";
        }

        Mail::send('emails.admin-notification', ['event' => $event, 'assignment' => $assignment, 'uniform' => $uniform, 'file'=>$file ], function($message) use ($assignment) {
            $message->to($assignment->user->profile->email)->subject("New Event Confirmation");
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

        if ($user->role_id != 1 && $user->role_id != 13) {
            $assignments = Assignment::where('user_id', $userId)->get();

            $events = $assignments->map(function($assignment) {
                return Event::find($assignment->event_id);
            })->filter(function ($event) {
                return $event->event_date >= date('Y-m-d 05:00:00', strtotime('+11 hour'));
            })->sortBy('event_date');

            $availabilities = Availability::where('user_id', '=', Auth::user()->id)
            ->where('date', '>=', date('Y-m-d'))
            ->get()
            ->sortBy('date');

            if(count($availabilities) == 0)
            {
                Session::flash('notAvailable','Please update your availabilities to be sure to have a job.');
                return view('availability.index')->with(compact('availabilities'));
            }

            return view('event.index')->with(compact('events', 'user', 'assignments','availabilities'));
        }

        // Shows all the events 
        if($user->role_id == 1)
        {
            $events = Event::where('event_date', '>=', date('Y-m-d 05:00:00', strtotime('+11 hour')))->get()->sortBy('event_date');
        }

        // Shows only the events with a bar event linked
        if($user->role_id == 13)
        {
            $events = Event::where('event_date', '>=', date('Y-m-d 05:00:00', strtotime('+11 hour')))->where('bar','=',1)->get()->sortBy('event_date');
        }

        return view('event.index')->with(compact('events'));
    }

    public function confirmAssistance($hash)
    {
        $assignment = Assignment::where('hash', $hash)->get()->first();

        if (!$assignment) {
            return redirect('/');
        }

        return redirect('event/' . $assignment->event_id);
    }

    public function notifyClient(Request $request, $eventId)
    {
        $event = Event::find($eventId);
        $admin = User::find($event->admin_id);
        $email = $request->input('client-email');

        if(!empty($email) && $email != "to-all")
        {
            Mail::send('emails.notify-client', ['event' => $event, 'client' => $event->client, 'assignments' => $event->assignments, 'admin'=>$admin], function($message) use ($email) {
                $message->to($email)->subject('Event Confirmation');
            });
        } 
        elseif(!empty($email) && $email == "to-all")
        {
            if(!empty($event->client->third_email))
            {
                Mail::send('emails.notify-client', ['event' => $event, 'client' => $event->client, 'assignments' => $event->assignments, 'admin'=>$admin], function($message) use ($event) {
                    $message->to($event->client->email)->cc($event->client->second_email)->cc($event->client->third_email)->subject('Event Confirmation');
                });
            }
            elseif(!empty($event->client->second_email))
            {
                Mail::send('emails.notify-client', ['event' => $event, 'client' => $event->client, 'assignments' => $event->assignments, 'admin'=>$admin], function($message) use ($event) {
                    $message->to($event->client->email)->cc($event->client->second_email)->subject('Event Confirmation');
                });
            }
        }
        else
        {
            Mail::send('emails.notify-client', ['event' => $event, 'client' => $event->client, 'assignments' => $event->assignments, 'admin'=>$admin], function($message) use ($event) {
                $message->to($event->client->email)->subject('Event Confirmation');
            });
        }

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

        // Empty the OutStock database to put back the item number in stock after the admin has added the timesheet to the event, which means that the event is over and the glasses should be given back. If the stock item doesn't exist anymore (=0 when event was created), a new object is created.
        $items = OutStock::where('event_id','=',$eventId)->where('category','=','glass')->get();
        foreach ($items as $item) 
        {
            $stock = Stock::where('name','=',$item->name)->first();
            if(!empty($stock))
            {
                $stock->quantity = $stock->quantity + $item->quantity;
                $stock->save();
            }
            else
            {
                $stock = Stock::create([
                    'name'      =>  $item->name,
                    'category'  =>  $item->category,
                    'quantity'  =>  $item->quantity,
                ]);
            }
            $item->delete();
        }

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
            $break = $assignment->id.'-break';
            $hours = $assignment->id.'-hours';

            $assignment->start_time = $request->$start_time;
            $assignment->hours = $request->$hours;
            $assignment->break = str_replace(':','.',$request->$break);
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
        if(Auth::user())
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
        else
        {
            return redirect('/');
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

    public function monthReport($year, $month, $order)
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
                    ->select('clients.name',DB::raw('count(DISTINCT events.id) as events_number'), DB::raw('count(assignments.user_id) as staff_number'), DB::raw('count(DISTINCT DATE_FORMAT(events.event_date,\'%Y-%m-%d\')) as days_worked'))
                    ->join('assignments','assignments.event_id', '=', 'events.id')
                    ->join('clients','clients.id','=','events.client_id')
                    ->where('events.event_date','>=',$date)
                    ->where('events.event_date','<',$next_report_month)
                    ->groupBy('clients.name')
                    ->orderBy($order, 'DESC')
                    ->get();  

        $total_events = DB::table('events')
                    ->select(DB::raw('count(DISTINCT events.id) as events_number'), DB::raw('count(assignments.user_id) as staff_number'), DB::raw('count(DISTINCT DATE_FORMAT(events.event_date,\'%Y-%m-%d\')) as days_worked'))
                    ->join('assignments','assignments.event_id', '=', 'events.id')
                    ->join('clients','clients.id','=','events.client_id')
                    ->where('events.event_date','>=',$date)
                    ->where('events.event_date','<',$next_report_month)
                    ->get();   

        return view('reports.month-report')->with(compact('events','total_events','written_date','year','month', 'last_month', 'next_month', 'last_year', 'next_year', 'days_numbers_in_month', 'order'));
    }

    /* 
    *
    If while creating/updating an event, a category is choosen, the app checks if the item is registered in the stock database. 
    Then, the quantity asked is reduced from the stock. 
    If the user doesn't ask too much items, the quantity asked is stored to OutStock, which contains all the items used for the event. 
    If the amount of items in stock = 0, the object is destroyed for avoiding to be chosen again by the user in another event creation.  
    *
    */
    public function outStockItems($category,$request,$eventId)
    {
        $stockItems = Stock::where('category','=',$category)->get();
        if(!empty($stockItems))
        {
            foreach ($stockItems as $stockItem) 
            {
                // Fetch the item in stocks and substract the quantity wanted
                $newItem = Stock::find($stockItem->id);

                $newItem->quantity = $newItem->quantity - $request->input(str_replace(' ','_',$stockItem->name));

                if($newItem->quantity < 0)
                {
                    Session::flash('danger', 'Too Many '.$newItem->name.' asked !');
                    return redirect()->back();
                }
                elseif($newItem->quantity == 0)
                {
                    // Update or create a new item in the event stock
                    $outStock = OutStock::where('event_id','=',$eventId)->where('name',"=",$newItem->name)->first();
                    if(!empty($outStock))
                    {
                        $outStock->quantity = $outStock->quantity + $request->input(str_replace(' ','_',$stockItem->name));
                        $outStock->save();
                    }
                    else
                    {
                        $OutStock = OutStock::create([
                            'event_id'      =>  $eventId,
                            'name'          =>  $newItem->name,
                            'category'      =>   $newItem->category,
                            'quantity'      =>  $newItem->quantity,
                        ]);
                    }
                    // Delete the instock item for avoiding using it right after in another event
                    $newItem->delete();
                } 
                else 
                {
                    // Update or create a new item in the event stock
                    $outStock = OutStock::where('event_id','=',$eventId)->where('name',"=",$newItem->name)->first();
                    if(!empty($outStock))
                    {
                        $outStock->quantity = $outStock->quantity + $request->input(str_replace(' ','_',$stockItem->name));
                        $outStock->save();
                    }
                    else
                    {
                        $OutStock = OutStock::create([
                            'event_id'      =>  $eventId,
                            'name'          =>  $newItem->name,
                            'category'      =>   $newItem->category,
                            'quantity'      =>  $newItem->quantity,
                        ]);
                    }
                    //Update the new quantity of items in stock
                    $newItem->save();
                }
            }
        }
    }

    public function outStockBarItems($item,$request,$eventId)
    {
        if($request->input($item) == 'on')
        {
            for($i=0; $i < $request->input($item.'counter'); $i++)
            {
                if(!empty($request->input($item.'Name'.$i)))
                {
                    $outStock = OutStock::where('event_id','=',$eventId)->where('name','=',$request->input($item.'Name'.$i))->first();
                    
                    if(empty($outStock))
                    {
                        OutStock::create([
                            'event_id'      =>  $eventId,
                            'name'          =>  $request->input($item.'Name'.$i),
                            'category'      =>  $item,
                            'quantity'      =>  $request->input($item.'Number'.$i),
                            'brand'         =>  $request->input($item.'List'.$i),
                        ]);
                    }
                    else
                    {
                        $outStock->name = $request->input($item.'Name'.$i);
                        $outStock->category = $item;
                        $outStock->quantity = $request->input($item.'Number'.$i);
                        $outStock->brand = $request->input($item.'List'.$i);
                        $outStock->save();
                    }
                }
            }
        }
    }

    public function fetchItems($itemName, $eventId)
    {
        $item = OutStock::where('event_id','=',$eventId)->where('category','=',$itemName)->get();
        return $item;
    }

    public function createIcs($eventId)
    {
        $event = Event::find($eventId);
        $startDate = date_format(date_create($event->event_date),'U');
        $date = date_format(date_create($event->event_date),'Y-m-d');
        $finishTime = date_format(date_create($event->finish_time),'H:i:s');
        $endDate = date_format(date_create($date."".$finish_time),'U');

        $ics = new ICS('//Company//Product//EN');

        $ics->startDate($startDate)
            ->endDate($endDate)
            ->address($event->address." ".$event->details)
            ->summary($event->event_name)
            ->uri(url('/event/'.$event->id))
            ->description("Notes : ".$event->notes." Uniform : ".$event->uniform()->set_name);

        return $ics;

    }
}