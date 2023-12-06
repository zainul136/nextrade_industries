<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusRequirement extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'order_status',
        'deposit_received',
        'deposit_amount',

        'freight_forwarder',
        'best_rate_received',
        'shipping_line',
        'acid_received',
        'acid_number',
        'booking_completed',
        'erd',
        'sailing_date',
        'truker_name',
        'trucker_quote',
        'load_date',
        'pre_shipped',
        'release_notes',
        'pre_shipping_docs',

        'item_shipped_scanned_out',

        'final_doc_submitted_to_ff',
        'nexpac_report_sent',
        'ktc_report_sent',
        'customer_email_all_paper_work',
        'nextrade_invoicing',
        'obelete_report_updated',

        'final_bl_draft_to_customer',
        'release_requested',
        'bl_received',
        'final_document_to_bank',
        'final_document_to_customer',
        'final_document_to_cargox',
        'final_payment',

        'ff_invoivce',
        'ff_paid',
        'ff_date_paid',
        'ff_invoice',
        'trucker_paid',
        'trucker_date',
        'final_payment_closed'
    ];
}
