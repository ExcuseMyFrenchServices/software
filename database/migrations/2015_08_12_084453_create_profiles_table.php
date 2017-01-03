<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('profiles', function(Blueprint $table)
		{
			$table->engine = "InnoDB";

			$table->increments('id');
			$table->string('email', 100)->unique();
			$table->string('first_name', 100);
			$table->string('last_name', 100);
			$table->string('phone_number', 30)->nullable();
			$table->string('rsa_number', 20)->nullable();
			$table->string('drivers_license', 20)->nullable();
			$table->boolean('has_car')->nullable();
			$table->string('address')->nullable();
			$table->string('shirt_size', 20)->nullable();
			$table->string('pant_size', 20)->nullable();
			$table->string('shoe_size', 20)->nullable();
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
		Schema::drop('profiles');
	}

}