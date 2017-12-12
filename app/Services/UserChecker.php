<?php 
namespace App\Services;

use App\Assignment;
use App\Event;
use App\User;

class UserChecker
{
	public function checkUserMission($userId)
	{
		$user = User::find($userId);

		//Set the number of mission that needed to be done before creating a job alert
		$jobMissionLever = $this->levelRecommandation($user);
		$missionNumber = count($user->assignments);
		
		for ($i=0; $i < count($jobMissionLever); $i++) { 
			if($missionNumber >= $jobMissionLever[$i]) {
				if($user->level <= $i++)
				{
					$user->level_alert = $i++;
					$user->save();
				}
			}
		}
	}

	private function getConfidenceRatio($user)
	{
		$adminTimes = count( Event::where('admin_id',"=","user_id"));
		$missionNumber = count($user->assignments);

		if($missionNumber == 0){
			return $adminTimes;
		}
		return $confidenceRatio = $adminTimes / $missionNumber;
	}

	private function levelRecommandation($user)
	{
		$confidenceRatio = $this->getConfidenceRatio($user);
		$jobMissionLever = [10,40,100];

		foreach ($jobMissionLever as $key => $value) 
		{
			$jobMissionLever[$key] = $value* $confidenceRatio;
		}

		return $jobMissionLever;
	}
}

