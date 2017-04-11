<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBarEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bar_event', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id')->unsigned();
            $table->integer('private')->unsigned();
            $table->integer('bar_back')->unsigned();
            $table->integer('bar_runner')->unsigned();
            $table->integer('classic_bartender')->unsigned();
            $table->integer('cocktail_bartender')->unsigned();
            $table->integer('flair_bartender')->unsigned();
            $table->integer('mixologist')->unsigned();
            $table->string('glass_type',25)->nullable();
            $table->integer('bar_number')->unsigned();
            $table->integer('ice')->unsigned();
            $table->text('notes');
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
        Schema::drop('bar_event');
    }
}
