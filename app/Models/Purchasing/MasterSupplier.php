<?php

namespace App\Models\Purchasing;

use Illuminate\Database\Eloquent\Model;

class MasterSupplier extends Model
{
    protected $table = 'master_supplier';

    protected $fillable = [
        'code',
        'name',
        'type',
        'contact_person',
        'address',
        'phone',
        'email',
    ];

    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'supplier_id');
    }
}
