<?php 
namespace App\Services;

use DateTime;
use App\User;
use App\Client;
use App\Event;
use App\Notification;

class NotificationService {

	public function notifyClient($message,Event $event){
		if(!$this->is_notification_exists($event)){
			$notification = Notification::create([
				'message'	=>	$message,
				'client_id'	=>	$event->client->id,
				'event_id'	=>	$event->id
			]);
			$this->addDateToMessage($notification);
		} else {
			$notification = Notification::where('event_id','=',$event->id)->where('client_id','=',$event->client->id)->first();
			$notification->message = $message;
			$notification->client_id = $event->client->id;
			$notification->save();
			$this->addDateToMessage($notification);
		}
	}

	public function addDateToMessage(Notification $notification){
		$notification->message = $notification->message.' on '.date('d/m/Y H:i:s', strtotime($notification->updated_at.'+10 hour'));
		$notification->save();
	}

	public function is_notification_exists(Event $event){
		$notification = $notification = Notification::where('event_id','=',$event->id)->where('client_id','=',$event->client->id)->first();
		if($notification){
			return true;
		} else {
			return false;
		}

	} 

	public function getClientNotification(Event $event){
		return $notification = Notification::where('event_id','=',$event->id)->where('client_id','=',$event->client->id)->first();
	}

}