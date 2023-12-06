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

        Schema::create('order_status_requirements', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->nullable();
            $table->string('order_status')->nullable();
            $table->string('deposit_received')->nullable();
            $table->string('deposit_amount')->nullable();
            $table->string('freight_forwarder')->nullable();
            $table->string('best_rate_received')->nullable();
            $table->string('shipping_line')->nullable();
            $table->string('acid_received')->nullable();
            $table->string('acid_number')->nullable();
            $table->string('booking_completed')->nullable();
            $table->string('erd')->nullable();
            $table->date('sailing_date')->nullable();
            $table->string('truker_name')->nullable();
            $table->string('trucker_quote')->nullable();
            $table->date('load_date')->nullable();
            $table->string('pre_shipped')->nullable();
            $table->string('release_notes')->nullable();
            $table->string('pre_shipping_docs')->nullable();
            $table->string('item_shipped_scanned_out')->nullable();
            $table->string('final_doc_submitted_to_ff')->nullable();
            $table->string('nexpac_report_sent')->nullable();
            $table->string('ktc_report_sent')->nullable();
            $table->string('customer_email_all_paper_work')->nullable();
            $table->string('nextrade_invoicing')->nullable();
            $table->string('obelete_report_updated')->nullable();
            $table->string('final_bl_draft_to_customer')->nullable();
            $table->string('release_requested')->nullable();
            $table->string('bl_received')->nullable();
            $table->string('final_document_to_bank')->nullable();
            $table->string('final_document_to_customer')->nullable();
            $table->string('final_document_to_cargox')->nullable();
            $table->string('final_payment')->nullable();
            $table->string('ff_invoivce')->nullable();
            $table->string('ff_paid')->nullable();
            $table->date('ff_date_paid')->nullable();
            $table->string('ff_invoice')->nullable();
            $table->string('trucker_paid')->nullable();
            $table->date('trucker_date')->nullable();
            $table->string('final_payment_closed')->nullable();
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
        Schema::dropIfExists('order_status_requirements');
    }
};
