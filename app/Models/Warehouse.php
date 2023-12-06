<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'contact',
        'location'
    ];

    /**
     * Get the collection of data
     */
    public function getAllWarehouses(): Collection
    {
        return $this->orderBy('id','desc')->get();
    }

    /**
     * Get the specified record
     */
    public function getWarehouse($id)
    {
        return $this->where('id', $id)->first();
    }

    /**
     * Get the specified column value
     */
    public function getWarehouseColumnValue($id, $attribute): string
    {
        return $this->where('id', $id)->value($attribute);
    }

    /**
     * Store warehouse details in storage
     */
    public function createWarehouse(array $warehouseDetails)
    {
        return $this->create($warehouseDetails);
    }

    /**
     * Update specified warehouse details in storage
     */
    public function updateWarehouse($id, array $warehouseDetails)
    {
        return $this->find($id)->update($warehouseDetails);
    }

    /**
     * Delete specified warehouse details from storage
     */
    public function destroyWarehouse($id)
    {
        return $this->find($id)->delete();
    }
}
