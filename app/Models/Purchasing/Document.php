<?php

namespace App\Models\Purchasing;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'receipt_id',
        'type',
        'document_number',
        'file_path',
    ];

    public function receipt()
    {
        return $this->belongsTo(Receipt::class, 'receipt_id');
    }
}
