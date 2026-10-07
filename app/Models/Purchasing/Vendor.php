<?php

namespace App\Models\Purchasing;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vendor extends Model
{
    use HasFactory;

    /**
     * Attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
        'contract_number',
        'contract_start_date',
        'contract_end_date',
        'contract_file',
        'status',
        'rejection_reason',
    ];

    /**
     * Attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'has_halal' => 'boolean',
        'has_haccp' => 'boolean',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
    ];

    /* -------------------------------------------------------------------------- */
    /*                                RELATIONSHIPS                               */
    /* -------------------------------------------------------------------------- */

    /**
     * Get the user associated with the vendor.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* -------------------------------------------------------------------------- */
    /*                               ACCESSORS & CASTS                            */
    /* -------------------------------------------------------------------------- */

    /**
     * Hitung sisa hari kontrak secara otomatis.
     * Mengembalikan nilai integer positif (sisa hari), 0 (hari ini), atau negatif (sudah expired).
     */
    protected function daysRemaining(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->contract_end_date
                ? (int) Carbon::now()->startOfDay()->diffInDays($this->contract_end_date, false)
                : null
        );
    }

    /**
     * Cek apakah kontrak sudah kadaluarsa.
     */
    protected function isExpired(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->contract_end_date
                ? $this->contract_end_date->isPast()
                : false
        );
    }

    /* -------------------------------------------------------------------------- */
    /*                                SCOPES                                      */
    /* -------------------------------------------------------------------------- */

    /**
     * Scope untuk menyaring vendor yang kontraknya akan habis dalam kurun waktu X hari.
     */
    public function scopeExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->whereNotNull('contract_end_date')
            ->where('contract_end_date', '>=', Carbon::now()->startOfDay())
            ->where('contract_end_date', '<=', Carbon::now()->addDays($days)->endOfDay());
    }

    /**
     * Scope untuk menyaring vendor yang kontraknya sudah habis (expired).
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('contract_end_date')
            ->where('contract_end_date', '<', Carbon::now()->startOfDay());
    }

    /**
     * Scope untuk menyaring vendor berdasarkan status approval.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'inactive');
    }
}
