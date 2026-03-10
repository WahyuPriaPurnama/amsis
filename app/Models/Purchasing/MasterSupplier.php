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
        'npwp',
        'bank_name',
        'bank_account_number',
        'term_of_payment',
    ];

    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'supplier_id');
    }
}
