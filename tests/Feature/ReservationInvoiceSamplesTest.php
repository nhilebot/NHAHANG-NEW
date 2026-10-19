<?php

namespace Tests\Feature;

use App\Models\Table;
use App\Models\User;
use App\Models\Reservation;
use Database\Seeders\CategorySeeder;
use Database\Seeders\MenuSeeder;
use Database\Seeders\OrderItemSeeder;
use Database\Seeders\OrderSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TableSeeder;
use Database\Seeders\UserSeeder;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationInvoiceSamplesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(now()->setTime(12, 0));

        $this->seed([
            RoleSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            MenuSeeder::class,
            TableSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
        ]);

        $dayKey = [
            Carbon::MONDAY => 'T2',
            Carbon::TUESDAY => 'T3',
            Carbon::WEDNESDAY => 'T4',
            Carbon::THURSDAY => 'T5',
            Carbon::FRIDAY => 'T6',
            Carbon::SATURDAY => 'T7',
            Carbon::SUNDAY => 'CN',
        ][Carbon::now()->dayOfWeek];

        $staff = User::where('email', 'staff@gmail.com')->firstOrFail();
        $staff->shifts = [$dayKey => 'full'];
        $staff->save();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_admin_and_staff_can_see_the_two_sample_bills_without_food_images(): void
    {
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();
        $staff = User::where('email', 'staff@gmail.com')->firstOrFail();

        foreach ([$admin, $staff] as $user) {
            $listRoute = $user->email === 'staff@gmail.com' ? '/staff/bills' : '/admin/bills';
            $detailRoute = $user->email === 'staff@gmail.com' ? '/staff/bills/' : '/admin/bills/';

            $response = $this->actingAs($user)->get($listRoute)->assertOk();
            $this->assertCount(2, $response->viewData('bills'));
            $response
                ->assertDontSee(asset('images/hs1.jpg'), false)
                ->assertDontSee(asset('images/hs2.jpg'), false)
                ->assertDontSee(asset('images/hs3.jpg'), false)
                ->assertDontSee(asset('images/salad1.jpg'), false);

            $cashBillId = Reservation::where('code', 'DEMO-RES-CASH-001')->value('id');
            $transferBillId = Reservation::where('code', 'DEMO-RES-TRANSFER-002')->value('id');

            $this->get($detailRoute . $cashBillId)
                ->assertOk()
                ->assertDontSee(asset('images/hs1.jpg'), false)
                ->assertDontSee(asset('images/hs2.jpg'), false);

            $this->get($detailRoute . $transferBillId)
                ->assertOk()
                ->assertDontSee(asset('images/hs3.jpg'), false)
                ->assertDontSee(asset('images/salad1.jpg'), false);
        }
    }

    public function test_sample_food_images_are_visible_in_customer_transaction_history(): void
    {
        $customer = User::where('email', 'leyennhi22122006@gmail.com')->firstOrFail();

        $this->actingAs($customer)
            ->get('/order-history')
            ->assertOk()
            ->assertSee(asset('images/hs1.jpg'), false)
            ->assertSee(asset('images/hs2.jpg'), false)
            ->assertSee(asset('images/hs3.jpg'), false)
            ->assertSee(asset('images/salad1.jpg'), false);
    }

    public function test_paid_sample_tables_are_marked_unavailable_on_the_reservation_page(): void
    {
        $customer = User::where('email', 'leyennhi22122006@gmail.com')->firstOrFail();
        $response = $this->actingAs($customer)->get('/reservation')->assertOk();

        foreach (['Bàn 01', 'Bàn 02'] as $tableName) {
            $table = Table::where('name', $tableName)->firstOrFail();
            $this->assertContains($table->id, $response->viewData('bookedTableIds'));
            $this->assertContains($table->id, $response->viewData('paymentTableIds'));
            $this->assertMatchesRegularExpression(
                '/<input[^>]*id="table' . $table->id . '"[^>]*disabled/s',
                $response->getContent()
            );
        }

        $response->assertSee('Đã đặt');
    }
}
