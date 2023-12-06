<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScanOutInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'release_number',
        'customer_id',
        'warehouse_id',
        'container',
        'tear_factor',
        'seal',
        'status',
        'pallet_weight',
        'tear_factor_weight',
        'scale_discrepancy',
        'is_order_pending',
        'pallet_on_container'
    ];

    public function getScanOutLogs(){
        return $this->hasMany(ScanOutLog::class, 'scan_out_inventory_id', 'id');
    }

    public function getWareHouse(){
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

    public function getCustomers(){
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function allOrderStatuses(){
        return $this->hasMany(OrderStatus::class, 'scan_out_inventory_id', 'id');
    }

    public function OrderFiles(){
        return $this->hasMany(OrderFiles::class, 'scan_out_inventory_id', 'id');
    }
}
