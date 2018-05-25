<?php namespace App\Http\Controllers;

use App\Assignment;
use App\Event;
use App\User;
use App\Uniform;
use Ical\Ical;
use App\Services\FinancialReportCalculation;
use App\Services\weekReport;
use App\Services\Modifications;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
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
                $file = File::get('files/'.$assignment->event->id.'.jpg');

                Mail::send('emails.event-confirmation', ['event' => $assignment->event, 'assignment' => $assignment, 'admin' => $admin, 'uniform'=>$uniform], function($message) use ($assignment) {
                $message->to($assignment->user->profile->email)->subject('Event Confirmation')->attach($file);
                });
            }
            elseif(file_exists('files/'.$assignment->event->id.'.pdf'))
            {
                $file = File::get('files/'.$assignment->event->id.'.pdf');

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
            $file = File::get('files/'.$assignment->event->id.'.jpg');

            Mail::send('emails.event-confirmation', ['event' => $assignment->event, 'assignment' => $assignment, 'admin' => $admin, 'uniform'=>$uniform], function($message) use ($assignment,$file,$ics) {
            $message->to($assignment->user->profile->email)->subject('Event Confirmation')->attach($file); //Attach ICS
            });
        }
        elseif(file_exists('files/'.$assignment->event->id.'.pdf'))
        {
            $file = File::get('files/'.$assignment->event->id.'.pdf');

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

    public function weekReport($week = null)
    {   

        $reportService = new ReportService($week);
        $weekReports = $reportService->getWeekReport();
        $start = date('d/m/Y', strtotime($reportService->week_start));
        $end = date('d/m/Y', strtotime($reportService->week_end));
        $next = $reportService->next_week;
        $last = $reportService->last_week;
        $week = $reportService->date;
        
        return view('reports.week-report')->with(compact('weekReports','start','end','week','next','last'));
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