<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_queues', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->nullable();
            $table->integer('color_id')->nullable();
            $table->integer('nt_id')->nullable();
            $table->string('order_column')->nullable();
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
        Schema::dropIfExists('order_queues');
    }
};
