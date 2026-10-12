<?php

namespace Tests\Feature;

use App\Models\Cart;
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

class CustomerCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_synchronizes_the_exact_cart_with_reservation_and_admin_order(): void
    {
        $this->seed([RoleSeeder::class, UserSeeder::class, CategorySeeder::class, MenuSeeder::class, TableSeeder::class]);

        $customer = User::where('email', 'customer@gmail.com')->first();
        $admin = User::where('email', 'admin@gmail.com')->first();
        [$firstMenu, $secondMenu] = Menu::take(2)->get()->all();
        Menu::query()->update(['stock' => 50]);
        $firstMenu->refresh();
        $secondMenu->refresh();

        $reservation = new Reservation();
        $reservation->user_id = $customer->id;
        $reservation->full_name = $customer->name;
        $reservation->phone = $customer->phone ?? '0900000000';
        $reservation->reservation_date = now()->toDateString();
        $reservation->reservation_time = '19:00';
        $reservation->table_id = Table::first()->id;
        $reservation->status = 'pending_deposit';
        $reservation->payment_status = 'unpaid';
        $reservation->total_price = 123;
        $reservation->deposit_amount = 25;
        $reservation->cart_data = [['id' => $firstMenu->id, 'name' => 'Món cũ', 'price' => 123, 'quantity' => 1]];
        $reservation->save();

        Cart::create(['user_id' => $customer->id, 'menu_id' => $firstMenu->id, 'quantity' => 2]);
        Cart::create(['user_id' => $customer->id, 'menu_id' => $secondMenu->id, 'quantity' => 1]);

        $this->actingAs($customer)
            ->post('/cart/checkout', ['payment_method' => 'BANK'])
            ->assertRedirect('/');

        $order = Order::latest()->firstOrFail();
        $expected = [
            ['menu_id' => $firstMenu->id, 'product_name' => $firstMenu->name, 'quantity' => 2],
            ['menu_id' => $secondMenu->id, 'product_name' => $secondMenu->name, 'quantity' => 1],
        ];
        $actualOrderItems = $order->items->sortBy('menu_id')->values()->map(fn ($item) => [
            'menu_id' => (int) $item->menu_id,
            'product_name' => $item->product_name,
            'quantity' => (int) $item->quantity,
        ])->all();

        $this->assertSame($expected, $actualOrderItems);
        $this->assertEquals(
            $firstMenu->price * 2 + $secondMenu->price,
            $order->total_price
        );

        $reservation->refresh();
        $this->assertSame([2, 1], array_column($reservation->cart_data, 'quantity'));
        $this->assertEquals($order->total_price, $reservation->total_price);
        $this->assertSame(
            [2, 1],
            OrderItem::where('reservation_id', $reservation->id)->orderBy('menu_id')->pluck('quantity')->all()
        );

        $this->actingAs($admin)
            ->get('/admin/orders')
            ->assertOk()
            ->assertSee($firstMenu->name)
            ->assertSee($secondMenu->name)
            ->assertSee('2x ' . $firstMenu->name)
            ->assertSee('1x ' . $secondMenu->name);

        $this->actingAs($admin)
            ->get("/admin/orders/{$order->id}")
            ->assertOk()
            ->assertSee($firstMenu->name)
            ->assertSee($secondMenu->name);
    }
}
