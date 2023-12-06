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
        Schema::create('scan_in_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->integer('warehouse_id')->nullable();
            $table->string('nexpac_bill')->nullable();
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
        Schema::dropIfExists('scan_in_inventories');
    }
};
