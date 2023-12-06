<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'product_type',
        'slug'
    ];

    /**
     * Get the collection of data
     */
    public function getAllProductTypes(): Collection
    {
        return $this->all();
    }

    /**
     * Get the specified record
     */
    public function getProductType($id)
    {
        return $this->where('id', $id)->first();
    }

    /**
     * Get the specified column value
     */
    public function getProductTypeColumnValue($id, $attribute): string
    {
        return $this->where('id', $id)->value($attribute);
    }

    /**
     * Store product type details in storage
     */
    public function createProductType(array $productTypeDetails)
    {
        return $this->create($productTypeDetails);
    }

    /**
     * Update specified product type details in storage
     */
    public function updateProductType($id, array $productTypeDetails)
    {
        return $this->find($id)->update($productTypeDetails);
    }

    /**
     * Delete specified product type details from storage
     */
    public function destroyProductType($id)
    {
        return $this->find($id)->delete();
    }
}
