<?php

namespace App\Models;

use App\Models\Concerns\GeneratesCode;
use Illuminate\Database\Eloquent\Model;

/**
 * Nguyên liệu trong kho.
 *
 * LƯU Ý: `current_stock` là tồn kho NGUYÊN LIỆU, hoàn toàn tách biệt với `menus.stock`
 * (số phần món có thể bán). Cố ý KHÔNG đưa `current_stock` vào $fillable để không thể
 * sửa tồn bằng mass-assignment; chỉ các nghiệp vụ kho (giai đoạn sau) mới được đổi.
 */
class Ingredient extends Model
{
    use GeneratesCode;

    protected $fillable = [
        'code', 'name', 'category_id', 'unit',
        'min_stock', 'reference_price', 'is_active', 'note',
    ];

    protected $casts = [
        'current_stock'   => 'float',
        'min_stock'       => 'float',
        'reference_price' => 'float',
        'is_active'       => 'boolean',
    ];

    public static function newCode(): string
    {
        return static::nextCode('NL', 4);
    }

    public function category()
    {
        return $this->belongsTo(IngredientCategory::class, 'category_id');
    }

    /** Hết hàng: tồn <= 0 */
    public function getIsOutAttribute(): bool
    {
        return $this->current_stock <= 0;
    }

    /** Sắp hết: có đặt mức tối thiểu và tồn đã chạm/dưới mức đó (nhưng chưa hết) */
    public function getIsLowAttribute(): bool
    {
        return !$this->is_out && $this->min_stock > 0 && $this->current_stock <= $this->min_stock;
    }

    public function scopeSearch($q, ?string $keyword)
    {
        if ($keyword === null || trim($keyword) === '') {
            return $q;
        }
        $kw = '%' . trim($keyword) . '%';

        return $q->where(function ($w) use ($kw) {
            $w->where('code', 'like', $kw)->orWhere('name', 'like', $kw);
        });
    }

    /** Lọc tình trạng tồn: out | low | ok */
    public function scopeStockLevel($q, ?string $level)
    {
        return match ($level) {
            'out'   => $q->where('current_stock', '<=', 0),
            'low'   => $q->where('current_stock', '>', 0)
                         ->where('min_stock', '>', 0)
                         ->whereColumn('current_stock', '<=', 'min_stock'),
            'ok'    => $q->where('current_stock', '>', 0)
                         ->where(function ($w) {
                             $w->where('min_stock', '<=', 0)
                               ->orWhereColumn('current_stock', '>', 'min_stock');
                         }),
            default => $q,
        };
    }

    /** 1250.5 -> "1.250,5"; 12 -> "12" */
    public static function fmt($n): string
    {
        $s = number_format((float) $n, 3, ',', '.');

        return rtrim(rtrim($s, '0'), ',');
    }
}