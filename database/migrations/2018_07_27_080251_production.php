<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Production extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('green_production', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('uid');
            $table->tinyInteger('status')->default(10)->comment('用户当前状态 10正常 20封禁');
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
        //
    }
}
