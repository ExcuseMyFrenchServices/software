<?php
namespace App\Services;

use App\Assignment;
use App\Availability;
use App\Event;
use App\User;
use DateTime;
use Illuminate\Support\Facades\DB;

class UsersMissions 
{       
    public function getUserMissions($userId)
    {    
        return $userMissions = DB::table('assignments')
                    		->select('users.username','clients.name', DB::raw('count(clients.name) as time_worked_for'))
                    		->join('users', 'users.id', "=", "assignments.user_id")
                    		->join('events', 'events.id', '=', 'assignments.event_id')
                    		->join('clients', 'clients.id', '=', 'events.client_id')
                    		->where('user_id', $userId)
                    		->groupBy('clients.name')
                    		->orderBy('time_worked_for', 'DESC')
                    		->get();
    }

    public function getMissionsForClient($clientName, $userId)
    {
        return $missionsForClient = 
                            DB::table('assignments')
                            ->select(DB::raw('count(clients.name) as time_worked_for'))
                            ->join('users', 'users.id', "=", 'assignments.user_id')
                            ->join('events', 'events.id', '=', 'assignments.event_id')
                            ->join('clients', 'clients.id', '=', 'events.client_id')
                            ->where('clients.name', $clientName)
                            ->where('users.id', $userId)
                            ->get();        
    }

    public function getUserTotalMissions($userId)
    {
    	return $total = DB::table('assignments')
               			->select('clients.name')
                		->join('events', 'events.id', '=', 'assignments.event_id')
                		->join('clients', 'clients.id', '=', 'events.client_id')
                		->where('user_id', $userId)
                		->count();
    }

    public function getUserBestClient($userId)
    {
    	return $best_client = DB::table('assignments')
                		    ->select('clients.name', DB::raw('count(clients.name) as time_worked_for'))
                    		->join('events', 'events.id', '=', 'assignments.event_id')
                    		->join('clients', 'clients.id', '=', 'events.client_id')
                    		->where('user_id', $userId)
                    		->groupBy('clients.name')
                    		->orderBy('time_worked_for', 'DESC')
                    		->first();
    }
}