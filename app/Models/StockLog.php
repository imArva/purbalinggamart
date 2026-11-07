<?php

namespace App\Models;

use Database\Factories\StockLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLog extends Model
{
    /** @use HasFactory<StockLogFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'type',      // in | out | adjustment
        'quantity',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function getDeltaAttribute(): int
    {
        return $this->type === 'out' ? -abs($this->quantity) : $this->quantity;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
