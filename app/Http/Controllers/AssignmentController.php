<?php namespace App\Http\Controllers;

use App\Assignment;
use App\Event;
use App\User;
use App\Uniform;
use Ical\Ical;
use App\Services\FinancialReportCalculation;
use App\Services\weekReport;
use App\Services\Modifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Filesystem\Filesystem;


class AssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function destroy($assignmentId)
    {
        $assignment = Assignment::find($assignmentId);

        $assignment->delete();

        return redirect()->back();
    }

    public function confirm($eventId)
    {
        $event = Event::find($eventId);
        $modificationService = new Modifications($event);
        $modificationService->confirmCheck();   

        Assignment::where(['event_id' => $eventId, 'user_id' => Auth::user()->id])->each(function ($assignment) {
            $assignment->status = 'confirmed';

            $assignment->push();
        });

        return redirect()->back();
    }

    public function forceConfirm(Request $request, $eventId)
    {
        Assignment::where(['event_id' => $eventId, 'user_id' => $request->input('user_id')])->each(function ($assignment) {
            $assignment->status = 'confirmed';

            $assignment->push();
        });

        return redirect()->back();
    }
    public function notifyAll($eventId)
    {
        $event = Event::find($eventId);
        $assignments = $event->assignments;
        foreach($assignments as $assignment)
        {
            if(!empty($assignment->event->admin_id))
            {
                $admin = User::find($assignment->event->admin_id)->profile;
            }
            else
            {
                $admin = "";
            }

            $uniform = Uniform::find($assignment->event->uniform);

            if(file_exists('files/'.$assignment->event->id.'.jpg'))
            {
                $file = Illuminate\Support\Facades\File::get('files/'.$assignment->event->id.'.jpg');

                Mail::send('emails.event-confirmation', ['event' => $assignment->event, 'assignment' => $assignment, 'admin' => $admin, 'uniform'=>$uniform], function($message) use ($assignment) {
                $message->to($assignment->user->profile->email)->subject('Event Confirmation')->attach($file);
                });
            }
            elseif(file_exists('files/'.$assignment->event->id.'.pdf'))
            {
                $file = Illuminate\Support\Facades\File::get('files/'.$assignment->event->id.'.pdf');

                Mail::send('emails.event-confirmation', ['event' => $assignment->event, 'assignment' => $assignment, 'admin' => $admin, 'uniform'=>$uniform], function($message) use ($assignment, $file) {
                $message->to($assignment->user->profile->email)->subject('Event Confirmation')->attach($file);
                });
            }
            else
            {
                $file = "";
                Mail::send('emails.event-confirmation', ['event' => $assignment->event, 'assignment' => $assignment, 'admin' => $admin, 'uniform'=>$uniform], function($message) use ($assignment) {
                $message->to($assignment->user->profile->email)->subject('Event Confirmation');
                });
            }


            $assignment->notification = true;
            $assignment->save();
        }

        Session::flash('success', 'The notification was sent successfully');

        return redirect()->back();
    }

    public function notify($assignmentId)
    {
        $assignment = Assignment::find($assignmentId);
        if(!empty($assignment->event->admin_id))
        {
            $admin = User::find($assignment->event->admin_id)->profile;
        }
        else
        {
            $admin = "";
        }

        $uniform = Uniform::find($assignment->event->uniform);

        $ics = AssignmentController::createIcs($assignment->event->id);


        if(file_exists('files/'.$assignment->event->id.'.jpg'))
        {
            $file = Illuminate\Support\Facades\File::get('files/'.$assignment->event->id.'.jpg');

            Mail::send('emails.event-confirmation', ['event' => $assignment->event, 'assignment' => $assignment, 'admin' => $admin, 'uniform'=>$uniform], function($message) use ($assignment,$file,$ics) {
            $message->to($assignment->user->profile->email)->subject('Event Confirmation')->attach($file); //Attach ICS
            });
        }
        elseif(file_exists('files/'.$assignment->event->id.'.pdf'))
        {
            $file = Illuminate\Support\Facades\File::get('files/'.$assignment->event->id.'.pdf');

            Mail::send('emails.event-confirmation', ['event' => $assignment->event, 'assignment' => $assignment, 'admin' => $admin, 'uniform'=>$uniform], function($message) use ($assignment, $file,$ics) {
            $message->to($assignment->user->profile->email)->subject('Event Confirmation')->attach($file); //Attach ICS
            });
        }
        else
        {
            $file = "";
            Mail::send('emails.event-confirmation', ['event' => $assignment->event, 'assignment' => $assignment, 'admin' => $admin, 'uniform'=>$uniform], function($message) use ($assignment,$ics) {
            $message->to($assignment->user->profile->email)->subject('Event Confirmation');
            }); //Attach ICS
        }


        $assignment->notification = true;
        $assignment->save();

        Session::flash('success', 'The notification was sent successfully');

        return redirect()->back();
    }

    public function weekReport($day = null)
    {   
        if($day == null){
            $day = date('w');
        }
        $week_start = date('Y-m-d', strtotime('-'.($day-1).' days'));
        $week_end = date('Y-m-d', strtotime($week_start.'-7 days'));
        $next = $day - 7;
        $previous = $day + 7;
        $assignments = DB::table('assignments')
                        ->select('events.id as event_id','events.event_date','events.event_name','users.id as user_id','profiles.last_name','profiles.first_name','users.level', 'assignments.start_time', 'assignments.hours', 'assignments.break')
                        ->join('events','events.id','=','assignments.event_id')
                        ->join('users','users.id', '=', 'assignments.user_id')
                        ->join('profiles','profiles.user_id','=','users.id')
                        ->where('events.event_date', '>=', $week_end)
                        ->where('events.event_date', '<', $week_start)
                        ->orderBy('events.event_date','ASC')
                        ->get();
        $hours = [];
        $cost = []; 
        $public_holiday = [];               
        foreach ($assignments as $assignment) 
        {
            $weekReport = new weekReport($assignment->event_date,$assignment->start_time,$assignment->hours,$assignment->break);
            if($weekReport->is_public_holiday($assignment->event_date))
            {
                $public_holiday[$assignment->event_id] = "PH";
            }
            else
            {
                $public_holiday[$assignment->event_id] = ""; 
            }
            $hours[$assignment->event_id.'-'.$assignment->user_id] = $weekReport->get_hours();
        }
        return view('reports.week-report')->with(compact('assignments','day','previous','next','week_start','week_end','hours','public_holiday'));
    }

    public function createIcs($eventId)
    {
        $event = Event::find($eventId);
        $startDate = date_create_from_format('Y-m-d H:i:s', $event->event_date);
        $date = date_format(date_create($event->event_date),'Y-m-d');
        $finishTime = date_format(date_create($event->finish_time),'H:i:s');
        $endDate = date_create_from_format('Y-m-d H:i:s', $date."".$finishTime);

        try {

        $ical = (new Ical())->setAddress($event->address." ".$event->details)
                ->setDateStart($startDate)
                ->setDateEnd($endDate)
                ->setDescription("Notes : ".$event->notes)
                ->setSummary($event->event_name)
                ->setFilename(uniqid());

        $ical->getICAL();          

        return $ical;

        } catch (\Exception $exc) {
            echo $exc->getMessage();

        }
    }
}