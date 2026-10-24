<?php

namespace App\Models;

use App\Services\UnitConverter;
use Illuminate\Database\Eloquent\Model;

/** Một nguyên liệu trong công thức: định lượng cho MỘT phần ăn, theo đơn vị riêng của dòng. */
class RecipeItem extends Model
{
    protected $fillable = ['recipe_id', 'ingredient_id', 'quantity', 'unit'];

    protected $casts = [
        'quantity' => 'decimal:3',
    ];

    public function recipe()     { return $this->belongsTo(Recipe::class); }
    public function ingredient() { return $this->belongsTo(Ingredient::class); }

    /**
     * Định lượng của MỘT phần, đã quy đổi sang đơn vị của KHO (ví dụ công thức 150 g, kho tính kg => 0,15).
     * Ném InvalidArgumentException nếu đơn vị công thức không tương thích với đơn vị kho.
     */
    public function quantityInStockUnit(): float
    {
        return UnitConverter::convert((float) $this->quantity, $this->unit, $this->ingredient->unit);
    }
}
