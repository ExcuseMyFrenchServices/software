<?php

use App\Client;
use App\Event;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClientsFromEvents extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$events = Event::all();

		foreach ($events as $event) {
			$client = Client::where('email', '=', $event->client_email)->first();

			if (!$client) {

				$client = Client::create([
					'name' => $event->client_name,
					'email' =>$event->client_email,
					'phone_number' => $event->phone_number
				]);

			}

			$event->client_id = $client->id;
			$event->save();
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
