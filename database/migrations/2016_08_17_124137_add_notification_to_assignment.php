<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNotificationToAssignment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function(Blueprint $table) {
            $table->dropColumn('staff_notification');
        });

        Schema::table('assignments', function(Blueprint $table) {
            $table->boolean('notification')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function(Blueprint $table) {
            $table->boolean('staff_notification')->default(false);
        });

        Schema::table('assignments', function(Blueprint $table) {
            $table->dropColumn('notification');
        });
    }
}
