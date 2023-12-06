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
        Schema::create('scan_in_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('scan_in_inventory_id')->nullable();
            $table->string('unit')->nullable();
            $table->string('skew_number')->nullable();
            $table->string('cgt')->nullable();
            $table->string('nt')->nullable();
            $table->string('product_type')->nullable();
            $table->string('color')->nullable();
            $table->string('rolls')->nullable();
            $table->string('weight')->nullable();
            $table->string('yards')->nullable();
            $table->string('cgt_price')->nullable();
            $table->string('cgt_pnl')->nullable();
            $table->boolean('is_scan_out')->default(0);
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
        Schema::dropIfExists('scan_in_logs');
    }
};
