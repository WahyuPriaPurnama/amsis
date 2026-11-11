<?php

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
        'attachment',          // Lampiran file per item saat pengajuan/pengeditan
        'date_received',       // Tanggal kedatangan barang
        'qty_received',        // Jumlah barang yang diterima
        'receipt_attachment',  // Bukti penerimaan/surat jalan
        'po_date',             // Tanggal Purchase Order
        'po_number',           // Nomor Purchase Order
    ];

    protected $casts = [
        'date_received' => 'date',
        'po_date' => 'date',
        'quantity' => 'decimal:2',
        'qty_received' => 'decimal:2',
    ];

    /**
     * Relasi ke Request Order Header
     */
    public function requestOrder()
    {
        return $this->belongsTo(RequestOrder::class);
    }
}
