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
        Schema::create('scan_out_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('scan_out_inventory_id')->nullable();
            $table->integer('scan_in_id')->nullable();
            $table->string('price')->nullable();
            $table->string('third_party_price')->nullable();
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scan_out_logs');
    }
};
