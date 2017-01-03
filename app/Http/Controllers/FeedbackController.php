<?php namespace App\Http\Controllers;

use App\Event;
use App\Feedback;
use App\Http\Requests\Feedback\SubmitFeedbackRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class FeedbackController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', [
            'only' => ['request', 'show']
        ]);

        $this->middleware('admin', [
            'only' => ['request', 'show']
        ]);
    }

    public function request($eventId)
    {
        $event = Event::find($eventId);

        $feedback = Feedback::where('event_id', '=', $eventId)->first();

        // First time it's requested
        if (!$feedback) {
            $feedback = Feedback::create([
                'event_id'  => $event->id,
                'client_id' => $event->client->id,
                'hash'      => str_random(15)
            ]);
        }

        Mail::send('emails.feedback', ['event' => $event, 'hash' => $feedback->hash], function($message) use ($event) {
            $message->to($event->client->email)->subject('Event Feedback');
        });

        Session::flash('success', 'Feedback request sent.');

        return redirect()->back();
    }

    public function form($hash)
    {
        $feedback = Feedback::where('hash', '=', $hash)->first();

        if (!$feedback) {
            return redirect('/');
        }

        return view('client.feedback')->with(['event' => $feedback->event, 'hash' => $hash]);
    }

    public function submit(SubmitFeedbackRequest $request, $hash)
    {
        $feedback = Feedback::where('hash', '=', $hash)->first();

        if (!$feedback) {
            return redirect('/');
        }

        $feedback->rating = $request->input('rating');
        $feedback->comment = $request->input('comment');
        $feedback->hash = null;

        $feedback->save();

        return view('client.feedback-thankyou');
    }

    public function show($eventId)
    {
        $feedback = Feedback::where('event_id', '=',$eventId)->first();

        return view('event.feedback-view')->with(compact('feedback'));
    }
}
