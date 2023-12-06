<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScanInLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'scan_in_inventory_id',
        'unit',
        'skew_number',
        'cgt',
        'nt',
        'product_type',
        'color',
        'rolls',
        'weight',
        'yards',
        'cgt_price',
        'cgt_pnl'
    ];

    public function get()
    {
        return $this->hasMany(ScanInLog::class, 'scan_in_inventory_id', 'id');
    }

    public function sIProductName()
    {
        return $this->hasOne(ProductType::class, 'id', 'product_type');
    }
    public function sICGT()
    {
        return $this->hasOne(CGTGrade::class, 'id', 'cgt');
    }
    public function sINT()
    {
        return $this->hasOne(NTGrade::class, 'id', 'nt');
    }
    public function sIColor()
    {
        return $this->hasOne(Color::class, 'id', 'color');
    }

    public function getScanInInventory(){
        return $this->hasOne(ScanInInventory::class, 'id', 'scan_in_inventory_id');
    }

    public function getScanOutLogs(){
        return $this->hasOne(ScanOutLog::class, 'scan_in_id', 'id');
    }

    public function getNTByColor()
    {
        return $this->hasMany(NTGrade::class, 'id', 'nt');
    }
}
