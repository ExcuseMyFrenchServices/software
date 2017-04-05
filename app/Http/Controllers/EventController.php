<?php namespace App\Http\Controllers;

use App\Assignment;
use App\Client;
use App\Event;
use App\Uniform;
use App\Stock;
use App\OutStock;
use App\BarEvent;
use App\Http\Requests\Event\CreateEventRequest;
use App\Http\Requests\Event\TimesheetRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Role;
use App\Services\AvailableUsers;
use App\Services\UsersMissions;
use App\Services\stockItems;
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
        $soft_drinks = Stock::where('category',"=","soft drink")->get();
        $alcohols = Stock::where('category',"=","alcohol")->get();
        $accessories = Stock::where('category',"=","accessory")->get();
        $glasses = Stock::where('category',"=","glass")->get();

        return view('event.create')->with(compact('clients', 'roles', 'uniforms', 'stocks','soft_drinks','alcohols','accessories','glasses'));
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
            'details'       => $request->input('details'),
            'uniform'       => $request->input('uniform'),
            'bar'           => $request->input('bar') == "on",
            'notes'         => $request->input('notes'),
            'start_time'    => array_values(array_filter(array_flatten($request->input('start_times')))),
            'booking_date'  => new DateTime($request->input('booking_date')),
            'event_date'    => new DateTime($request->input('event_date').$start_time[0]),
        ]);

        if($request->input('bar') == 'on')
        {
            $bar_event = BarEvent::create([
                'event_id'  => $event->id,
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
        $uniform = Uniform::find($event->uniform);
        $glasses = OutStock::where('event_id','=',$eventId)->where('category','=','glass')->get();
        $softs = OutStock::where('event_id','=',$eventId)->where('category','=','soft drink')->get();
        $accessories = OutStock::where('event_id','=',$eventId)->where('category','=','accessory')->get();
        $alcohols = OutStock::where('event_id','=',$eventId)->where('category','=','alcohol')->get();
        return view('event.detail')->with(compact('event','uniform','glasses','softs','alcohols','accessories'));
    }

    public function edit($eventId)
    {
        $event = Event::find($eventId);

        $clients = Client::all()->sortBy('name');
        $uniforms = Uniform::all();

        //Display all items that are registered in the stock section
        $soft_drinks = Stock::where('category',"=","soft drink")->get();
        $alcohols = Stock::where('category',"=","alcohol")->get();
        $accessories = Stock::where('category',"=","accessory")->get();
        $glasses = Stock::where('category',"=","glass")->get();

        //Shows if the user already booked some items for the event
        $outStockGlasses = OutStock::where('event_id','=',$eventId)->where('category','=','glass')->get();
        $outStockSofts = OutStock::where('event_id','=',$eventId)->where('category','=','soft drink')->get();
        $outStockAccessories = OutStock::where('event_id','=',$eventId)->where('category','=','accessory')->get();
        $outStockAlcohols = OutStock::where('event_id','=',$eventId)->where('category','=','alcohol')->get();
        
        return view('event.create')->with(compact('event', 'clients', 'uniforms','glasses','alcohols','accessories','soft_drinks','outStockGlasses','outStockSofts','outStockAlcohols','outStockAccessories'));
    }

    public function update(UpdateEventRequest $request, $eventId)
    {
        $event = Event::find($eventId);
        $uniform = Uniform::find((int)$request->input('uniform'));

        // Check if an Bar Event object exists, needs to be created or deleted
        if($request->input('bar') === 'on')
        {
            $bar_event = $event->barEvent;
            if(empty($bar_empty))
            {
                $bar_event = BarEvent::create([
                    'event_id'  => $event->id,
                ]);
            }
        }
        else
        {
            $bar_event = $event->barEvent;
            if(!empty($bar_event))
            {
                $bar_event->delete();
            }
        }

        $start_time = array_values(array_filter(array_flatten($request->input('start_times'))));
        // Keep track of old values to know exactly what have been updated
        $old_start_time = $event->start_time;
        $old_finish_time = $event->finish_time;
        $old_address = $event->address;
        $old_details = $event->details;
        $old_uniform = $event->uniform;
        $old_notes = $event->notes;

        $event->event_name      = $request->input('event_name');
        $event->client_id       = $request->input('client');
        $event->finish_time     = $request->input('finish_time');
        $event->number_staff    = $request->input('number_staff');
        $event->address         = $request->input('address');
        $event->details         = $request->input('details');
        $event->uniform         = $request->input('uniform');
        $event->notes           = $request->input('notes');
        $event->start_time      = array_values(array_filter(array_flatten($request->input('start_times'))));
        $event->booking_date    = new DateTime($request->input('booking_date'));
        $event->event_date      = new DateTime($request->input('event_date').$start_time[0]);

        $event->save();

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
        Assignment::where('event_id', $event->id)->each(function ($assignment) use ($event,$old_start_time,$uniform,$old_finish_time,$old_address,$old_details,$old_notes,$old_uniform, $updated) 
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

                        Mail::send('emails.assignment-update', ['event' => $assignment->event, 'assignment' => $assignment, 'uniform'=>$uniform], function($message) use ($assignment) {
                            $message->to($assignment->user->profile->email)->subject('Important : Event Start Time Updated !');
                        });

                    }
                }
            }
            elseif($updated == 'on')
            {
                $subject = 'Important : Event Updated';
                // Change the email subject depending on what have been updated
                if($old_finish_time != $event->finish_time)
                {
                    $subject = 'Important : Event End Time updated';
                }
                elseif ($old_address != $event->address) 
                {
                    $subject = "Important : Event's Address Changed";
                }
                elseif($old_uniform != $event->uniform)
                {
                    $subject = "Important : Event's Uniform Changed";
                }
                elseif($old_details != $event->details)
                {
                    $subject = "Important : Event's Details Changed";
                }

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

        $glasses = OutStock::where('event_id','=',$eventId)->where('category','=','glass')->get();
        $softs = OutStock::where('event_id','=',$eventId)->where('category','=','soft drink')->get();
        $accessories = OutStock::where('event_id','=',$eventId)->where('category','=','accessory')->get();
        $alcohols = OutStock::where('event_id','=',$eventId)->where('category','=','alcohol')->get();

        $event->id              = null;
        $event->start_time      = [];
        $event->finish_time     = null;
        $event->number_staff    = null;
        $event->booking_date    = null;
        $event->event_date      = null;

        $clients = Client::all()->sortBy('name');
        $uniforms = Uniform::all();

        return view('event.create')->with(compact('event', 'clients', 'uniforms','glasses','softs','accessories','alcohols'));
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
            $hours = $assignment->id.'-hours';
            $break = $assignment->id.'-break';

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
}