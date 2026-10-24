<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    public const UPDATED_AT = null;   // sổ cái chỉ ghi thêm

    public const TYPE_IN     = 'in';
    public const TYPE_OUT    = 'out';
    public const TYPE_ADJUST = 'adjust';

    public const TYPES = [
        self::TYPE_IN     => 'Nhập kho',
        self::TYPE_OUT    => 'Xuất kho',
        self::TYPE_ADJUST => 'Kiểm kê',
    ];

    protected $fillable = [
        'ingredient_id', 'type', 'quantity', 'balance_after', 'unit_cost',
        'reference_type', 'reference_id', 'note', 'user_id',
    ];

    protected $casts = [
        'quantity'      => 'decimal:3',
        'balance_after' => 'decimal:3',
        'unit_cost'     => 'decimal:2',
    ];

    public function ingredient() { return $this->belongsTo(Ingredient::class); }
    public function user()       { return $this->belongsTo(User::class); }
    public function reference()  { return $this->morphTo(); }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
