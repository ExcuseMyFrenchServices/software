<?php
namespace App\Services;

use App\Assignment;
use App\Availability;
use App\Event;
use App\User;
use App\PublicHoliday;
use DateTime;
use App\Services\UsersMissions;


class FinancialReportCalculation {

    /**
     * Make financial calculations for reporting such as the cost of a employee for a specific event or the number of hour he made. Also, the class will help determining which cost is it for a specific hour of the week.
     *
     * The hours are extracted from the events hours and date
    */

    public function staffCost($start_time, $finish_time, $event_date, $user_level, $break)
    {
        $hours = $this->hourSpent($start_time, $finish_time, $event_date, $break);
        switch ($user_level) 
        {
            case 1:
                $low_wage = 23;
                $high_wage = 25;
                $very_high_wage = 26;
                $saturday_wage = 27.50;
                $sunday_wage = 32;
                $public_holiday_wage = 50.50;
                break;
            
            case 2:
                $low_wage = 24;
                $high_wage = 26;
                $very_high_wage = 27;
                $saturday_wage = 28.50;
                $sunday_wage = 33.50;
                $public_holiday_wage = 52;
                break;

            case 3:
                $low_wage = 25;
                $high_wage = 27;
                $very_high_wage = 28;
                $saturday_wage = 29.50;
                $sunday_wage = 34.50;
                $public_holiday_wage = 54;
                break;

            case 4:
                $low_wage = 27.50;
                $high_wage = 29.50;
                $very_high_wage = 30.50;
                $saturday_wage = 33;
                $sunday_wage = 38.50;
                $public_holiday_wage = 60.50;
                break;    
        }

        return $staffCost = $hours['low_cost_hours'] * $low_wage + $hours['high_cost_hours'] * $high_wage + $hours['very_high_hours'] * $very_high_wage + $hours['saturday_hours'] * $saturday_wage + $hours['sunday_hours'] * $sunday_wage + $hours['public_holiday_hours'] * $public_holiday_wage;
    }

