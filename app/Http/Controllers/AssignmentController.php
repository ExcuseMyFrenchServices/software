<?php namespace App\Http\Controllers;

use App\Assignment;
use App\Event;
use App\User;
use App\Uniform;
use App\Services\FinancialReportCalculation;
use App\Services\weekReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

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

        Session::flash('success', 'The notification was sent successfully');

        return redirect()->back();
    }

    public function weekReport()
    {
        $day = date('w');
        $week_start = date('Y-m-d', strtotime('-'.($day-1).' days'));
        $week_end = date('Y-m-d', strtotime($week_start.'-7 days'));

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

        return view('reports.week-report')->with(compact('assignments','week_start','week_end','hours','public_holiday'));
    }
}