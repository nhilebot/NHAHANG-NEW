<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        $orders = DB::table('orders')
            ->whereIn('code', ['DEMO-CASH-001', 'DEMO-TRANSFER-002'])
            ->get();

        if ($orders->count() !== 2) {
            throw new RuntimeException('Run OrderSeeder before OrderItemSeeder to create both demo orders.');
        }

        $menusByName = DB::table('menus')
            ->get(['id', 'name'])
            ->keyBy(fn ($menu) => strtolower(trim($menu->name)));

        DB::transaction(function () use ($orders, $menusByName): void {
            foreach ($orders as $order) {
                $reservation = DB::table('reservations')
                    ->where('id', $order->reservation_id)
                    ->first();

                if (!$reservation) {
                    throw new RuntimeException("The demo order {$order->code} has no linked reservation.");
                }

                $items = json_decode($reservation->cart_data, true, 512, JSON_THROW_ON_ERROR);

                foreach ($items as $item) {
                    $menu = $menusByName->get(strtolower(trim($item['name'])));
                    if (!$menu) {
                        throw new RuntimeException("No menu entry found for demo dish {$item['name']}.");
                    }

                    DB::table('order_items')->updateOrInsert(
                        [
                            'order_id' => $order->id,
                            'reservation_id' => $reservation->id,
                            'product_name' => $item['name'],
                        ],
                        [
                            'menu_id' => $menu->id,
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                            'created_at' => $order->created_at,
                            'updated_at' => $order->updated_at,
                        ]
                    );
                }
            }
        });
    }
}
