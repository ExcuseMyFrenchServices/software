<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddServicesToEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('events', function(Blueprint $table)
		{
			$table->boolean('glasses')->after('uniform');
			$table->boolean('soft_drinks')->after('glasses');
			$table->boolean('bar')->after('soft_drinks');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('events', function(Blueprint $table)
		{
			$table->dropColumn('glasses');
			$table->dropColumn('soft_drinks');
			$table->dropColumn('bar');
		});
	}

}
