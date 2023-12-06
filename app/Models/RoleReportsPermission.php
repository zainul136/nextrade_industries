<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleReportsPermission extends Model
{
    use HasFactory;
    protected $fillable = [
        'role_id',
        'inventory_report',
        'cgt_summary',
        'nt_summary',
        'color_summary',
        'commulative_cgt',
        'commulative_nt',
        'customer_summary',
        'nexpac_report',
        'internal_report',
        'billing_report',
        'pnl_report'
    ];

    public function roleName()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
}
