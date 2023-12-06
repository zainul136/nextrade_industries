<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
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
    public function getAllCustomers(): Collection
    {
        return $this->orderBy('id','desc')->get();
    }

    /**
     * Get the specified record
     */
    public function getCustomer($id)
    {
        return $this->where('id', $id)->first();
    }

    /**
     * Get the specified column value
     */
    public function getCustomerColumnValue($id, $attribute): string
    {
        return $this->where('id', $id)->value($attribute);
    }

    /**
     * Store customer details in storage
     */
    public function createCustomer(array $customerDetails)
    {
        return $this->create($customerDetails);
    }

    /**
     * Update specified customer details in storage
     */
    public function updateCustomer($id, array $customerDetails)
    {
        return $this->find($id)->update($customerDetails);
    }

    /**
     * Delete specified customer details from storage
     */
    public function destroyCustomer($id)
    {
        return $this->find($id)->delete();
    }
}
