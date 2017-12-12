<?php
namespace App\Services;

use DateTime;
use App\Event;
use App\User;
use App\Assignment;
use App\Services\UserService;

class WeekReport{
	public $id		= 0;
	public $date 	= '';
	public $name 	= '';
	public $staff 	= [];
}

class Staff{
	public $id			= 0;
	public $first_name 	= '';
	public $last_name 	= '';
	public $level 		= 0;
	public $start_time	= '';
	public $break		= '';
	public $end_time	= '';
	public $worktime 	= 0;
}

class ReportService {

	public $date;
	public $week_start;
	public $week_end;
	public $last_week;
	public $next_week;
	public $events;


	public function __construct($week = null){
		if($week === null){
			$this->date = date('W');
		} else {
			$this->date = $week;
		}
		$isoDate = new DateTime();
		$this->week_start 	= 	$isoDate->setISODate(date('Y'),$this->date)->format('Y-m-d');
		$this->week_end 	= 	date('Y-m-d', strtotime($this->week_start.'+7 day'));;
        $this->last_week	= 	$this->date - 1;
        $this->next_week 	= 	$this->date + 1;
        $this->events 		= 	Event::where('event_date','>=',$this->week_start)->where('event_date','<',$this->week_end)->orderBy('event_date','ASC')->get();
	}

	public function getWeekReport(){
		$weekReport = [];

		foreach ($this->events as $event) {
			$report = new WeekReport();
			$report->id = $event->id;
			$report->date = date('l d/m/Y', strtotime($event->event_date));
			$report->name = $event->event_name;
			$report->staff = $this->getStaff($event);

			if(count($report->staff) > 0 ){
				$weekReport[] = $report;
			}
		}

		return $weekReport;
	}

	public function getStaff(Event $event){
		$staffs = [];

		$assignments = $event->assignments;

		if($assignments !== null){
			foreach($assignments as $assignment){
				$staff = new Staff();
				$staff->id 			= $assignment->user->id;
				$staff->first_name 	= $assignment->user->profile->first_name;
				$staff->last_name 	= $assignment->user->profile->last_name;
				$staff->level 		= $assignment->user->level;
				$staff->start_time 	= $assignment->time;
				$staff->break 		= $assignment->break;
				$staff->end_time 	= $assignment->hours;
				$staff->worktime	= $this->getWorktime($assignment); 
				
				$staffs[] = $staff;
				
			}
		}
		return $staffs;
	}

	public function getWorktime(Assignment $assignment){
		$userService = new UserService($assignment->user);
		return $wortime = $userService->getTotalHour($assignment);
	}

}