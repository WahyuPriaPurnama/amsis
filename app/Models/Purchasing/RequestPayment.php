<?php

namespace App\Models\Purchasing;

use App\Models\HRD\Subsidiary;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RequestPayment extends Model
{
    protected $fillable = [
        'payment_number',
        'date',
        'division',
        'purpose',
        'grand_total',
        'status',
        'attachment',
        'requested_by',
        'approved_by_manager',
        'approved_by_bod',
        'subsidiary_id',
        'approved_by_manager_at',
        'approved_by_bod_at',
    ];

    public function items()
    {
        return $this->hasMany(RequestPaymentItem::class);
    }

    public function subsidiary()
    {
        return $this->belongsTo(Subsidiary::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function plantManager()
    {
        return $this->belongsTo(User::class, 'approved_by_manager');
    }

    public function bod()
    {
        return $this->belongsTo(User::class, 'approved_by_bod');
    }
}
