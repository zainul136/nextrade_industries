<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'contact',
        'email',
        'country',
        'product',
        'address'
    ];

    /**
     * Get the collection of data
     */
    public function getAllSuppliers(): Collection
    {
        return $this->all();
    }

    /**
     * Get the specified record
     */
    public function getSupplier($id)
    {
        return $this->where('id', $id)->first();
    }

    /**
     * Get the specified column value
     */
    public function getSupplierColumnValue($id, $attribute): string
    {
        return $this->where('id', $id)->value($attribute);
    }

    /**
     * Store supplier details in storage
     */
    public function createSupplier(array $supplierDetails)
    {
        return $this->create($supplierDetails);
    }

    /**
     * Update specified supplier details in storage
     */
    public function updateSupplier($id, array $supplierDetails)
    {
        return $this->find($id)->update($supplierDetails);
    }

    /**
     * Delete specified supplier details from storage
     */
    public function destroySupplier($id)
    {
        return $this->find($id)->delete();
    }
}
