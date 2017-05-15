<?php

namespace App\Http\Controllers;

use App\Event;
use App\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

class GoogleCalendarController extends Controller
{

    protected $client;
    public $hash;

    public function __construct()
    {
        $client = new Google_Client();
        $client->setAuthConfig('client_secret.json');
        $client->addScope(Google_Service_Calendar::CALENDAR);

        $this->client = $client;
    }

    public function oauth()
    {
        session_start();

        $this->client->setRedirectUri(action('GoogleCalendarController@oauth'));

        if(!isset($_GET['code']))
        {
            $auth_url = $this->client->createAuthUrl();
            return redirect($auth_url);
        }
        else
        {
            $this->client->authenticate($_GET['code']);
            $_SESSION['access_token'] = $this->client->getAccessToken();
            return redirect(url('/create_calendar_event/'.$this->hash));
        }
    }

    public function create_calendar_event($hash)
    {
        session_start();

        $this->hash = $hash;

        $assignment = Assignment::where('hash', $hash)->get()->first();
        if (!$assignment) {
            return redirect('/');
        }

        Auth::loginUsingId($assignment->user_id);
        
        //Set Dates for the calendar event
        $greenwich = "+11:00";
        $date = date_format(date_create($assignment->event->event_date),'Y-m-d');
        $start_date = date_format(date_create($assignment->event->event_date), "Y-m-d H:i:s".$greenwich);

        $hour_date = date_format(date_create($assignment->event->finish_time), "H:i:s");
        $end_date = $date.$hour_date.$greenwich;

        $iso_start_date = date_format(date_create($start_date),'c');
        $iso_end_date = date_format(date_create($end_date), "c");
        
        
        if(isset($_SESSION['access_token']))
        {
            $this->client->setAccessToken($_SESSION['access_token']);

            $service = new Google_Service_Calendar($this->client);
            $calendar_event = new Google_Service_Calendar_Event(array(
              'summary' => $assignment->event->event_name,
              'location' => $assignment->event->address,
              'description' => $assignment->event->details,
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
            return redirect(url('/event/'.$assignment->event->id));
        }
        else
        {
            return redirect(url('/oauth'));
        }
        
    }

    public function icsFile()
    {
        
    }
}
