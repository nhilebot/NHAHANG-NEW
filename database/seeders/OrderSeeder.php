<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderSeeder extends Seeder
{
    private const SAMPLE_ORDERS = [
        [
            'code' => 'DEMO-CASH-001',
            'reservation_code' => 'DEMO-RES-CASH-001',
            'table_name' => 'Bàn 01',
            'reservation_time' => '18:00:00',
            'payment_method' => 'cash',
            'status' => 'paid_cash',
            'items' => [
                ['name' => 'Cua Hấp', 'quantity' => 1, 'price' => 250000, 'image' => 'images/hs1.jpg'],
                ['name' => 'Tôm Càng Hấp', 'quantity' => 1, 'price' => 220000, 'image' => 'images/hs2.jpg'],
            ],
        ],
        [
            'code' => 'DEMO-TRANSFER-002',
            'reservation_code' => 'DEMO-RES-TRANSFER-002',
            'table_name' => 'Bàn 02',
            'reservation_time' => '19:00:00',
            'payment_method' => 'transfer',
            'status' => 'paid_transfer',
            'items' => [
                ['name' => 'Mực hấp', 'quantity' => 1, 'price' => 150000,],
                ['name' => 'Salad trái cây', 'quantity' => 1, 'price' => 80000,],
            ],
        ],
    ];

    public function run(): void
    {
        $customer = DB::table('users')
            ->where('email', 'leyennhi22122006@gmail.com')
            ->first();
        $staff = DB::table('users')
            ->where('email', 'staff@gmail.com')
            ->first();

        if (!$customer || !$staff) {
            throw new RuntimeException('Run UserSeeder before OrderSeeder to create the demo customer and staff.');
        }

        DB::transaction(function () use ($customer, $staff): void {
            $now = now();

            foreach (self::SAMPLE_ORDERS as $sample) {
                $table = DB::table('tables')
                    ->where('name', $sample['table_name'])
                    ->first();

                if (!$table) {
                    throw new RuntimeException('Run TableSeeder before OrderSeeder to create the demo tables.');
                }

                $total = array_sum(array_map(
                    fn (array $item): int => $item['quantity'] * $item['price'],
                    $sample['items']
                ));
                $deposit = (int) round($total * 0.2);
                $cartData = json_encode($sample['items'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

                DB::table('reservations')->updateOrInsert(
                    ['code' => $sample['reservation_code']],
                    [
                        'user_id' => $customer->id,
                        'reservation_date' => $now->toDateString(),
                        'reservation_time' => $sample['reservation_time'],
                        'table_id' => $table->id,
                        'full_name' => $customer->name,
                        'email' => $customer->email,
                        'phone' => $customer->phone,
                        'notes' => 'Dữ liệu hóa đơn mẫu',
                        'cart_data' => $cartData,
                        'total_price' => $total,
                        'deposit_amount' => $deposit,
                        'paid_amount' => $total - $deposit,
                        'paid_at' => $now,
                        'paid_by' => $staff->id,
                        'deposit_paid_at' => $now->copy()->subHour(),
                        'reservation_status' => 'completed',
                        'payment_status' => 'paid',
                        'payment_method' => $sample['payment_method'],
                        'status' => $sample['status'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );

                $reservationId = DB::table('reservations')
                    ->where('code', $sample['reservation_code'])
                    ->value('id');

                if (!$reservationId) {
                    throw new RuntimeException("Could not find reservation for demo order {$sample['code']}.");
                }

                DB::table('orders')->updateOrInsert(
                    ['code' => $sample['code']],
                    [
                        'user_id' => $customer->id,
                        'reservation_id' => $reservationId,
                        'table_number' => (string) $table->id,
                        'total_price' => $total,
                        'status' => 'paid',
                        'payment_method' => $sample['payment_method'],
                        'name' => $customer->name,
                        'phone' => $customer->phone,
                        'notes' => 'Dữ liệu hóa đơn mẫu',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        });
    }
}
