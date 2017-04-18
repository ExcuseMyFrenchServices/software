<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Http\Requests;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

class GoogleCalendarController extends Controller
{

    protected $client;

    public function __construct()
    {
        $client = new Google_Client();
        $client->setAuthConfig('client_secret.json');
        $client->addScope(Google_Service_Calendar::CALENDAR);

        $this->client = $client;
    }

    public function oauth($event_id)
    {
        session_start();

        $this->client->setRedirectUri(action('GoogleCalendarController@oauth', ['event_id'=>$event_id]));

        if(!isset($_GET['code']))
        {
            $auth_url = $this->client->createAuthUrl();
            return redirect($auth_url);
        }
        else
        {
            $this->client->authenticate($_GET['code']);
            $_SESSION['access_token'] = $this->client->getAccessToken();
            return redirect(url('/create_calendar_event/'.$event_id));
        }
    }

    public function create_calendar_event($hash)
    {
        session_start();

        $assignment = Assignment::where('hash', $hash)->get()->first();
        if (!$assignment) {
            return redirect('/');
        }

        Auth::loginUsingId($assignment->user_id);

        $event = $assignment->event;

        $date = date_format(date_create($event->event_date),'Y-m-d');
        $greenwich = "+11:00";
        $end_date = date_format(date_create($event->finish_time), $date."H:i:s".$greenwich);
        $start_date = date_format(date_create($event->event_date), "Y-m-d H:i:s".$greenwich);

        $iso_start_date = date_format(date_create($start_date),'c');
        $iso_end_date = date_format(date_create($end_date), "c");

        if(isset($_SESSION['access_token']))
        {
            $this->client->setAccessToken($_SESSION['access_token']);

            $service = new Google_Service_Calendar($this->client);
            $calendar_event = new Google_Service_Calendar_Event(array(
              'summary' => $event->event_name,
              'location' => $event->address,
              'description' => $event->details,
              'start' => array(
                'dateTime' => $iso_start_date,
                'timeZone' => 'Australia/Sydney',
              ),
              'end' => array(
                'dateTime' => $iso_end_date,
                'timeZone' => 'Australia/Sydney',
              ),
            ));

            $calendarId = 'primary';
            $calendar_event = $service->events->insert($calendarId, $calendar_event);

            Session::flash('calendar','Event successfully registered in your calendar');
            return redirect(url('/event/'.$event->id));
        }
        else
        {
            return redirect(url('/oauth/'.$event->id));
        }
    }
}
