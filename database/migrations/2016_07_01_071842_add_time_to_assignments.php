<?php

use App\Assignment;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddTimeToAssignments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assignments', function ($table) {
            $table->string('time', 1000)->after('event_id');
        });

        Assignment::all()->each(function ($assignment) {
            $assignment->time = $assignment->event->start_time[0];

            $assignment->save();
        });
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
