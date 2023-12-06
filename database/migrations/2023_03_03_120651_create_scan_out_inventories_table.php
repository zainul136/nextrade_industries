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
        Schema::create('scan_out_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('release_number')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('warehouse_id')->nullable();
            $table->string('container')->nullable();
            $table->string('tear_factor')->nullable();
            $table->string('seal')->nullable();
            $table->string('pallet_weight')->nullable();
            $table->string('tear_factor_weight')->nullable();
            $table->string('scale_discrepancy')->nullable();
            $table->enum('status', ['pending', 'preload', 'shipping_in_process', 'shipped', 'post_loading_documentation', 'end_stage', 'closed', 'cancelled'])->default('preloaded');
            $table->integer('is_order_pending')->nullable();
            $table->tinyInteger('pending_order_complete')->default(0);
            $table->integer('pallet_on_container')->nullable();
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
        Schema::dropIfExists('scan_out_inventories');
    }
};
