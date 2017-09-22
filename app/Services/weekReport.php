<?php
namespace App\Services;

use App\Assignment;
use App\Availability;
use App\Event;
use App\User;
use App\PublicHoliday;
use DateTime;
use App\Services\UsersMissions;

class weekReport {

	public $day;
	public $date;
	public $start;
	public $end;
	public $break;
	public $startStatus;
	public $endStatus;

	public $low = 0;
	public $mid = 0;
	public $hight = 0;
	public $saturday = 0;
	public $sunday = 0;
	public $publicHoliday = 0;
	public $bonus = 0;
	public $total = 0;

	public function __construct($date,$start,$end,$break)
	{
		$this->date = $date;
		$this->start = $this->convertStringToHour($start);
		$this->end = $this->convertStringToHour($end);
		$this->break = $break;
		$this->startStatus = $this->check_hour($this->start);
		$this->endStatus = $this->check_hour($this->end);
	}

	public function get_hours()
	{
		$this->check_day();
		$this->bonus();
		return $this->hours_spend();
	}

	private function bonus()
	{
		if($this->end > $this->start)
		{
			$check_bonus = $this->end - $this->start;
		}
		elseif($this->end < $this->start)
		{
			$check_bonus = (24 - $this->start) + $this->end;
		}
		else
		{
			$check_bonus = 24;
		}

		if($check_bonus < 4)
		{
			$this->bonus = 4- $check_bonus;
	
			$highest_time = $this->get_highest_time();
			$bonus_amount = $check_bonus - $this->$highest_time;
			$this->$highest_time = $this->$highest_time + (4 - ($this->$highest_time + $bonus_amount));
		}
	}

	public function staffCost($user_level)
    {
        $hours = $this->hours_spend();
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

        $staffCost = [];
        $staffCost['low'] = $hours['low'] * $low_wage;
        $staffCost['mid'] = $hours['mid'] * $high_wage;
        $staffCost['hight'] = $hours['hight'] * $very_high_wage;
        $staffCost['saturday'] = $hours['saturday'] * $saturday_wage;
        $staffCost['sunday'] = $hours['sunday'] * $sunday_wage;
        $staffCost['publicHoliday'] = $hours['publicHoliday'] * $public_holiday_wage;
        
        return $staffCost;
    }

	private function get_highest_time()
	{
		if($this->publicHoliday)
		{
			return 'publicHoliday';
		}
		elseif($this->sunday)
		{
			return 'sunday';
		}
		elseif($this->saturday)
		{
			return 'saturday';
		}
		elseif($this->hight)
		{
			return 'hight';
		}
		elseif($this->mid)
		{
			return 'mid';
		}
		else
		{
			return 'low';
		}
	}

	private function hours_spend()
	{	$hours_spend = [];

		$hours_spend['low'] = $this->low;
		$hours_spend['mid'] = $this->mid;
		$hours_spend['hight'] = $this->hight;
		$hours_spend['saturday'] = $this->saturday;
		$hours_spend['sunday'] = $this->sunday;
		$hours_spend['publicHoliday'] = $this->publicHoliday;
		$hours_spend['bonus'] = $this->bonus;
		$this->total = ($this->low + $this->mid + $this->hight + $this->saturday + $this->sunday + $this->publicHoliday) - $this->convertStringToHour($this->break);
		$hours_spend['total'] = $this->total;

		return $hours_spend;
	}

	private function check_hour($hour)
	{
		if(7 <= $hour && $hour <= 19)
		{
			return 'low';
		}
		elseif(19 < $hour && $hour < 24)
		{
			return 'mid';
		}
		elseif(0 <= $hour && $hour < 7)
		{
			return 'hight';
		}
	}

	private function check_day()
	{
		$this->day = date('l',strtotime($this->date));

		if($this->is_public_holiday($this->date))
		{
			$this->publicHoliday_case(); 
		}
		else
		{
			switch($this->day)
			{
				case 'Friday':
					$this->week_case();
				break;

				case 'Saturday':
					$this->week_end_case();
				break;

				case 'Sunday':
					$this->week_end_case();
				break;

				default:
					$this->week_case();
				break;
			}
		}
	}

