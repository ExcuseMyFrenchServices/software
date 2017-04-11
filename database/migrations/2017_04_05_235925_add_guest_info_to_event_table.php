<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGuestInfoToEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('event_type',50)->after('event_date')->nullable();
            $table->string('guest_arrival_time',25)->before('start_time')->nullable();
            $table->integer('guest_number')->before('number_staff')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('event_type');
            $table->dropColumn('guest_arrival_time');
            $table->dropColumn('guest_number');
        });
    }
}
