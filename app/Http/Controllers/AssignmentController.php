<?php namespace App\Http\Controllers;

use App\Assignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

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

    public function notify($assignmentId)
    {
        $assignment = Assignment::find($assignmentId);

        Mail::send('emails.event-confirmation', ['event' => $assignment->event, 'assignment' => $assignment], function($message) use ($assignment) {
            $message->to($assignment->user->profile->email)->subject('Event Confirmation');
        });

        $assignment->notification = true;
        $assignment->save();

        Session::flash('success', 'The notification was sent successfully');

        return redirect()->back();
    }
}