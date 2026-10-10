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
            ->sum('total_price');

        $totalRevenue = Reservation::whereIn('status', [
                'paid_cash',
                'paid_transfer',
                'completed'
            ])
            ->sum('total_price');

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

        $total   = (int) $bill->total_price;
        $deposit = (int) ($bill->deposit_amount ?? 0);
        $deposit = min($deposit, $total);
        // Số tiền thu cuối buổi: ưu tiên số đã ghi lúc thanh toán, hóa đơn cũ thì tính lại
        $remaining = $bill->paid_amount !== null
            ? (int) $bill->paid_amount
            : max($total - $deposit, 0);

        return view('admin.bills.show', compact('bill', 'total', 'deposit', 'remaining'));
    }
}