<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Color extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug'
    ];

    /**
     * Set slug attribute to lowercase letters
     * before saving in storage
     */
    public function slug(): Attribute
    {
        return new Attribute(
            set: fn ($value) => strtoupper($value)
        );
    }

    /**
     * Get the collection of data
     */
    public function getAllColors(): Collection
    {
        return $this->all();
    }

    /**
     * Get the specified record
     */
    public function getColor($id)
    {
        return $this->where('id', $id)->first();
    }

    /**
     * Get the specified column value
     */
    public function getColorColumnValue($id, $attribute): string
    {
        return $this->where('id', $id)->value($attribute);
    }

    /**
     * Store color details in storage
     */
    public function createColor(array $colorDetails)
    {
        return $this->create($colorDetails);
    }

    /**
     * Update specified color details in storage
     */
    public function updateColor($id, array $colorDetails)
    {
        return $this->find($id)->update($colorDetails);
    }

    /**
     * Delete specified color details from storage
     */
    public function destroyColor($id)
    {
        return $this->find($id)->delete();
    }
}
