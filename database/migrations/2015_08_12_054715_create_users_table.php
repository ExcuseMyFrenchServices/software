<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->engine = "InnoDB";

			$table->increments('id');
			$table->string('username')->unique();
			$table->string('password', 60);
			$table->integer('role_id')->unsigned();
			$table->string('hash')->nullable();
			$table->rememberToken();
			$table->timestamps();

			$table->foreign('role_id')->references('id')->on('roles');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}