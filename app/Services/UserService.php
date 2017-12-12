<?php
namespace App\Services;

use DateTime;
use App\User;
use App\Event;
use App\Assignment;
use App\PublicHoliday;

class HourPay {
	public $low = 0;
	public $high = 0;
	public $very_high = 0;
	public $saturday = 0;
	public $sunday = 0;
	public $public_holiday = 0;
}

class PayRoll {
	public $date = '';
	public $isPublicHoliday = false;
	public $event_name = '';
	public $start = '';
	public $break = 0;
	public $end = '';
    public $travel_time = 0;
	public $total_hour = 0;
	public $pay = 0;
}

class UserService {

	private $user;
	public $hourPay;

	public function __construct(User $user){
		$this->user = $user;
		$this->hourPay = new HourPay();
		$this->getUserHourPay();
	}

	public function getPayrolls(){
		$payrolls = [];

		foreach($this->getUserAssignments() as $assignment){
			$event = $assignment->event;

			$payroll = new PayRoll();
			$payroll->date 				= 		$event->event_date;
			$payroll->isPublicHoliday 	=		$this->is_public_holiday($event->event_date);
			$payroll->event_name 		= 		$event->client->name;
			$payroll->start 			=		$assignment->time;
			$payroll->break 			=		$assignment->break;
			$payroll->end 				=		$assignment->hours;
            $payroll->travel_time       =       $assignment->event->travel_paid;
			$payroll->total_hour		=		$this->getTotalHour($assignment);
			$payroll->pay 				=		$this->getAssignmentPay($assignment);

			$payrolls[] = $payroll;
		}

		return $payrolls;
	}

	public function getUserAssignments(){
		return $assignments = Assignment::where('user_id','=',$this->user->id)->get();;
	}

	public function getTotalHour(Assignment $assignment){
		$hours = $this->getAssignmentHours($assignment);
		
		$total = 0;
		foreach($hours as $hour => $workTime){
			$total += $workTime;
		}
    	$total = $this->removeTime($total,$assignment->break);
        $total = $this->addTime($total,$assignment->event->travel_paid);

        return $total;
	}

    public function removeTime($time,$minutesTime){
        $minutesTime = $minutesTime/100;
        $minutes = $time - floor($time);
        $hour = floor($time);

        $minus = $minutes - $minutesTime;
        
        if($minus < 0){
            $hour--;
            $minus = 0.6 + $minus;
        }
        
        return $hour+$minus;
    }

    public function addTime($time,$minutesTime){
        $minutesTime = $minutesTime/100;
        $minutes = $time - floor($time);
        $hour = floor($time);

        $added = $minutes + $minutesTime;

        if($added > 60){
            $hour++;
            $added = 0.6 - $added;
        }

        return $hour+$added;
    }

	public function getAssignmentHours(Assignment $assignment)
    {
    	$start = $assignment->time;
    	$end = $assignment->hours;
    	if($end === null){
    		$end = $this->addHour($start,4);
    	}
    	
    	$first = intval(date('H',strtotime($start)));
    	$last = $this->midnight(intval(date('H',strtotime($end))));

    	$hours = [];
    	
    	for ($i= $first; $i <= $last; $i++) { 
    		if( $i == $first && !$this->isFullHour($start) ){
    			$hours[$i] = 1-(date('i', strtotime($start))/60);
    		} elseif( $i == $last && !$this->isFullHour($end) ) { 
    			$hours[$i] = date('i', strtotime($end))/60;
    		} elseif( $i == $last && $this->isFullHour($end) ){
    			$hours[$i] = 0;
    		} else {
    			$hours[$i] = 1;
    		}
    	}
		
    	return $hours;
    }

    public function getPay($date,$hour,$workTime){
    	if(
    		strtolower($this->getHumanDay($date)) == 'saturday' || 
    		strtolower($this->getHumanDay($date)) == 'sunday'
    	){
    		$range = strtolower($this->getHumanDay($date));
    		return $workTime * $this->hourPay->$range;

    	} elseif($this->is_public_holiday($date)){
    		return $workTime * $this->hourPay->public_holiday;

    	} elseif(strtolower($this->getHumanDay($date)) == 'friday' && $hour >= 0 && $hour < 7){
    		return $workTime * $this->hourPay->saturday;
    	} else { 
    		if($hour >= 7 && $hour < 19){
    			return $workTime * $this->hourPay->low;
    		} elseif($hour >= 19 && $hour < 24){
    			return $workTime * $this->hourPay->high;
    		} else {
    			return $workTime * $this->hourPay->very_high;
    		}
    	}
    }

