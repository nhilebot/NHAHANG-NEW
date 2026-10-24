<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Cửa DUY NHẤT để thay đổi Ingredient::current_stock.
 * Mọi nghiệp vụ (nhập, xuất, kiểm kê, trừ kho khi bán...) đều gọi qua đây
 * để luôn có một dòng sổ cái tương ứng.
 */
class StockService
{
    /**
     * @param float $signedQty Số lượng CÓ DẤU: dương = tăng tồn, âm = giảm tồn.
     */
    public function move(
        int $ingredientId,
        string $type,
        float $signedQty,
        ?Model $reference = null,
        ?float $unitCost = null,
        ?string $note = null,
        ?int $userId = null,
    ): StockMovement {
        return DB::transaction(function () use ($ingredientId, $type, $signedQty, $reference, $unitCost, $note, $userId) {
            // Khóa dòng để hai thao tác đồng thời không ghi đè nhau.
            $ingredient = Ingredient::whereKey($ingredientId)->lockForUpdate()->firstOrFail();

            $balance = round((float) $ingredient->current_stock + $signedQty, 3);
            if ($balance < 0) {
                throw new InvalidArgumentException(
                    "Không đủ tồn kho cho \"{$ingredient->name}\" (còn {$ingredient->current_stock} {$ingredient->unit})."
                );
            }

            // current_stock không nằm trong $fillable nên phải forceFill.
            $ingredient->forceFill(['current_stock' => $balance])->save();

            return StockMovement::create([
                'ingredient_id'  => $ingredient->id,
                'type'           => $type,
                'quantity'       => $signedQty,
                'balance_after'  => $balance,
                'unit_cost'      => $unitCost,
                'reference_type' => $reference?->getMorphClass(),
                'reference_id'   => $reference?->getKey(),
                'note'           => $note,
                'user_id'        => $userId,
            ]);
        });
    }
}
