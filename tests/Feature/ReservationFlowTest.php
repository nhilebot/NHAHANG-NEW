<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Database\Seeders\MenuSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TableSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;
    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, UserSeeder::class, CategorySeeder::class, MenuSeeder::class, TableSeeder::class]);
        $this->staff    = User::where('email', 'staff@gmail.com')->first();
        $this->customer = User::where('email', 'leyennhi22122006@gmail.com')->first();
    }

    private function makeReservation(string $status, int $total = 500000, ?int $deposit = 100000): Reservation
    {
        $r = new Reservation();
        $r->user_id = $this->customer->id;
        $r->full_name = 'Khach';
        $r->phone = '0900000000';
        $r->reservation_date = now()->toDateString();
        $r->reservation_time = '19:00';
        $r->table_id = Table::first()->id;
        $r->status = $status;
        $r->payment_status = 'unpaid';
        $r->total_price = $total;
        $r->deposit_amount = $deposit;
        $r->cart_data = [['id' => Menu::first()->id, 'name' => 'Mon', 'price' => 100000, 'quantity' => 5]];
        $r->save();
        return $r;
    }

    public function test_invalid_status_value_is_rejected(): void
    {
        $r = $this->makeReservation('pending_deposit');
        $this->actingAs($this->staff)->post("/staff/reservations/{$r->id}/update-status", ['status' => 'whatever'])
             ->assertSessionHasErrors('status');
        $this->assertSame('pending_deposit', $r->fresh()->status);
    }

    public function test_cannot_skip_steps_or_self_mark_paid(): void
    {
        $r = $this->makeReservation('pending_deposit');

        foreach (['completed', 'served', 'paid_cash', 'serving'] as $bad) {
            $this->actingAs($this->staff)->post("/staff/reservations/{$r->id}/update-status", ['status' => $bad]);
            $this->assertSame('pending_deposit', $r->fresh()->status, "không được nhảy sang $bad");
        }
    }

    public function test_deposit_confirmation_sets_payment_info(): void
    {
        $r = $this->makeReservation('pending_deposit');
        $this->actingAs($this->staff)->post("/staff/reservations/{$r->id}/update-status", ['status' => 'pending'])
             ->assertSessionHas('success');

        $r->refresh();
        $this->assertSame('pending', $r->status);
        $this->assertSame('deposit_paid', $r->payment_status);
        $this->assertNotNull($r->deposit_paid_at);
    }

    public function test_full_happy_path_with_deposit_deducted_at_payment(): void
    {
        $r = $this->makeReservation('pending_deposit', 500000, 100000);
        $url = fn ($p) => "/staff/reservations/{$r->id}/$p";

        foreach (['pending', 'confirmed', 'arrived', 'serving'] as $st) {
            $this->actingAs($this->staff)->post($url('update-status'), ['status' => $st]);
            $this->assertSame($st, $r->fresh()->status);
        }

        // Bếp nấu xong
        $this->actingAs(User::where('email', 'chef@gmail.com')->first())->post("/chef/order/{$r->id}/finish");
        $this->assertSame('ready', $r->fresh()->status);

        $this->actingAs($this->staff)->post($url('update-status'), ['status' => 'served']);
        $this->assertSame('served', $r->fresh()->status);

        // Thanh toán: thu 400k (đã trừ cọc 100k)
        $this->actingAs($this->staff)->post($url('pay'), ['payment_method' => 'cash'])->assertSessionHas('success');
        $r->refresh();
        $this->assertSame('paid_cash', $r->status);
        $this->assertSame(400000, (int) $r->paid_amount);
        $this->assertSame($this->staff->id, (int) $r->paid_by);
        $this->assertNotNull($r->paid_at);

        $this->actingAs($this->staff)->post($url('update-status'), ['status' => 'completed']);
        $this->assertSame('completed', $r->fresh()->status);
        $this->assertSame('cleaning', Table::find($r->table_id)->status);
    }

    public function test_cannot_pay_before_served(): void
    {
        $r = $this->makeReservation('serving');
        $this->actingAs($this->staff)->post("/staff/reservations/{$r->id}/pay", ['payment_method' => 'cash'])
             ->assertSessionHas('error');
        $this->assertSame('serving', $r->fresh()->status);
    }

    public function test_chef_cannot_set_arbitrary_item_status(): void
    {
        $r = $this->makeReservation('serving');
        $menuId = $r->cart_data[0]['id'];
        $this->actingAs(User::where('email', 'chef@gmail.com')->first())
             ->post("/chef/item/{$r->id}/{$menuId}/status", ['chef_status' => 'hacked'])
             ->assertSessionHasErrors('chef_status');
    }

    public function test_cancel_releases_table(): void
    {
        $r = $this->makeReservation('pending_deposit');
        Table::where('id', $r->table_id)->update(['status' => 'reserved']);

        $this->actingAs($this->staff)->post("/staff/reservations/{$r->id}/update-status", ['status' => 'cancelled']);
        $this->assertSame('cancelled', $r->fresh()->status);
        $this->assertSame('empty', Table::find($r->table_id)->status);
    }
}
