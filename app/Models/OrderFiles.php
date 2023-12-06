<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderFiles extends Model
{
    use HasFactory;
    protected $fillable = [
        'scan_out_inventory_id',
        'file_name'
    ];

}
