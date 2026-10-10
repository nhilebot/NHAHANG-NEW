<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\MenuSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, UserSeeder::class, CategorySeeder::class, MenuSeeder::class]);
    }

    public function test_order_history_only_lists_own_orders(): void
    {
        $me    = User::where('email', 'leyennhi22122006@gmail.com')->first();
        $other = User::where('email', 'staff@gmail.com')->first();

        Order::create(['user_id' => $me->id,    'table_number' => 'A1', 'total_price' => 1, 'status' => 'pending', 'name' => 'MINE_ORDER_OWNER']);
        Order::create(['user_id' => $other->id, 'table_number' => 'A2', 'total_price' => 1, 'status' => 'pending', 'name' => 'SECRET_OTHER_USER']);

        $res = $this->actingAs($me)->get('/order-history');
        $res->assertOk();
        $this->assertCount(1, $res->viewData('orders'));
        $this->assertSame($me->id, $res->viewData('orders')->first()->user_id);
    }

    public function test_client_cannot_set_price_on_order_store(): void
    {
        $me   = User::where('email', 'leyennhi22122006@gmail.com')->first();
        $menu = Menu::first();

        $this->actingAs($me)->postJson('/order/store', [
            'total_price' => 1,
            'items' => [['id' => $menu->id, 'name' => 'FAKE', 'price' => 1, 'quantity' => 2]],
        ])->assertOk();

        $order = Order::latest('id')->first();
        $this->assertEquals($menu->price * 2, $order->total_price);
        $this->assertSame($menu->name, $order->orderItems->first()->product_name);
        $this->assertEquals($menu->price, $order->orderItems->first()->price);
    }
}