	public function is_public_holiday()
    {
        $date = date('d/m/Y', strtotime($this->date));
        $year = date('Y', strtotime($this->date));

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


	private function convertStringToHour($hour)
	{
		if(strpos($hour,'min') || strpos($hour,' min'))
		{
			$hour = str_replace('min','',$hour);
		}
		if(strpos($hour,'"'))
		{
			$hour = str_replace('"','',$hour);
		}
		if(strpos($hour,'.'))
		{
			$part = explode('.', $hour);
			if($part[1] == 3)
			{
				$part[1] = $part[1]*10;
			}
            $time = $part[0] + floor(($part[1]/60)*100) / 100;
            return $time;
		}
		if(strpos($hour,':'))
		{
			$part = explode(':', $hour);
			if($part[1] == 3)
			{
				$part[1] = $part[1]*10;
			}
            $time = $part[0] + floor(($part[1]/60)*100) / 100;
            return $time;
		}
		return floatval($hour/60);
	}

	private function week_case()
	{

		if($this->startStatus == 'low')
		{
			if($this->endStatus  == 'low')
			{
				$this->low = $this->end - $this->start;
			}
			elseif($this->endStatus == 'mid')
			{
				$this->low = 19 - $this->start;
				$this->mid = $this->end - 19;
			}
			elseif($this->endStatus == 'hight')
			{
				$this->low = 19 - $this->start;
				$this->mid = 24 - 19;
				if($this->day == 'Friday')
				{
					$this->saturday = $this->end;
				}
				else
				{
					$this->hight = $this->end;
				}
			}
		}
		elseif($this->startStatus == 'mid')
		{
			if($this->endStatus == 'mid')
			{
				$this->mid = $this->end - $this->start;
			}
			elseif($this->endStatus == 'hight')
			{
				$this->mid = 24 - $this->start;
				if($this->day == 'Friday')
				{
					$this->saturday = $this->end;
				}
				else
				{
					$this->hight = $this->end;
				}
			}
			elseif($this->endStatus == 'low')
			{
				$this->mid = 24 - $this->start;
				if($this->day == 'Friday')
				{
					$this->saturday = $this->end;
				}
				else
				{
					$this->hight = 7;
					$this->low = $this->end - 7;
				}
			}
		} elseif($this->startStatus == 'hight')
		{
			if($this->endStatus == 'low')
			{
				$this->hight = 7 - $this->start;
				$this->low = $this->end - 7;
			}
		}
	}

	private function week_end_case()
	{
		if($this->startStatus == 'low' || $this->startStatus == 'mid')
		{
			if($this->endStatus  == 'hight')
			{
				if($this->day == 'Saturday')
				{
					$this->saturday = 24 - $this->start;
					$this->sunday = $this->end;
				}
				elseif($this->day == 'Sunday')
				{
					$this->sunday = (24 - $this->start) + $this->end;
				}
			}
			else
			{
				if($this->day == 'Saturday')
				{
					$this->saturday = $this->end - $this->start;
				}
				elseif($this->day == 'Sunday')
				{
					$this->sunday = $this->end - $this->start;
				}
			}
		}
	}	

	private function public_holiday_case()
	{
		if($this->startStatus == 'low' || $this->startStatus == 'mid')
		{
			if($this->endStatus  == 'hight')
			{
				if($this->day == 'Friday')
				{
					$this->publicHoliday = 24 - $this->start;
					$this->saturday = $this->end;
				}
				elseif($this->day == 'Saturday')
				{
					$this->publicHoliday = 24 - $this->start;
					$this->sunday = $this->end;
				}
				else
				{
					$this->publicHoliday = 24 - $this->start;
					$this->low = $this->end;
				}
			}
			else
			{
				$this->publicHoliday = $this->end - $this->start;
			}
		}
	}
}