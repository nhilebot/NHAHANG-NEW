<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\MenuSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TableSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExtraItemsTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;
    private User $customer;
    private Menu $mực;
    private Menu $tôm;
    private Reservation $res;
    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, UserSeeder::class, CategorySeeder::class, MenuSeeder::class, TableSeeder::class]);
        $this->staff    = User::where('email', 'staff@gmail.com')->first();
        $this->customer = User::where('email', 'customer@gmail.com')->first();
        [$this->mực, $this->tôm] = Menu::take(2)->get()->all();
        Menu::query()->update(['stock' => 50]);

        $this->res = new Reservation();
        $this->res->user_id = $this->customer->id;
        $this->res->full_name = 'Khach';
        $this->res->phone = '0900000000';
        $this->res->reservation_date = now()->toDateString();
        $this->res->reservation_time = '19:00';
        $this->res->table_id = Table::first()->id;
        $this->res->status = 'arrived';
        $this->res->payment_status = 'deposit_paid';
        $this->res->deposit_amount = 100000;
        $this->res->total_price = $this->mực->price * 2;
        $this->res->cart_data = [['id' => $this->mực->id, 'name' => $this->mực->name, 'price' => $this->mực->price, 'quantity' => 2]];
        $this->res->save();

        $this->order = Order::create([
            'user_id' => $this->customer->id, 'reservation_id' => $this->res->id,
            'table_number' => (string) $this->res->table_id, 'total_price' => $this->mực->price * 2,
            'status' => 'pending_payment', 'name' => 'Khach',
        ]);
        OrderItem::create(['order_id' => $this->order->id, 'menu_id' => $this->mực->id,
            'product_name' => $this->mực->name, 'quantity' => 2, 'price' => $this->mực->price]);
    }

    public function test_staff_extra_items_show_in_customer_history(): void
    {
        $this->actingAs($this->staff)->post("/staff/reservations/{$this->res->id}/update-items", [
            'items' => [
                ['id' => $this->mực->id, 'quantity' => 3],
                ['id' => $this->tôm->id, 'quantity' => 3],
            ],
        ])->assertSessionHas('success');

        $items = OrderItem::where('order_id', $this->order->id)->get();
        $this->assertEquals(1, $items->where('menu_id', $this->mực->id)->where('is_extra', true)->sum('quantity'));
        $this->assertEquals(3, $items->where('menu_id', $this->tôm->id)->where('is_extra', true)->sum('quantity'));
        $this->assertEquals(2, $items->where('menu_id', $this->mực->id)->where('is_extra', false)->sum('quantity'));

        $expected = $this->mực->price * 3 + $this->tôm->price * 3;
        $this->assertEquals($expected, $this->order->fresh()->total_price);
        $this->assertEquals(100000, $this->res->fresh()->deposit_amount);

        $this->actingAs($this->customer)->get('/order-history')->assertOk()->assertSee('Gọi thêm tại bàn');
    }

    public function test_reducing_extra_removes_from_history_total(): void
    {
        $this->actingAs($this->staff)->post("/staff/reservations/{$this->res->id}/update-items", [
            'items' => [['id' => $this->mực->id, 'quantity' => 2], ['id' => $this->tôm->id, 'quantity' => 3]],
        ]);
        $this->actingAs($this->staff)->post("/staff/reservations/{$this->res->id}/update-items", [
            'items' => [['id' => $this->mực->id, 'quantity' => 2], ['id' => $this->tôm->id, 'quantity' => 1]],
        ]);
        $this->assertEquals(1, OrderItem::where('order_id', $this->order->id)->where('menu_id', $this->tôm->id)->sum('quantity'));
        $this->assertEquals($this->mực->price * 2 + $this->tôm->price, $this->order->fresh()->total_price);
    }

    public function test_cannot_add_items_after_paid(): void
    {
        $this->res->status = 'paid_cash';
        $this->res->save();
        $this->actingAs($this->staff)->post("/staff/reservations/{$this->res->id}/update-items", [
            'items' => [['id' => $this->tôm->id, 'quantity' => 1]],
        ])->assertSessionHas('error');
        $this->assertSame(0, OrderItem::where('order_id', $this->order->id)->where('is_extra', true)->count());
    }
}
