<?php

namespace App\Models\Purchasing;

use Illuminate\Database\Eloquent\Model;

class RequestPaymentItem extends Model
{
    protected $fillable = [
        'request_payment_id',
        'item_name',
        'quantity',
        'unit',
        'unit_price',
        'total_price',
    ];

    public function requestPayment()
    {
        return $this->belongsTo(RequestPayment::class);
    }
}
