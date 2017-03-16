<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSetNameToUniforms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uniforms', function (Blueprint $table) {
            $table->string('set_name',50)->before('jacket');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uniforms', function (Blueprint $table) {
            $table->dropColumn('set_name');
        });
    }
}
