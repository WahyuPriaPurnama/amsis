<?php

// app/Models/RequestOrderItem.php
namespace App\Models\Purchasing;

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
        'remark',
        'date_received',
        'qty_received',
        'receipt_attachment',
        'po_date',
        'po_number',
    ];

    /**
     * Relasi ke Request Order
     */
    public function requestOrder()
    {
        return $this->belongsTo(RequestOrder::class);
    }
}
