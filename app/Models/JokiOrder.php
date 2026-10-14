<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JokiOrder extends Model
{
    use HasFactory;

    public const STATUSES = [
        'pending' => 'Menunggu penawaran harga',
        'quoted' => 'Harga dikirim',
        'processing' => 'Sedang dikerjakan',
        'completed' => 'Selesai',
        'rejected' => 'Ditolak',
    ];

    protected $fillable = [
        'user_id',
        'worker_id',
        'order_code',
        'game',
        'service_type',
        'player_nickname',
        'player_id',
        'zone_id',
        'current_rank',
        'target_rank',
        'notes',
        'quoted_price',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? 'Status tidak diketahui';
    }

    public function getServiceLabelAttribute(): string
    {
        return config("kirestore.joki.mobile_legends.services.{$this->service_type}", 'Joki Mobile Legends');
    }
}
