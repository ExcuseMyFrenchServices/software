<?php
namespace App\Services;

use App\Assignment;
use App\Availability;
use App\Event;
use App\User;
use DateTime;
use App\Services\UsersMissions;

class AvailableUsers {

    /**
     * Gets a collection of users that are available for the given event
     *
     * This means users available on the hours and day of the week of the event and that don't have another event
     * that overlaps with this one
     */
    public function get($event, $time)
    {
        $date = new DateTime($event->event_date);

        $event_hours = $this->getEventHours($event, $time);
        $availabilities = Availability::where('date', $date)->get();

        $available_users = [];

        foreach ($availabilities as $availability) {
            $intersection = array_intersect($event_hours, $availability->times);
            if (sizeof($event_hours) == sizeof($intersection)) {
                $available = true;
                $assignments = Assignment::where('user_id', '=', $availability->user_id)->get();

                foreach ($assignments as $assignment) {
                    if ($this->eventsOverlap(Event::find($assignment->event_id), $event)) {
                        $available = false;
                        break;
                    }
                }

                if ($available) {
                    $available_users[] = $availability->user_id;
                }
            }
        }

        return $availableUsers = User::whereIn('id', $available_users)->get();
    }

    /**
     * Detect if two events are on the same date (day, month & year) and share at least one hour of the day
     *
     * [12 13 14 15] and [14 15 16 17 18 19] on the same date clash
     * [6 7 8 9] and [10 11 12 13] on the same date don't clash
     *
     * [6 7 8 9] and [6 7 8 9] on different dates don't clash
     *
     * @param $eventA
     * @param $eventB
     * @return bool
     */
    public function eventsOverlap($eventA, $eventB)
    {
        if ($eventA->event_date != $eventB->event_date) {
            return false;
        }

        $hoursA = $this->getEventHours($eventA, $eventA->start_time[0]);
        $hoursB = $this->getEventHours($eventB, $eventB->start_time[0]);

        return sizeof(array_intersect($hoursA, $hoursB)) > 0;
    }

    /**
     * Return formatted array with the hours of the event. If finish time is not available, 4 hours duration is assumed.
     * If the event finishes after midnight, this is not reflected in the resulting array. The highest possible
     * hour in the array is 23
     *
     * Examples:
     *
     * from 3pm to 7pm -> [15 16 17 18]
     * from 10pm to 2am -> [22 23]     Note that only hours in the start day are included
     *
     *
     * Refer to afterMidnightEvent to check if an event finishes after midnight
     *
     * @param $event
     * @return array
     */
    public function getEventHours($event, $time)
    {
        $start = new DateTime($time);
        $start_time = intval($start->format('H'));

        if ($event->finish_time) {
            $finish = new DateTime($event->finish_time);
            $hour = intval($finish->format('H'));
            $minutes = $finish->format('i');

            if ($minutes != '00') {
                $finish_time = $hour + 1;
            } else {
                $finish_time = $hour;
            }
        } else {
            $finish_time = ($start_time + 4) % 24;
        }

        if ($finish_time < $start_time) {
            $finish_time = 24;
        }

        $event_hours = [];

        for($i = $start_time; $i < $finish_time; $i++) {
            $event_hours[] = $i;
        }

        return $event_hours;
    }
}