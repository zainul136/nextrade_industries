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
        Schema::create('role_reports_permissions', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id')->nullable();
            $table->tinyInteger('inventory_report')->default(0);
            $table->tinyInteger('cgt_summary')->default(0);
            $table->tinyInteger('nt_summary')->default(0);
            $table->tinyInteger('color_summary')->default(0);
            $table->tinyInteger('commulative_cgt')->default(0);
            $table->tinyInteger('commulative_nt')->default(0);
            $table->tinyInteger('customer_summary')->default(0);
            $table->tinyInteger('nexpac_report')->default(0);
            $table->tinyInteger('internal_report')->default(0);
            $table->tinyInteger('billing_report')->default(0);
            $table->tinyInteger('pnl_report')->default(0);
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
        Schema::dropIfExists('role_reports_permissions');
    }
};
