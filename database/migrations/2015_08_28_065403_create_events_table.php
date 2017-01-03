<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('events', function(Blueprint $table)
		{
			$table->engine = "InnoDB";

			$table->increments('id');
			$table->string('event_name', 100)->nullable();
			$table->string('client_name', 100);
			$table->string('client_email', 100);
			$table->string('phone_number', 30)->nullable();
			$table->dateTime('booking_date');
			$table->dateTime('event_date');
			$table->string('start_time');
			$table->string('finish_time')->nullable();
			$table->integer('number_staff');
			$table->string('address')->nullable();
			$table->string('uniform')->nullable();
			$table->string('notes')->nullable();
			$table->timestamps();

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('events');
	}

}
