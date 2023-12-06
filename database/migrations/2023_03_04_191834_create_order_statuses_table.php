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
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->integer('scan_out_inventory_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->enum('previous_status', ['pending', 'preload', 'shipping_in_process', 'shipped', 'post_loading_documentation', 'end_stage', 'closed', 'cancelled'])->default('preloaded');
            $table->enum('changed_to', ['pending', 'preload', 'shipping_in_process', 'shipped', 'post_loading_documentation', 'end_stage', 'closed', 'cancelled'])->default('preloaded');
            $table->tinyInteger('deposit_received')->default(0);
            $table->tinyInteger('rate_received')->default(0);
            $table->tinyInteger('rate_approved')->default(0);
            $table->tinyInteger('rate_quote')->default(0);
            $table->tinyInteger('acid_received')->default(0);
            $table->tinyInteger('booking_completed')->default(0);
            $table->tinyInteger('erd')->default(0);
            $table->tinyInteger('sailing_date')->default(0);
            $table->tinyInteger('arrival_date')->default(0);
            $table->tinyInteger('truker_name')->default(0);
            $table->tinyInteger('trucker_quote')->default(0);
            $table->tinyInteger('load_date')->default(0);
            $table->tinyInteger('item_shipped')->default(0);
            $table->tinyInteger('pre_shipped')->default(0);
            $table->tinyInteger('preliminary_doc')->default(0);
            $table->tinyInteger('release_notes')->default(0);
            $table->tinyInteger('shipment_loaded')->default(0);
            $table->tinyInteger('final_shipping_doc')->default(0);
            $table->tinyInteger('nextpac_report')->default(0);
            $table->tinyInteger('ktc_report')->default(0);
            $table->tinyInteger('cus_paperwork_completed')->default(0);
            $table->tinyInteger('nextrade_invoicing')->default(0);
            $table->tinyInteger('obselete_report')->default(0);
            $table->tinyInteger('final_payment_received')->default(0);
            $table->tinyInteger('final_bl_draft')->default(0);
            $table->tinyInteger('release_requested')->default(0);
            $table->tinyInteger('bl_requested')->default(0);
            $table->tinyInteger('final_doc_to_bank')->default(0);
            $table->tinyInteger('final_doc_to_customer')->default(0);
            $table->tinyInteger('final_doc_to_cargoX')->default(0);
            $table->tinyInteger('ff_invoice')->default(0);
            $table->tinyInteger('ff_paid')->default(0);
            $table->tinyInteger('ff_date_paid')->default(0);
            $table->tinyInteger('trucker_invoice')->default(0);
            $table->tinyInteger('trucker_paid')->default(0);
            $table->tinyInteger('trucker_date')->default(0);
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
        Schema::dropIfExists('order_statuses');
    }
};
