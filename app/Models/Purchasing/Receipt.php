<?php

namespace App\Models\Purchasing;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $table = 'receipts';

    protected $fillable = [
        'reference_number',
        'supplier_id',
        'arrival_date',
        'received_by',
        'notes',
    ];

    public function supplier()
    {
        return $this->belongsTo(MasterSupplier::class, 'supplier_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'receipt_id');
    }
}
