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
        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id')->nullable();
            $table->tinyInteger('users')->default(0);
            $table->tinyInteger('roles')->default(0);
            $table->tinyInteger('warehouses')->default(0);
            $table->tinyInteger('customers')->default(0);
            $table->tinyInteger('suppliers')->default(0);
            $table->tinyInteger('cgt_gardes')->default(0);
            $table->tinyInteger('nt_grades')->default(0);
            $table->tinyInteger('colors')->default(0);
            $table->tinyInteger('product_types')->default(0);
            $table->tinyInteger('scan_in')->default(0);
            $table->tinyInteger('scan_out')->default(0);
            $table->tinyInteger('inventory')->default(0);
            $table->tinyInteger('orders')->default(0);
            $table->tinyInteger('reports')->default(0);
            $table->tinyInteger('nt_grade_column')->default(0);
            $table->tinyInteger('nt_price_column')->default(0);
            $table->tinyInteger('third_party_price_column')->default(0);
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
        Schema::dropIfExists('role_has_permissions');
    }
};
