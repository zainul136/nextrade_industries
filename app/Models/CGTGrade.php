<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CGTGrade extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'grade_name',
        'slug',
        'price',
        'billing_code',
        // 'pnl'
    ];

    /**
     * Get the collection of data
     */
    public function getAllCgtGrades(): Collection
    {
        return $this->all();
    }

    /**
     * Get the specified record
     */
    public function getCgtGrade($id)
    {
        return $this->where('id', $id)->first();
    }

    /**
     * Get the specified column value
     */
    public function getCgtGradeColumnValue($id, $attribute): string
    {
        return $this->where('id', $id)->value($attribute);
    }

    /**
     * Store cgt grade details in storage
     */
    public function createCgtGrade(array $cgtGradeDetails)
    {
        return $this->create($cgtGradeDetails);
    }

    /**
     * Update specified cgt grade details in storage
     */
    public function updateCgtGrade($id, array $cgtGradeDetails)
    {
        return $this->find($id)->update($cgtGradeDetails);
    }

    /**
     * Delete specified cgt grade details from storage
     */
    public function destroyCgtGrade($id)
    {
        return $this->find($id)->delete();
    }
}
