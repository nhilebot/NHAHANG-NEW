<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TableSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBillTest extends TestCase
{
    use RefreshDatabase;

    public function test_bill_detail_shows_deposit_and_final_payment(): void
    {
        $this->seed([RoleSeeder::class, UserSeeder::class, TableSeeder::class]);
        $r = new Reservation();
        $r->user_id = User::where('email', 'customer@gmail.com')->first()->id;
        $r->full_name = 'K'; $r->phone = '09'; $r->reservation_date = now()->toDateString();
        $r->reservation_time = '19:00'; $r->table_id = Table::first()->id;
        $r->status = 'paid_cash'; $r->payment_status = 'deposit_paid';
        $r->total_price = 1000000; $r->deposit_amount = 200000;
        $r->paid_amount = 800000; $r->paid_at = now(); $r->deposit_paid_at = now();
        $r->cart_data = [];
        $r->save();

        $res = $this->actingAs(User::where('email', 'admin@gmail.com')->first())->get("/admin/bills/{$r->id}");
        $res->assertOk()->assertSee('Đã thu trước')->assertSee('200,000đ')->assertSee('800,000đ')->assertSee('1,000,000đ')->assertSee('HÓA ĐƠN THANH TOÁN')->assertSee('CÒN PHẢI THANH TOÁN');
    }
}
