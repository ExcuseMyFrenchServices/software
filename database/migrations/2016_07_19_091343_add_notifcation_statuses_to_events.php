<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNotifcationStatusesToEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function(Blueprint $table) {
            $table->dropColumn('notification_status');
        });

        Schema::table('events', function(Blueprint $table) {
            $table->boolean('client_notification')->default(false);
            $table->boolean('staff_notification')->default(false);
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
            $table->dropColumn('client_notification');
            $table->dropColumn('staff_notification');
        });
    }
}
