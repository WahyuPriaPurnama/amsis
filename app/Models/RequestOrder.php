<?php
// app/Models/RequestOrder.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'subsidiary_id',
        'division',
        'request_date',
        'request_number',
        'purpose',
        'status',
        'requested_by',
        'approved_by_div_head',
        'approved_by_manager',  
        'approved_by_divhead_at',
        'approved_by_manager_at',
    ];

    /**
     * Relasi ke detail barang
     */
    public function items()
    {
        return $this->hasMany(RequestOrderItem::class);
    }

    /**
     * Relasi ke user pengaju
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Relasi ke kepala divisi
     */
    public function divHead()
    {
        return $this->belongsTo(User::class, 'approved_by_div_head');
    }

    /**
     * Relasi ke plant manager
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'approved_by_manager');
    }

    public function subsidiary()
    {
        return $this->belongsTo(Subsidiary::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by');
        // ganti 'requested_by' dengan 'user_id' kalau kolomnya bernama user_id
    }
}
