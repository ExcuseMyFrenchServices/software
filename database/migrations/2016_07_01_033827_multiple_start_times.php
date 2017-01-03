<?php

use App\Event;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class MultipleStartTimes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function ($table) {
            $table->string('start_time', 1000)->change();
        });

        Event::all()->each(function ($event) {
            $event->start_time = [$this->convertTo24($event->getOriginal('start_time'))];
            $event->finish_time = $this->convertTo24($event->finish_time);

            $event->save();
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

    private function convertTo24($time)
    {
        if (empty($time)) {
            return '';
        }

        list($time, $meridian) = explode(' ', $time);

        list($hours, $minutes) = explode(':', $time);

        if ($meridian == 'PM') {
            $hours = $hours + 12;
        }

        if ($hours == 24) {
            $hours = 12;
        }

        return $hours . ':' . $minutes;
    }
}
