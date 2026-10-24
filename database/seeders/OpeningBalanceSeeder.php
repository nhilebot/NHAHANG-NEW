<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\StockMovement;
use Illuminate\Database\Seeder;

/**
 * Tồn mẫu do InventorySampleSeeder gán trực tiếp nên chưa có dòng sổ kho.
 * Seeder này tạo dòng "tồn đầu kỳ" cho các nguyên liệu chưa có biến động nào,
 * để Sổ kho khớp với tồn hiện tại. Chạy lại an toàn.
 *
 *   php artisan db:seed --class=OpeningBalanceSeeder
 */
class OpeningBalanceSeeder extends Seeder
{
    public function run(): void
    {
        Ingredient::where('current_stock', '>', 0)
            ->whereDoesntHave('movements')
            ->each(function (Ingredient $i) {
                StockMovement::create([
                    'ingredient_id' => $i->id,
                    'type'          => StockMovement::TYPE_ADJUST,
                    'quantity'      => $i->current_stock,
                    'balance_after' => $i->current_stock,
                    'unit_cost'     => $i->reference_price,
                    'note'          => 'Tồn đầu kỳ (dữ liệu mẫu)',
                ]);
            });
    }
}
