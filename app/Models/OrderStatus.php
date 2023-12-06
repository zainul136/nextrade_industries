<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'scan_out_inventory_id',
        'previous_status',
        'changed_to',
        'deposit_received',
        'rate_received',
        'rate_approved',
        'rate_quote',
        'acid_received',
        'acid_number',
        'booking_completed',
        'erd',
        'sailing_date',
        'arrival_date',
        'truker_name',
        'trucker_quote',
        'load_date',
        'item_shipped',
        'pre_shipped',
        'preliminary_doc',
        'release_notes',
        'shipment_loaded',
        'final_shipping_doc',
        'nextpac_report',
        'ktc_report',
        'cus_paperwork_completed',
        'nextrade_invoicing',
        'obselete_report',
        'final_payment_received',
        'final_bl_draft',
        'release_requested',
        'bl_requested',
        'final_doc_to_bank',
        'final_doc_to_customer',
        'final_doc_to_cargoX',
        'ff_invoice',
        'ff_paid',
        'ff_date_paid',
        'trucker_invoice',
        'trucker_paid',
        'trucker_date',
        'user_id'
    ];

    public function getUser()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
