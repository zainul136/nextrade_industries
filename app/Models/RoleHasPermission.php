<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleHasPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'users',
        'roles',
        'warehouses',
        'customers',
        'suppliers',
        'cgt_gardes',
        'nt_grades',
        'colors',
        'product_types',
        'scan_in',
        'scan_out',
        'inventory',
        'orders',
        'reports',
        'nt_grade_column',
        'nt_price_column',
        'third_party_price_column'
    ];

    public function roleName()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
}
