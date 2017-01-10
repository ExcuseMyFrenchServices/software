<?php
namespace App\Services;

use App\Assignment;
use App\Availability;
use App\Event;
use App\User;
use DateTime;
use App\Services\UsersMissions;

class FinancialReportCalculation {

    /**
     * Make financial calculations for reporting such as the cost of a employee for a specific event or the number of hour he made. Also, the class will help determining which cost is it for a specific hour of the week.
     *
     * The hours are extracted from the events hours and date
    */

    public function staffCost($start_time, $finish_time, $event_date, $user_level)
    {
        $event_date = date('d/m/Y',strtotime($event_date));
        $year = date('Y',strtotime($event_date));
        $hours = $this->hourSpent($start_time, $finish_time, $event_date);

        $event_day = date('l',strtotime($event_date));

        if($event_day == 'Saturday')
        {
            switch ($user_level) 
            {
                case 1:
                    $wage = 27.50;
                    break;
                case 2:
                    $wage = 28.50;
                    break;
                case 3:
                    $wage = 29.50;
                    break;
                case 4:
                    $wage = 33;               
                    break;
            }
            return $staffCost = $hours['low_cost_hours'] * $wage;
        }
        elseif($event_day == 'Sunday')
        {
            switch ($user_level) 
            {
                case 1:
                    $wage = 32;
                    break;
                case 2:
                    $wage = 33.50;
                    break;
                case 3:
                    $wage = 34.50;
                    break;
                case 4:
                    $wage = 38.50;               
                    break;
            }
            return $staffCost = $hours['low_cost_hours'] * $wage;
        }
        else
        {
            $publicHolidays = $this->publicHolidays($year);
            $publicHolidaysNumber = count($publicHolidays);
            for ($i=0; $i < $publicHolidaysNumber; $i++) 
            { 
                if($event_date == $publicHolidays[$i])
                {
                    switch ($user_level) 
                    {
                        case 1:
                            $wage = 50.50;
                            break;
                        case 2:
                            $wage = 52;
                            break;
                        case 3:
                            $wage = 54;
                            break;
                        case 4:
                            $wage = 60.50;               
                            break;
                    }
                    return $staffCost = $hours['low_cost_hours'] * $wage;
                }
                else
                {
                    switch ($user_level) 
                    {
                        case 1:
                            $low_wage = 23;
                            $high_wage = 25;
                            $very_high_wage = 26;
                            break;
                        case 2:
                            $low_wage = 24;
                            $high_wage = 26;
                            $very_high_wage = 27;
                            break;
                        case 3:
                            $low_wage = 25;
                            $high_wage = 27;
                            $very_high_wage = 28;
                            break;
                        case 4:
                            $low_wage = 27.5;
                            $high_wage = 29.5;
                            $very_high_wage = 30.50;               
                            break;
                    }
                    return $staffCost = $hours['low_cost_hours'] * $low_wage + $hours['high_cost_hours']*$high_wage + $hours['very_high_hours']*$very_high_wage;  
                }
            }        
        }
    }

    public function hourSpent($start_time, $finish_time, $event_date)
    {
        $event_day = date('l',strtotime($event_date));
        $start_time = str_replace('["','',str_replace('"]','',$start_time));
        
        $start_time = $this->convertHourToFloat($start_time);
        $finish_time = $this->convertHourToFloat($finish_time);

        $hours_spent = [];

        if($event_day != 'Saturday' && $event_day != 'Sunday')
        {
            if($start_time < 19 && $finish_time < 19)
            {
                $hours_spent['low_cost_hours'] = $finish_time - $start_time;
                $hours_spent['high_cost_hours'] = 0;
                $hours_spent['very_high_hours'] = 0;
                return $hours_spent;
            }
            elseif($start_time < 19 && 19 < $finish_time && $finish_time < 24)
            {
                $high_cost_hours = $finish_time - 19;
                $low_cost_hours = 19 - $start_time;
                $hours_spent['low_cost_hours'] = $low_cost_hours;
                $hours_spent['high_cost_hours'] = $high_cost_hours;
                $hours_spent['very_high_hours'] = 0;
                return $hours_spent;
            }
            elseif(19 < $start_time && $start_time < 24 && $finish_time < 24)
            {
                $hours_spent['low_cost_hours'] = 0;
                $hours_spent['high_cost_hours'] = $finish_time - $start_time;
                $hours_spent['very_high_hours'] = 0;
                return $hours_spent;
            }
            elseif(19 < $start_time && $start_time < 24 && 00 <= $finish_time && $finish_time <= 07)
            {
                $high_cost_hours = 24 - $start_time;
                $very_high_hours = 07 - $finish_time;
                $hours_spent['low_cost_hours'] = 0;
                $hours_spent['high_cost_hours'] = $high_cost_hours;
                $hours_spent['very_high_hours'] = $very_high_hours;
                return $hours_spent;                
            }
            elseif(00 <= $start_time && $start_time < 07 && 00 < $finish_time && $finish_time <= 07)
            {
                $hours_spent['low_cost_hours'] = 0;
                $hours_spent['high_cost_hours'] = 0;
                $hours_spent['very_high_hours'] = $finish_time - $start_time;
                return $hours_spent;                
            }
            elseif($start_time < 19 && 00 <= $finish_time && $finish_time <= 07)
            {
                $low_cost_hours = 19 - $start_time;
                $high_cost_hours = 24 - 19;
                $very_high_hours = 00 - $finish_time;
                $hours_spent['low_cost_hours'] = $low_cost_hours;
                $hours_spent['high_cost_hours'] = $high_cost_hours;
                $hours_spent['very_high_hours'] = $very_high_hours;
                return $hours_spent;                  
            }
        } 
        else
        {
            $hours_spent['low_cost_hours'] = $finish_time - $start_time;
            $hours_spent['high_cost_hours'] = 0;
            $hours_spent['very_high_hours'] = 0;
            return $hours_spent;   
        }
    }

    public function convertHourToFloat($hour)
    {
        $part = explode(':', $hour);
        return $part[0] + floor(($part[1]/60)*100) / 100 . PHP_EOL;        
    }

    public function publicHolidays($year)
    {
        $publicHolidays = [
            '01/01/'.$year,
            '26/01/'.$year,
            '25/04/'.$year,
            '25/12/'.$year,
            '26/12/'.$year
        ];

        if($year == 2017)
        {
            $publicHolidays[] = '02/01/2017';
            $publicHolidays[] = '14/04/2017';
            $publicHolidays[] = '15/04/2017';
            $publicHolidays[] = '16/04/2017';
            $publicHolidays[] = '17/04/2017';
            $publicHolidays[] = '12/06/2017';
            $publicHolidays[] = '02/10/2017';
        }
        
        return $publicHolidays;
    }
}