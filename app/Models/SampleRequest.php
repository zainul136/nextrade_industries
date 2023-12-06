<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampleRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'type',
        'status',
    ];


    public function getSamplerequests(): Collection
    {
        return $this->whereHas('getCustomer')->with('getCustomer')->orderBy('id', 'desc')->get();
    }

    public function getCustomer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id')->whereNotNull('id');
    }


    public function createSampleRequest(array $sampleDetails)
    {
        return $this->create($sampleDetails);
    }

    public function updateSampleRequest($id, array $sampleDetails)
    {
        return $this->find($id)->update($sampleDetails);
    }

    public function getSample($id)
    {
        return $this->find($id);
    }

    public function destroySample($id)
    {
        return $this->find($id)->delete();
    }
}
