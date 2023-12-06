<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NTGrade extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'grade_name',
        'slug',
    ];

    /**
     * Get the collection of data
     */
    public function getAllNtGrades(): Collection
    {
        return $this->all();
    }

    /**
     * Get the specified record
     */
    public function getNtGrade($id)
    {
        return $this->where('id', $id)->first();
    }

    /**
     * Get the specified column value
     */
    public function getNtGradeColumnValue($id, $attribute): string
    {
        return $this->where('id', $id)->value($attribute);
    }

    /**
     * Store nt grade details in storage
     */
    public function createNtGrade(array $ntGradeDetails)
    {
        return $this->create($ntGradeDetails);
    }

    /**
     * Update specified nt grade details in storage
     */
    public function updateNtGrade($id, array $ntGradeDetails)
    {
        return $this->find($id)->update($ntGradeDetails);
    }

    /**
     * Delete specified nt grade details from storage
     */
    public function destroyNtGrade($id)
    {
        return $this->find($id)->delete();
    }
}
