<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderQueue extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'color_id',
        'nt_id',
        'order_column'
    ];
}
