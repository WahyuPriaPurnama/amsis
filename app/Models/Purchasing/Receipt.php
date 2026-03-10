<?php

namespace App\Models\Purchasing;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $table = 'goods_receipts';

    protected $fillable = [
        'receipt_number',
        'supplier_id',
        'receipt_date',
        'status',
    ];

    public function supplier()
    {
        return $this->belongsTo(MasterSupplier::class, 'supplier_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'goods_receipt_id');
    }
}
