<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopUpOrder extends Model
{
    use HasFactory;

    public const STATUSES = [
        'pending' => 'Menunggu konfirmasi',
        'processing' => 'Sedang diproses',
        'completed' => 'Selesai',
        'rejected' => 'Ditolak',
    ];

    protected $fillable = [
        'user_id',
        'order_code',
        'game',
        'player_id',
        'zone_id',
        'package_key',
        'package_name',
        'diamonds',
        'price',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? 'Status tidak diketahui';
    }
}
