<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniformTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uniforms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('jacket',50);
            $table->string('shirt',50);
            $table->string('pant',50);
            $table->string('shoes',50);
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
        Schema::drop('uniforms');
    }
}
