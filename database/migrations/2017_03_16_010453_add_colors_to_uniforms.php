<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColorsToUniforms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uniforms', function (Blueprint $table) {
            $table->string('jacket_color',25)->after('jacket');
            $table->string('shirt_color',25)->after('shirt');
            $table->string('pant_color',25)->after('pant');
            $table->string('shoes_color',25)->after('shoes');
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
            $table->dropColumn('jacket_color');
            $table->dropColumn('shirt_color');
            $table->dropColumn('pant_color');
            $table->dropColumn('shoes_color');
        });
    }
}
