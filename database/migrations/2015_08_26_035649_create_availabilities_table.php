<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvailabilitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('availabilities', function(Blueprint $table)
		{
			$table->engine = "InnoDB";

			$table->increments('id');
			$table->string('day');
			$table->string('times');
			$table->boolean('after_midnight');
			$table->integer('user_id')->unsigned();
			$table->timestamps();

			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('availabilities');
	}

}