    public function getRatePay($date,$hour){
    	if(
    		strtolower($this->getHumanDay($date)) == 'saturday' || 
    		strtolower($this->getHumanDay($date)) == 'sunday'
    	){
    		$range = strtolower($this->getHumanDay($date));
    		return $this->hourPay->$range;

    	} elseif($this->is_public_holiday($date)){
    		return $this->hourPay->public_holiday;

    	} elseif(strtolower($this->getHumanDay($date)) == 'friday' && $hour >= 0 && $hour < 7){
    		return $this->hourPay->saturday;
    	} else { 
    		if($hour >= 7 && $hour < 19){
    			return $this->hourPay->low;
    		} elseif($hour >= 19 && $hour < 24){
    			return $this->hourPay->high;
    		} else {
    			return $this->hourPay->very_high;
    		}
    	}
    }

    public function getAssignmentPay(Assignment $assignment){
    	$hours = $this->getAssignmentHours($assignment);

    	$pay = 0;
    	
    	foreach ($hours as $hour => $workTime) {
    		$pay += $this->getPay($assignment->event->event_date,$hour,$workTime);
    	}
    	
    	if($assignment->break){
    		$pay -= $assignment->break/60 * $this->getLowestHour($assignment);
    	}
    	
    	if($this->isLessThanFour($assignment)){
    		$pay += 1 * $this->getHighestHour($assignment);
    	}
		
    	return round($pay,2);
    }

    public function midnight($time){
    	if($time == '00'){
    		return 24;
    	} else {
    		return intval($time);
    	}
    }

    public function isFullHour($time){
    	if(date('i',strtotime($time)) == '00'){
    		return true;
    	} 
    	return false;
    }

    public function isLessThanFour(Assignment $assignment){
    	$first = intval(date('H',strtotime($assignment->time)));
    	$last = intval($this->midnight(date('H',strtotime($assignment->hours))));

    	if( ($last - $first) < 4){
    		return true;
    	} else {
    		return false;
    	}
    }

    public function addHour($time,$add){
    	$hour = date('H',strtotime($time));
    	$minutes = date('i', strtotime($time));

    	$newHour = intval($hour) + $add;
    	return $newTime = $newHour.':'.$minutes;
    }

    public function addMinutes($time,$add){
    	$hour = date('H',strtotime($time));
    	$minutes = date('i', strtotime($time));

    	$newMinutes = intval($minutes) + $add;
    	if($newMinutes >= 60){
    		$newMinutes = $newMinutes - 60;
    		$hour = intval($hour)+1;
    	}
    	return $newTime = $hour.':'.$newMinutes;
    }

	public function getHighestHour(Assignment $assignment){
		$firstHour = intval(date('H', strtotime($assignment->time)));
		$lastHour = $this->midnight(date('H', strtotime($assignment->hours)));
		// On a specific case where the user started before 7
		if($firstHour < $lastHour && $firstHour < 7){
			return $this->getRatePay($assignment->event->event_date,$firstHour);
		} else {
			return $this->getRatePay($assignment->event->event_date,$lastHour);
		}
	}

	public function getLowestHour(Assignment $assignment){
		$hours = $this->getAssignmentHours($assignment);
		foreach ($hours as $hour => $workTime) {
			if($hour >= 7 && $hour < 24){
				return $this->getRatePay($assignment->event->event_date,$hour);
			}
		}
	}

	public function getHumanDay($date){
		return date('l', strtotime($date));
	}

	public function getUserHourPay(){
		$level = $this->user->level;

		switch ($level) 
        {
            case 1:
                $this->hourPay->low = 23;
                $this->hourPay->high = 25;
                $this->hourPay->very_high = 26;
                $this->hourPay->saturday = 27.50;
                $this->hourPay->sunday = 32;
                $this->hourPay->public_holiday = 50.50;
                break;
            
            case 2:
                $this->hourPay->low = 24;
                $this->hourPay->high = 26;
                $this->hourPay->very_high = 27;
                $this->hourPay->saturday = 28.50;
                $this->hourPay->sunday = 33.50;
                $this->hourPay->public_holiday = 52;
                break;

            case 3:
                $this->hourPay->low = 25;
                $this->hourPay->high = 27;
                $this->hourPay->very_high = 28;
                $this->hourPay->saturday = 29.50;
                $this->hourPay->sunday = 34.50;
                $this->hourPay->public_holiday = 54;
                break;

            case 4:
                $this->hourPay->low = 27.50;
                $this->hourPay->high = 29.50;
                $this->hourPay->very_high = 30.50;
                $this->hourPay->saturday = 33;
                $this->hourPay->sunday = 38.50;
                $this->hourPay->public_holiday = 60.50;
                break;    
        }
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