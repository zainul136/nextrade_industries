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
        Schema::create('c_g_t_grades', function (Blueprint $table) {
            $table->id();
            $table->string('grade_name')->nullable();
            $table->string('slug')->nullable();
            $table->string('price')->nullable();
            $table->string('billing_code')->nullable();
            // $table->string('pnl')->nullable(); 
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
        Schema::dropIfExists('c_g_t_grades');
    }
};
