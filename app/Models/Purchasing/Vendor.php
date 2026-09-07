<?php

namespace App\Models\Purchasing;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Vendor extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'address',
        'nib',
        'npwp',
        'has_halal',
        'has_haccp',
        'skp_number',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'pic_name',
        'pic_email',
        'pic_phone',
        'nib_file',
        'npwp_file',
        'certificate_file',
        'status',
        'rejection_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
