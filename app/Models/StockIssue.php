<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockIssue extends Model
{
    public const REASONS = [
        'kitchen' => 'Dùng trong bếp',
        'waste'   => 'Hao hụt',
        'spoiled' => 'Hỏng / hết hạn',
        'discard' => 'Hủy bỏ',
        'other'   => 'Khác',
    ];

    protected $fillable = ['code', 'reason', 'issued_at', 'total_amount', 'note', 'created_by'];

    protected $casts = [
        'issued_at'    => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function creator()   { return $this->belongsTo(User::class, 'created_by'); }
    public function items()     { return $this->hasMany(StockIssueItem::class); }
    public function movements() { return $this->morphMany(StockMovement::class, 'reference'); }

    public function getReasonLabelAttribute(): string
    {
        return self::REASONS[$this->reason] ?? $this->reason;
    }
}
