<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Role;

class AddNewRoles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		Role::create(['name' => 'Bar staff']);
		Role::create(['name' => 'Senior staff']);
		Role::create(['name' => 'Board Room Attendant']);
		Role::create(['name' => 'Barista']);
		Role::create(['name' => 'Team Leader']);
		Role::create(['name' => 'Bartender']);
		Role::create(['name' => 'Supervisor']);
		Role::create(['name' => 'Manager']);

		$waiter = Role::where('name', '=', 'waiter')->first();
		$waiter->name = 'Wait staff';
		$waiter->save();

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
