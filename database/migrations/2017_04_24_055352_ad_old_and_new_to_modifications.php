<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdOldAndNewToModifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modifications', function (Blueprint $table) {
            $table->string('old_value',100)->after('event_id')->nullable();
            $table->string('new_value',100)->after('event_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modifications', function (Blueprint $table) {
            $table->dropColumn('old_value');
            $table->dropColumn('new_value');
        });
    }
}
