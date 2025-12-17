<?php
// app/Models/RequestOrder.php
namespace App\Models\Purchasing;

use App\Models\HRD\Subsidiary;
use App\Models\Purchasing\RequestOrderItem;
use App\Models\User;
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
        'approved_by_bod',
        'approved_by_divhead_at',
        'approved_by_manager_at',
        'approved_by_bod_at',
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
    public function plantManager()
    {
        return $this->belongsTo(User::class, 'approved_by_manager');
    }

    public function bod()
    {
        return $this->belongsTo(User::class, 'approved_by_bod');
    }

    public function subsidiary()
    {
        return $this->belongsTo(Subsidiary::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
    public function getStatusLabelAttribute(): string
    {
        if ($this->status === 'approved_by_div_head' && $this->subsidiary_id == 2) {
            return '<span class="badge bg-warning text-dark">Menunggu Persetujuan Plant Manager / BOD</span>';
        }

        return match ($this->status) {
            'pending' => '<span class="badge bg-warning text-dark">Menunggu Persetujuan Kepala Divisi</span>',
            'approved_by_div_head' => '<span class="badge bg-warning text-dark">Menunggu Persetujuan Plant Manager</span>',
            'approved_by_manager' => '<span class="badge bg-warning text-dark">Menunggu Persetujuan BOD</span>',
            'approved_by_bod' => '<span class="badge bg-success">Approved</span>',
            default => '<span class="badge bg-secondary">' . $this->status . '</span>',
        };
    }
}