    public function hourSpent($start_time, $finish_time, $event_date, $break)
    {
        $event_day = date('l',strtotime($event_date));
        
        $next_day = date('d/m/Y', strtotime($event_date.'+1 day'));
        $next_event_day = date('l', strtotime($event_date.'+1 day'));

        $start_time = str_replace('["','',str_replace('"]','',$start_time));
        
        $start_time = $this->convertHourToFloat($start_time);
        $finish_time = $this->convertHourToFloat($finish_time);

        $hours_spent = [];

        $bonus_time = 0;

        if(empty($break))
        {
            $break = 0;
        }
        else
        {
            if($break == '1')
            {
                $break == '60';
            }
            $break = floor(($break/60)*100) / 100 . PHP_EOL;
        }

        if($event_day != 'Saturday' && $event_day != 'Sunday' && !$this->is_public_holiday($event_date))
        {
            if(7 <= $start_time && $start_time < 19)
            {
                if(7 < $finish_time && $finish_time <= 19)
                {
                    if($finish_time - $start_time < 4)
                    {
                        $bonus_time = 4 - ($finish_time - $start_time);
                    }
                    
                    $hours_spent['low_cost_hours'] = ($finish_time - $start_time) + $bonus_time-$break;
                    $hours_spent['high_cost_hours'] = 0;
                    $hours_spent['very_high_hours'] = 0;
                    $hours_spent['saturday_hours'] = 0;
                    $hours_spent['sunday_hours'] = 0;
                    $hours_spent['public_holiday_hours'] = 0;
                    $hours_spent['bonus_time'] = $bonus_time;
                    return $hours_spent;                    
                }
                elseif(19 < $finish_time && $finish_time < 24)
                {

                    $low_cost_hours = 19 - $start_time;                    
                    $high_cost_hours = $finish_time - 19;
                    
                    if($high_cost_hours + $low_cost_hours < 4)
                    {
                        $bonus_time = 4 - ($high_cost_hours + $low_cost_hours);
                    }
                    
                    $hours_spent['low_cost_hours'] = $low_cost_hours-$break;
                    $hours_spent['high_cost_hours'] = $high_cost_hours + $bonus_time;
                    $hours_spent['very_high_hours'] = 0;
                    $hours_spent['saturday_hours'] = 0;
                    $hours_spent['sunday_hours'] = 0;
                    $hours_spent['public_holiday_hours'] = 0;
                    $hours_spent['bonus_time'] = $bonus_time;
                    return $hours_spent;                    
                }
                elseif($finish_time == 0)
                {
                    $low_cost_hours = 19 - $start_time;                    
                    $high_cost_hours = 24 - 19;
                    $hours_spent['low_cost_hours'] = $low_cost_hours-$break;
                    $hours_spent['high_cost_hours'] = $high_cost_hours;
                    $hours_spent['very_high_hours'] = 0;
                    $hours_spent['saturday_hours'] = 0;
                    $hours_spent['sunday_hours'] = 0;
                    $hours_spent['public_holiday_hours'] = 0;
                    $hours_spent['bonus_time'] = $bonus_time;
                    return $hours_spent;                       
                }
                elseif(0 < $finish_time && $finish_time <= 7)
                {
                    if($this->is_public_holiday($next_day))
                    {
                        $low_cost_hours = 19 - $start_time;                    
                        $high_cost_hours = 24 - 19;
                        $public_holiday_hours = $finish_time;
                        $hours_spent['low_cost_hours'] = $low_cost_hours-$break;
                        $hours_spent['high_cost_hours'] = $high_cost_hours;
                        $hours_spent['very_high_hours'] = 0;
                        $hours_spent['saturday_hours'] = 0;
                        $hours_spent['sunday_hours'] = 0;
                        $hours_spent['public_holiday_hours'] = $public_holiday_hours;
                        $hours_spent['bonus_time'] = $bonus_time;
                        return $hours_spent;    
                    }
                    else
                    {
                        if($event_day != 'Friday')
                        {
                            $low_cost_hours = 19 - $start_time;                    
                            $high_cost_hours = 24 - 19;
                            $very_high_hours = $finish_time;
                            $hours_spent['low_cost_hours'] = $low_cost_hours-$break;
                            $hours_spent['high_cost_hours'] = $high_cost_hours;
                            $hours_spent['very_high_hours'] = $very_high_hours;
                            $hours_spent['saturday_hours'] = 0;
                            $hours_spent['sunday_hours'] = 0;
                            $hours_spent['public_holiday_hours'] = 0;
                            $hours_spent['bonus_time'] = $bonus_time;
                            return $hours_spent;  
                        }
                        elseif($event_day == 'Friday')
                        {
                            $low_cost_hours = 19 - $start_time;                    
                            $high_cost_hours = 24 - 19;
                            $saturday_hours = $finish_time;
                            $hours_spent['low_cost_hours'] = $low_cost_hours-$break;
                            $hours_spent['high_cost_hours'] = $high_cost_hours;
                            $hours_spent['very_high_hours'] = 0;
                            $hours_spent['saturday_hours'] = $saturday_hours;
                            $hours_spent['sunday_hours'] = 0;
                            $hours_spent['public_holiday_hours'] = 0;
                            $hours_spent['bonus_time'] = $bonus_time;
                            return $hours_spent;
                        }
                    }
                    
                }
                elseif(7 < $finish_time && $finish_time <= 19)
                {
                    if($this->is_public_holiday($next_day))
                    {
                        $low_cost_hours = 19 - $start_time;                    
                        $high_cost_hours = 24 - 19;
                        $public_holiday_hours = $finish_time;
                        $hours_spent['low_cost_hours'] = $low_cost_hours-$break;
                        $hours_spent['high_cost_hours'] = $high_cost_hours;
                        $hours_spent['very_high_hours'] = 0;
                        $hours_spent['saturday_hours'] = 0;
                        $hours_spent['sunday_hours'] = 0;
                        $hours_spent['public_holiday_hours'] = $public_holiday_hours;
                        $hours_spent['bonus_time'] = $bonus_time;
                        return $hours_spent;    
                    }
                    else
                    {                    
                        if($event_day != 'Friday')
                        {
                            $low_cost_hours = 19 - $start_time + $finish_time - 7;                    
                            $high_cost_hours = 24 - 19;
                            $very_high_hours = 7;
                            $hours_spent['low_cost_hours'] = $low_cost_hours-$break;
                            $hours_spent['high_cost_hours'] = $high_cost_hours;
                            $hours_spent['very_high_hours'] = $very_high_hours;
                            $hours_spent['saturday_hours'] = 0;
                            $hours_spent['sunday_hours'] = 0;
                            $hours_spent['public_holiday_hours'] = 0;
                            $hours_spent['bonus_time'] = $bonus_time;
                            return $hours_spent;        
                        }
                        else
                        {
                            $low_cost_hours = 19 - $start_time;                     
                            $high_cost_hours = 24 - 19;
                            $very_high_hours = 7;
                            $saturday_hours = $finish_time - 7;
                            $hours_spent['low_cost_hours'] = $low_cost_hours-$break;
                            $hours_spent['high_cost_hours'] = $high_cost_hours;
                            $hours_spent['very_high_hours'] = $very_high_hours;
                            $hours_spent['saturday_hours'] = $saturday_hours;
                            $hours_spent['sunday_hours'] = 0;
                            $hours_spent['public_holiday_hours'] = 0;
                            $hours_spent['bonus_time'] = $bonus_time;
                            return $hours_spent; 
                        }
                    }                 
                }
            }
            elseif(19 <= $start_time && $start_time < 24)
            {
                if(19 < $finish_time && $finish_time < 24)
                {        

                    $high_cost_hours = $finish_time - $start_time;
                    
                    if($high_cost_hours < 4)
                    {
                        $bonus_time = 4 - ($high_cost_hours);
                    }                     
                    
                    $hours_spent['low_cost_hours'] = 0;
                    $hours_spent['high_cost_hours'] = $high_cost_hours + $bonus_time-$break;
                    $hours_spent['very_high_hours'] = 0;
                    $hours_spent['saturday_hours'] = 0;
                    $hours_spent['sunday_hours'] = 0;
                    $hours_spent['public_holiday_hours'] = 0;
                    $hours_spent['bonus_time'] = $bonus_time;
                    return $hours_spent;                    
                }
                elseif($finish_time == 0)
                {                   
                    $high_cost_hours = 24 - $start_time;

                    if($high_cost_hours < 4)
                    {
                        $bonus_time = 4 - ($high_cost_hours);
                    } 

                    $hours_spent['low_cost_hours'] = 0;
                    $hours_spent['high_cost_hours'] = $high_cost_hours + $bonus_time-$break;
                    $hours_spent['very_high_hours'] = 0;
                    $hours_spent['saturday_hours'] = 0;
                    $hours_spent['sunday_hours'] = 0;
                    $hours_spent['public_holiday_hours'] = 0;
                    $hours_spent['bonus_time'] = $bonus_time;
                    return $hours_spent;                       
                }
                elseif(0 < $finish_time && $finish_time < 7)
                {
                    if($this->is_public_holiday($next_day))
                    {                   
                        $high_cost_hours = 24 - $start_time;
                        $public_holiday_hours = $finish_time;

                        if($high_cost_hours + $public_holiday_hours < 4)
                        {
                            $bonus_time = 4 - ($high_cost_hours + $public_holiday_hours);
                        } 

                        $hours_spent['low_cost_hours'] = 0;
                        $hours_spent['high_cost_hours'] = $high_cost_hours-$break;
                        $hours_spent['very_high_hours'] = 0;
                        $hours_spent['saturday_hours'] = 0;
                        $hours_spent['sunday_hours'] = 0;
                        $hours_spent['public_holiday_hours'] = $public_holiday_hours + $bonus_time;
                        $hours_spent['bonus_time'] = $bonus_time;
                        return $hours_spent;    
                    }
                    else
                    {                    
                        if($event_day != 'Friday')
                        {                   
                            $high_cost_hours = 24 - $start_time;
                            $very_high_hours = $finish_time;

                            if($high_cost_hours + $very_high_hours < 4)
                            {
                                $bonus_time = 4 - ($high_cost_hours + $very_high_hours);
                            } 

                            $hours_spent['low_cost_hours'] = 0;
                            $hours_spent['high_cost_hours'] = $high_cost_hours-$break;
                            $hours_spent['very_high_hours'] = $very_high_hours + $bonus_time;
                            $hours_spent['saturday_hours'] = 0;
                            $hours_spent['sunday_hours'] = 0;
                            $hours_spent['public_holiday_hours'] = 0;
                            $hours_spent['bonus_time'] = $bonus_time;
                            return $hours_spent;  
                        }
                        else
                        {
                            $high_cost_hours = 24 - $start_time;
                            $saturday_hours = $finish_time;

                            if($high_cost_hours + $saturday_hours < 4)
                            {
                                $bonus_time = 4 - ($high_cost_hours + $saturday_hours);
                            } 

                            $hours_spent['low_cost_hours'] = 0;
                            $hours_spent['high_cost_hours'] = $high_cost_hours-$break;
                            $hours_spent['very_high_hours'] = 0;
                            $hours_spent['saturday_hours'] = $saturday_hours + $bonus_time;
                            $hours_spent['sunday_hours'] = 0;
                            $hours_spent['public_holiday_hours'] = 0;
                            $hours_spent['bonus_time'] = $bonus_time;
                            return $hours_spent;   
                        }
                    }
                }
                elseif (7 < $finish_time && $finish_time <= 19) 
                {
                    if($this->is_public_holiday($next_day))
                    {                   
                        $high_cost_hours = 24 - $start_time;
                        $public_holiday_hours = $finish_time;
                        $hours_spent['low_cost_hours'] = 0;
                        $hours_spent['high_cost_hours'] = $high_cost_hours-$break;
                        $hours_spent['very_high_hours'] = 0;
                        $hours_spent['saturday_hours'] = 0;
                        $hours_spent['sunday_hours'] = 0;
                        $hours_spent['public_holiday_hours'] = $public_holiday_hours;
                        $hours_spent['bonus_time'] = $bonus_time;
                        return $hours_spent;    
                    }
                    else
                    {                    
                        if($event_day != 'Friday')
                        {
                            $low_cost_hours = $finish_time - 7;
                            $high_cost_hours = 24 - $start_time;
                            $very_high_hours = 7;
                            $hours_spent['low_cost_hours'] = $low_cost_hours-$break;
                            $hours_spent['high_cost_hours'] = $high_cost_hours;
                            $hours_spent['very_high_hours'] = $very_high_hours;
                            $hours_spent['saturday_hours'] = 0;
                            $hours_spent['sunday_hours'] = 0;
                            $hours_spent['public_holiday_hours'] = 0;
                            $hours_spent['bonus_time'] = $bonus_time;
                            return $hours_spent;  
                        }
                        else
                        {
                            $saturday_hours = $finish_time - 7;
                            $high_cost_hours = 24 - $start_time;
                            $very_high_hours = 7;
                            $hours_spent['low_cost_hours'] = 0;
                            $hours_spent['high_cost_hours'] = $high_cost_hours-$break;
                            $hours_spent['very_high_hours'] = $very_high_hours;
                            $hours_spent['saturday_hours'] = $saturday_hours;
                            $hours_spent['sunday_hours'] = 0;
                            $hours_spent['public_holiday_hours'] = 0;
                            $hours_spent['bonus_time'] = $bonus_time;
                            return $hours_spent;  
                        }  
                    }                 
                }
            }
            elseif ($start_time == 0) 
            {
                if(0 < $finish_time && $finish_time <= 7)
                {                   
                    $very_high_hours = $finish_time;
                    if($very_high_hours < 4)
                    {
                        $bonus_time = 4 - ($very_high_hours);
                    } 
                    $hours_spent['low_cost_hours'] = 0;
                    $hours_spent['high_cost_hours'] = 0;
                    $hours_spent['very_high_hours'] = $very_high_hours + $bonus_time-$break;
                    $hours_spent['saturday_hours'] = 0;
                    $hours_spent['sunday_hours'] = 0;
                    $hours_spent['public_holiday_hours'] = 0;
                    $hours_spent['bonus_time'] = $bonus_time;
                    return $hours_spent;  
                }    
                elseif(7 < $finish_time)
                {
                    $low_cost_hours = $finish_time - 7;
                    $very_high_hours = 7;
                    $hours_spent['low_cost_hours'] = $low_cost_hours-$break;
                    $hours_spent['high_cost_hours'] = 0;
                    $hours_spent['very_high_hours'] = $very_high_hours;
                    $hours_spent['saturday_hours'] = 0;
                    $hours_spent['sunday_hours'] = 0;
                    $hours_spent['public_holiday_hours'] = 0;
                    $hours_spent['bonus_time'] = $bonus_time;
                    return $hours_spent; 
                }         
            }
            elseif(0 < $start_time && $start_time < 7)
            {
                if($finish_time <= 7)
                {                   
                    $very_high_hours = $finish_time - $start_time;
                    
                    if($very_high_hours < 4)
                    {
                        $bonus_time = 4 - ($very_high_hours);
                    }

                    $hours_spent['low_cost_hours'] = 0;
                    $hours_spent['high_cost_hours'] = 0;
                    $hours_spent['very_high_hours'] = $very_high_hours + $bonus_time-$break;
                    $hours_spent['saturday_hours'] = 0;
                    $hours_spent['sunday_hours'] = 0;
                    $hours_spent['public_holiday_hours'] = 0;
                    $hours_spent['bonus_time'] = $bonus_time;
                    return $hours_spent;  
                }    
                elseif(7 < $finish_time && $finish_time < 19)
                {
                    $low_cost_hours = $finish_time - 7;
                    $very_high_hours = 7 - $start_time;

                    if($low_cost_hours + $very_high_hours < 4)
                    {
                        $bonus_time = 4 - ($low_cost_hours + $very_high_hours);
                    }

                    $hours_spent['low_cost_hours'] = $low_cost_hours + $bonus_time-$break;
                    $hours_spent['high_cost_hours'] = 0;
                    $hours_spent['very_high_hours'] = $very_high_hours;
                    $hours_spent['saturday_hours'] = 0;
                    $hours_spent['sunday_hours'] = 0;
                    $hours_spent['public_holiday_hours'] = 0;
                    $hours_spent['bonus_time'] = $bonus_time;
                    return $hours_spent; 
                } 
            }
        }
        elseif($event_day == 'Saturday' && !$this->is_public_holiday($event_date))
        {
            if(7 <= $start_time && $start_time < 24)
            {
                if($start_time < $finish_time && $finish_time < 24)
                {
                    if($finish_time - $start_time < 4)
                    {
                        $bonus_time = 4 - ($finish_time - $start_time);
                    }

                    $hours_spent['low_cost_hours'] = 0;
                    $hours_spent['high_cost_hours'] = 0;
                    $hours_spent['very_high_hours'] = 0;
                    $hours_spent['saturday_hours'] = ($finish_time - $start_time) + $bonus_time-$break;
                    $hours_spent['sunday_hours'] = 0;
                    $hours_spent['public_holiday_hours'] = 0;
                    $hours_spent['bonus_time'] = $bonus_time;
                    return $hours_spent; 
                }
                elseif($finish_time == 0)
                {
                    if(24 - $start_time < 4)
                    {
                        $bonus_time = 4 - (24-$start_time);
                    }

                    $hours_spent['low_cost_hours'] = 0;
                    $hours_spent['high_cost_hours'] = 0;
                    $hours_spent['very_high_hours'] = 0;
                    $hours_spent['saturday_hours'] = (24 - $start_time) + $bonus_time-$break;
                    $hours_spent['sunday_hours'] = 0;
                    $hours_spent['public_holiday_hours'] = 0;
                    $hours_spent['bonus_time'] = $bonus_time;
                    return $hours_spent; 
                }      
                elseif (0 < $finish_time && $finish_time < $start_time) 
                {
                    if($this->is_public_holiday($next_day))
                    {
                        if( (24-$start_time)+$finish_time < 4)
                        {
                            $bonus_time = 4 - ((24-$start_time)+$finish_time);
                        }

                        $hours_spent['low_cost_hours'] = 0;
                        $hours_spent['high_cost_hours'] = 0;
                        $hours_spent['very_high_hours'] = 0;
                        $hours_spent['saturday_hours'] = 24 - $start_time-$break;
                        $hours_spent['sunday_hours'] = 0;
                        $hours_spent['public_holiday_hours'] = $finish_time + $bonus_time;
                        $hours_spent['bonus_time'] = $bonus_time;
                        return $hours_spent; 
                    }
                    else
                    {
                        if( (24-$start_time)+$finish_time < 4)
                        {
                            $bonus_time = 4 - ((24-$start_time)+$finish_time);
                        }

                        $hours_spent['low_cost_hours'] = 0;
                        $hours_spent['high_cost_hours'] = 0;
                        $hours_spent['very_high_hours'] = 0;
                        $hours_spent['saturday_hours'] = 24 - $start_time-$break;
                        $hours_spent['sunday_hours'] = $finish_time + $bonus_time;
                        $hours_spent['public_holiday_hours'] = 0;
                        $hours_spent['bonus_time'] = $bonus_time;
                        return $hours_spent;                         
                    }
                }         
            }
        }
        elseif($event_day == 'Sunday' && !$this->is_public_holiday($event_date))
        {
            if( 7 < $finish_time && $finish_time < 24)
            {
                if($finish_time - $start_time < 4)
                {
                    $bonus_time = 4 - ($finish_time - $start_time);
                }

                $hours_spent['low_cost_hours'] = 0;
                $hours_spent['high_cost_hours'] = 0;
                $hours_spent['very_high_hours'] = 0;
                $hours_spent['saturday_hours'] = 0;
                $hours_spent['sunday_hours'] = ($finish_time - $start_time)+$bonus_time-$break;
                $hours_spent['public_holiday_hours'] = 0;
                $hours_spent['bonus_time'] = $bonus_time;
                return $hours_spent;                 
            }
            elseif($finish_time == 0)
            {
                if(24-$start_time < 4)
                {
                    $bonus_time = 4 - (24-$start_time);
                }

                $hours_spent['low_cost_hours'] = 0;
                $hours_spent['high_cost_hours'] = 0;
                $hours_spent['very_high_hours'] = 0;
                $hours_spent['saturday_hours'] = 0;
                $hours_spent['sunday_hours'] = (24 - $start_time)+$start_time-$break;
                $hours_spent['public_holiday_hours'] = 0;
                $hours_spent['bonus_time'] = $bonus_time;
                return $hours_spent;                    
            }
            elseif(0 < $finish_time && $finish_time <= 7)
            {
                if($this->is_public_holiday($next_day))
                {
                    if( (24-$start_time)+ $finish_time < 4)
                    {
                        $bonus_time = 4 - ((24-$start_time)+$finish_time);
                    }

                    $hours_spent['low_cost_hours'] = 0;
                    $hours_spent['high_cost_hours'] = 0;
                    $hours_spent['very_high_hours'] = 0;
                    $hours_spent['saturday_hours'] = 0;
                    $hours_spent['sunday_hours'] = 24 - $start_time-$break;
                    $hours_spent['public_holiday_hours'] = $finish_time + $bonus_time;
                    $hours_spent['bonus_time'] = $bonus_time;
                    return $hours_spent;                        
                }
                else
                {
                    if( (24-$start_time)+ $finish_time < 4)
                    {
                        $bonus_time = 4 - ((24-$start_time)+$finish_time);
                    }

                    $hours_spent['low_cost_hours'] = 0;
                    $hours_spent['high_cost_hours'] = 0;
                    $hours_spent['very_high_hours'] = $finish_time + $bonus_time-$break;
                    $hours_spent['saturday_hours'] = 0;
                    $hours_spent['sunday_hours'] = 24 - $start_time;
                    $hours_spent['public_holiday_hours'] = 0;
                    $hours_spent['bonus_time'] = $bonus_time;
                    return $hours_spent;                       
                }
            }
        }
        elseif($this->is_public_holiday($event_date))
        {
            if($start_time < $finish_time && $finish_time < 24)
            {
                if($finish_time - $start_time < 4)
                {
                    $bonus_time = 4 - ($finish_time - $start_time);
                }

                $hours_spent['low_cost_hours'] = 0;
                $hours_spent['high_cost_hours'] = 0;
                $hours_spent['very_high_hours'] = 0;
                $hours_spent['saturday_hours'] = 0;
                $hours_spent['sunday_hours'] = 0;
                $hours_spent['public_holiday_hours'] = ($finish_time - $start_time) + $bonus_time-$break;
                $hours_spent['bonus_time'] = $bonus_time;
                return $hours_spent;                   
            }
            elseif ($finish_time == 0) 
            {
                if(24 - $start_time < 4)
                {
                    $bonus_time = 4 - (24 - $start_time);
                }

                $hours_spent['low_cost_hours'] = 0;
                $hours_spent['high_cost_hours'] = 0;
                $hours_spent['very_high_hours'] = 0;
                $hours_spent['saturday_hours'] = 0;
                $hours_spent['sunday_hours'] = 0;
                $hours_spent['public_holiday_hours'] = (24 - $start_time)+$bonus_time-$break;
                $hours_spent['bonus_time'] = $bonus_time;
                return $hours_spent;                   
            }
            elseif($finish_time < $start_time)
            {
                if($next_event_day == 'Saturday')
                {
                    if( (24-$start_time)+$finish_time < 4)
                    {
                        $bonus_time = 4 - ((24-$start_time)+$finish_time);
                    }

                    $hours_spent['low_cost_hours'] = 0;
                    $hours_spent['high_cost_hours'] = 0;
                    $hours_spent['very_high_hours'] = 0;
                    $hours_spent['saturday_hours'] = $finish_time + $bonus_time-$break;
                    $hours_spent['sunday_hours'] = 0;
                    $hours_spent['public_holiday_hours'] = 24 - $start_time;
                    $hours_spent['bonus_time'] = $bonus_time;
                    return $hours_spent;
                }
                elseif($next_event_day == 'Sunday')
                {
                    if( (24-$start_time)+$finish_time < 4)
                    {
                        $bonus_time = 4 - ((24-$start_time)+$finish_time);
                    }

                    $hours_spent['low_cost_hours'] = 0;
                    $hours_spent['high_cost_hours'] = 0;
                    $hours_spent['very_high_hours'] = 0;
                    $hours_spent['saturday_hours'] = 0;
                    $hours_spent['sunday_hours'] = $finish_time+$bonus_time-$break;
                    $hours_spent['public_holiday_hours'] = 24 - $start_time;
                    $hours_spent['bonus_time'] = $bonus_time;
                    return $hours_spent;        
                }
                else
                {
                    if( (24-$start_time)+$finish_time < 4)
                    {
                        $bonus_time = 4 - ((24-$start_time)+$finish_time);
                    }
                    $hours_spent['low_cost_hours'] = $finish_time+$bonus_time-$break;
                    $hours_spent['high_cost_hours'] = 0;
                    $hours_spent['very_high_hours'] = 0;
                    $hours_spent['saturday_hours'] = 0;
                    $hours_spent['sunday_hours'] = 0;
                    $hours_spent['public_holiday_hours'] = 24 - $start_time;
                    $hours_spent['bonus_time'] = $bonus_time;
                    return $hours_spent;    
                }
            }    
        }
    }

    public function convertHourToFloat($hour)
    {
        if(!empty($hour))
        {
            $hour = str_replace('.3', '.30', $hour);
            $hour = str_replace('.', ':', $hour);
            if(strstr($hour,':') === FALSE)
            {
                $time = $hour;
            }
            else
            {
                $part = explode(':', $hour);
                $time = $part[0] + floor(($part[1]/60)*100) / 100 . PHP_EOL;    
            }
        }
        else
        {
            $time = '00';
        }

        return $time;        
    }


    public function is_public_holiday($date)
    {
        $date = date('d/m/Y', strtotime($date));
        $year = date('Y', strtotime($date));

        $publicHolidays = PublicHoliday::where('year','=',$year)
                                        ->orWhere('year', '=', 1)   
                                        ->orderBy('public_holiday_date', 'ASC')
                                        ->get();

        foreach($publicHolidays as $publicHoliday) 
        { 
            if($date == $publicHoliday->public_holiday_date)
            {
                return true;
            }
        }
    }
}