<?php 
namespace App\Services;

use Illuminate\Support\Facades\Auth;

use App\Http\Requests;
use App\Modification;
use App\User;
use App\Role;
use App\Event;
use App\BarEvent;
use App\Assignment;

class Modifications 
{

    private $event;
    private $barEvent;
    private $outStocks;
    protected $start_time;
    protected $last_change;

    public function __construct(Event $event)
    {
        $this->event = $event->replicate();
        if($event->bar && $event->barEvent !== null)
        {
            $this->barEvent = $event->barEvent->replicate();
        }
        if(count($event->outStock) > 0)
        {
            $this->outStocks = [];
            $outStocks = $event->outStock;
            foreach ($outStocks as $key => $outStock) 
            {
                $this->outStocks[$key] = $outStock->replicate();
            }
        }
    }

    public function get(Event $event)
    {
        return $modifications = Modification::where('event_id','=',$event->id)->get();
    }

    public function getLast(Event $event)
    {
        return $modifications = Modification::where('event_id','=',$event->id)->orderBy('created_at','DESC')->first();
    }

    public function checkUpdates(Event $event)
    {   
        $eventAttributes = $event->getAttributes();
        
        foreach ($eventAttributes as $key => $value) 
        {
            if(is_array($this->event->$key))
            {
                $value = str_replace('["','',$value); 
                $value = str_replace('"]','',$value);

                foreach ($this->event->$key as $old) 
                {
                    if($old != $value)
                    {
                        $key = str_replace('_',' ',$key);
                        $this->create($event->id,$key,$old,$value);
                        $this->start_time = true;
                    }
                }
            }
            elseif($this->event->$key != $value)
            {
                if($key != "id" && $key != "updated_at" && $key != "created_at" && $key != "booking_date")
                {
                    if(!is_array($this->event->$key) && !is_array($value) && !is_object($this->event->$key) && !is_object($value))
                    {  
                        //$this->create($event->id,$key,$this->event->$key,$value);
                        echo "Key ".$key." => value ".$value." old value ".$this->event->$key;
                    }
                }
                $this->last_change = $key;
            }
        }
        // Check bar Event values
        if($event->bar && $event->barEvent !== null)
        {
            $barEventAttributes = $event->barEvent->getAttributes();
            foreach ($barEventAttributes as $barKey => $barValue) 
            {
                if($this->barEvent->$key != $barValue)
                {
                    if($barKey != 'id' && $barKey != 'event_id' && $barKey != "updated_at" && $barKey != "created_at")
                    {
                        if($this->event->$barKey != "" && $barValue != 0)
                        {
                            $this->create($event->id,$barKey,$this->event->$barKey,$barValue);
                        }
                    }
                }
                $this->last_change = $barKey;
            }
        }


        if(count($event->outStock) > 0)
        {
            $old_stock = $this->outStocks;
            $new_stock = $event->outStock;

            //Two collections are obtained
            // We need to compare them and keep only the different key=>values sets to register them in a Modification object

            // CLue: register in old objects all the objects from the outStock collection in a private variable.
            //When calling the new collection, use foreach and compare each objects to their replication
            // If the object is the same, then pass if the object is different then show up the differences. 
        }
    }

    public function create($eventId,$key,$old_value,$new_value)
    {
    	$role = Role::find(Auth::user()->role_id)->first();

    	Modification::create([
                'name'          =>  Auth::user()->username,
                'role'          =>  $role->name,
                'modifications' =>  $key,
                'event_id'      =>  $eventId,
                'old_value'     =>  $old_value,
                'new_value'     =>  $new_value
            ]);
    }

    public function backUp(Event $event,$modificationId)
    {   
        $modification = Modification::find($modificationId);
        $key = $modification->modifications;
        if($event->$key !== null)
        {
            $event->$key = $modification->old_value;
            $event->save();
            $modification->delete();
        }
        elseif($event->barEvent->$key !== null)
        {
            $barEvent = $event->barEvent;
            $barEvent->$key = $modification->old_value;
            $barEvent->save();
            $modification->delete();
        }
    }

    public function emailSubject()
    {
        return $subject = 'Update : '.$this->last_change.' Updated !';
    }
}