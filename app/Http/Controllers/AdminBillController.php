<?php

namespace App\Http\Controllers;

use App\Models\Reservation;

class AdminBillController extends Controller
{
    public function index()
    {
        $bills = Reservation::with(['user', 'table'])
            ->whereIn('status', [
                'paid_cash',
                'paid_transfer',
                'completed'
            ])
            ->latest()
            ->paginate(15);

        $todayRevenue = Reservation::whereIn('status', [
                'paid_cash',
                'paid_transfer',
                'completed'
            ])
            ->whereDate('updated_at', today())
            ->sum(\Illuminate\Support\Facades\DB::raw('total_price - COALESCE(discount_amount, 0)'));

        $totalRevenue = Reservation::whereIn('status', [
                'paid_cash',
                'paid_transfer',
                'completed'
            ])
            ->sum(\Illuminate\Support\Facades\DB::raw('total_price - COALESCE(discount_amount, 0)'));

        return view('admin.bills.index', compact(
            'bills',
            'todayRevenue',
            'totalRevenue'
        ));
    }

    public function show($id)
    {
        $bill = Reservation::with([
            'user',
            'table'
        ])->findOrFail($id);

        $total    = (int) $bill->total_price;                  // tiền món gốc
        $discount = (int) ($bill->discount_amount ?? 0);       // khuyến mãi đã chốt lúc thanh toán
        $payable  = max($total - $discount, 0);                // tổng khách thực trả sau giảm giá
        $deposit  = min((int) ($bill->deposit_amount ?? 0), $payable);
        // Số tiền thu cuối buổi: ưu tiên số đã ghi lúc thanh toán, hóa đơn cũ thì tính lại
        $remaining = $bill->paid_amount !== null
            ? (int) $bill->paid_amount
            : max($payable - $deposit, 0);

        return view('admin.bills.show', compact('bill', 'total', 'deposit', 'remaining', 'discount', 'payable'));
    }
}