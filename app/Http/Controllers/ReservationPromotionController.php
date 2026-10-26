<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Reservation;
use App\Services\PromotionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Gắn / gỡ mã khuyến mãi cho đặt bàn.
 *  - Khách  : ở trang giỏ hàng, khi đặt bàn đang chờ cọc.
 *  - Nhân viên/Admin: ở màn hình thanh toán, khi đơn đang phục vụ (chưa thanh toán).
 * Controller này KHÔNG sửa total_price, deposit_amount hay trạng thái đơn.
 */
class ReservationPromotionController extends Controller
{
    private const STAFF_STATUSES = ['arrived', 'serving', 'ready', 'served'];

    public function __construct(private PromotionService $promotions)
    {
        $this->middleware('auth');
    }

    /* ------------------------- KHÁCH ------------------------- */

    public function customerApply(Request $request)
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:30']], [
            'code.required' => 'Vui lòng nhập mã khuyến mãi.',
        ]);

        $reservation = $this->customerReservation();
        if (!$reservation) {
            return back()->with('promo_error', 'Bạn cần đặt bàn (đang chờ cọc) trước khi dùng mã khuyến mãi.');
        }

        // Tổng tiền món trong giỏ — cùng cách tính với CartController@index
        $subtotal = (int) Cart::with('menu')->where('user_id', auth()->id())->get()
            ->sum(fn ($i) => $i->menu ? $i->menu->price * $i->quantity : 0);

        try {
            $promo = $this->promotions->attach($reservation, $data['code'], $subtotal);
        } catch (\DomainException $e) {
            return back()->withInput()->with('promo_error', $e->getMessage());
        }

        return back()->with('promo_success', 'Đã áp dụng mã ' . $promo->code . '. Khoản giảm sẽ được trừ khi thanh toán tại quầy.');
    }

    public function customerRemove()
    {
        if ($reservation = $this->customerReservation()) {
            $this->promotions->detach($reservation);
        }

        return back()->with('promo_success', 'Đã gỡ mã khuyến mãi.');
    }

    private function customerReservation(): ?Reservation
    {
        return Reservation::where('user_id', auth()->id())
            ->where('status', 'pending_deposit')
            ->where('payment_status', 'unpaid')
            ->latest()
            ->first();
    }

    /* --------------------- NHÂN VIÊN / ADMIN --------------------- */

    public function staffApply(Request $request, $id)
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:30']], [
            'code.required' => 'Vui lòng nhập mã khuyến mãi.',
        ]);

        try {
            $promo = DB::transaction(function () use ($id, $data) {
                $reservation = Reservation::lockForUpdate()->findOrFail($id);
                $this->assertEditable($reservation);

                return $this->promotions->attach($reservation, $data['code'], (int) $reservation->total_price);
            });
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Đã áp dụng mã ' . $promo->code . '.');
    }

    public function staffRemove($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $reservation = Reservation::lockForUpdate()->findOrFail($id);
                $this->assertEditable($reservation);
                $this->promotions->detach($reservation);
            });
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Đã gỡ mã khuyến mãi.');
    }

    private function assertEditable(Reservation $reservation): void
    {
        if (!in_array($reservation->status, self::STAFF_STATUSES, true)) {
            throw new \DomainException('Chỉ gắn/gỡ mã khi đơn đang phục vụ và chưa thanh toán.');
        }
    }
}
