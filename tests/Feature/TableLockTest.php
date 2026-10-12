<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Menu;
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

class TableLockTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private Table $table;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, UserSeeder::class, CategorySeeder::class, MenuSeeder::class, TableSeeder::class]);
        $this->customer = User::where('email', 'customer@gmail.com')->first();
        $this->table = Table::first();
        Menu::query()->update(['stock' => 50]);
    }

    private function book(User $u): void
    {
        Cart::create(['user_id' => $u->id, 'menu_id' => Menu::first()->id, 'quantity' => 1]);
        $this->actingAs($u)->post('/reservation/store', [
            'full_name' => 'K', 'phone' => '0900000000',
            'reservation_date' => now()->addDay()->toDateString(),
            'reservation_time' => '19:00', 'table_id' => $this->table->id,
        ]);
    }

    public function test_booking_alone_does_not_lock_table(): void
    {
        $this->book($this->customer);
        $this->assertSame('pending_deposit', Reservation::latest('id')->first()->status);
        $this->assertSame('empty', $this->table->fresh()->status);

        $res = $this->actingAs($this->customer)->get('/reservation');
        $this->assertNotContains($this->table->id, $res->viewData('bookedTableIds'));
    }

    public function test_confirm_payment_locks_table_and_blocks_others(): void
    {
        $this->book($this->customer);
        $this->actingAs($this->customer)->post('/cart/checkout', ['payment_method' => 'BANK']);

        $r = Reservation::latest('id')->first();
        $this->assertSame('deposit_submitted', $r->payment_status);
        $this->assertSame('reserved', $this->table->fresh()->status);

        $res = $this->actingAs($this->customer)->get('/reservation');
        $this->assertContains($this->table->id, $res->viewData('bookedTableIds'));

        // người khác không đặt được bàn đã khóa
        $other = User::where('email', 'staff@gmail.com')->first();
        $before = Reservation::count();
        $this->book($other);
        $this->assertSame($before, Reservation::count());
    }

    public function test_second_customer_checkout_fails_if_table_taken_meanwhile(): void
    {
        $other = User::where('email', 'staff@gmail.com')->first();
        $this->book($this->customer);
        $this->book($other);   // cả hai cùng giữ pending_deposit (chưa khóa)
        $this->assertSame(2, Reservation::count());

        $this->actingAs($this->customer)->post('/cart/checkout', ['payment_method' => 'BANK']);
        Cart::create(['user_id' => $other->id, 'menu_id' => Menu::first()->id, 'quantity' => 1]);
        $this->actingAs($other)->post('/cart/checkout', ['payment_method' => 'BANK'])->assertSessionHas('error');

        $this->assertSame('unpaid', Reservation::where('user_id', $other->id)->first()->payment_status);
    }

    public function test_table_marked_as_serving_is_not_shown_or_bookable(): void
    {
        $this->table->update(['status' => 'serving']);

        $response = $this->actingAs($this->customer)->get('/reservation')->assertOk();
        $this->assertSame(
            1,
            preg_match('/<input[^>]*id="table' . $this->table->id . '"[^>]*disabled/s', $response->getContent())
        );
        $this->assertContains($this->table->id, $response->viewData('bookedTableIds'));

        $before = Reservation::count();
        $this->book($this->customer);

        $this->assertSame($before, Reservation::count());
        $this->assertSame('serving', $this->table->fresh()->status);
    }

    public function test_checkout_rejects_a_reservation_if_its_table_is_now_serving(): void
    {
        $this->book($this->customer);
        $reservation = Reservation::latest('id')->firstOrFail();
        $this->table->update(['status' => 'serving']);

        $this->actingAs($this->customer)
            ->get('/cart')
            ->assertOk()
            ->assertSee('Không thể đặt món lúc này')
            ->assertSee("Bàn {$this->table->name} hiện không trống")
            ->assertDontSee('id="checkoutForm"', false);

        $orderCount = \App\Models\Order::count();
        $this->actingAs($this->customer)
            ->post('/cart/checkout', ['payment_method' => 'BANK'])
            ->assertSessionHas('error');

        $this->assertSame($orderCount, \App\Models\Order::count());
        $this->assertSame('unpaid', $reservation->fresh()->payment_status);
        $this->assertSame('serving', $this->table->fresh()->status);
    }

    public function test_cart_shows_order_blocked_notice_even_when_cart_is_empty(): void
    {
        $this->book($this->customer);
        $this->table->update(['status' => 'serving']);
        Cart::where('user_id', $this->customer->id)->delete();

        $this->actingAs($this->customer)
            ->get('/cart')
            ->assertOk()
            ->assertSee('Không thể đặt món lúc này')
            ->assertSee("Bàn {$this->table->name} hiện không trống");
    }

    public function test_public_booking_endpoint_rejects_a_table_that_is_being_served(): void
    {
        $this->table->update(['status' => 'serving']);
        $before = Reservation::count();

        $this->post('/book-table', [
            'full_name' => 'K',
            'phone' => '0900000000',
            'reservation_date' => now()->addDay()->toDateString(),
            'reservation_time' => '19:00',
            'table_id' => $this->table->id,
        ])->assertSessionHas('error');

        $this->assertSame($before, Reservation::count());
    }
}
