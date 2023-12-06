<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScanOutLog extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'scan_out_inventory_id',
        'scan_in_id',
        'price',
        'third_party_price',
    ];

    public function scanOutInventory()
    {
        return $this->hasOne(ScanOutInventory::class, 'id', 'scan_out_inventory_id');
    }

    public function scanInLog()
    {
        return $this->hasOne(ScanInLog::class, 'id', 'scan_in_id');
    }

}
