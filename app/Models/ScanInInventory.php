<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScanInInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'supplier_id',
        'warehouse_id',
        'nexpac_bill',
    ];

    public function getScanLogs(){
        return $this->hasMany(ScanInLog::class, 'scan_in_inventory_id', 'id');
    }

    public function warehouse(){
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

    public function supplier(){
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }

}
