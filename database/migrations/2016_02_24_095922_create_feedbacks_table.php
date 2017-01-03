<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedbacksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('feedbacks', function(Blueprint $table)
		{
			$table->engine = "InnoDB";

			$table->increments('id');
			$table->integer('event_id')->unsigned();
			$table->integer('client_id')->unsigned();
			$table->integer('rating')->unsigned()->nullable();
			$table->string('comment')->nullable();
			$table->string('hash')->nullable();
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
		Schema::drop('feedbacks');
	}

}
