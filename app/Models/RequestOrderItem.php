<?php

// app/Models/RequestOrderItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_order_id',
        'item_name',
        'quantity',
        'unit',
    ];

    /**
     * Relasi ke Request Order
     */
    public function requestOrder()
    {
        return $this->belongsTo(RequestOrder::class);
    }
}
