<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone'     => 'required|string|max:20',
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required',
            'table_id'  => 'required|integer|exists:tables,id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                if (!Table::lockIfAvailableForBooking((int) $request->table_id)) {
                    throw new \DomainException('Bàn này hiện đang được sử dụng, đã đặt hoặc đang dọn. Vui lòng chọn bàn khác.');
                }

                $reservation = new Reservation();
                $reservation->full_name        = $request->full_name;
                $reservation->email            = $request->email ?? '';
                $reservation->phone            = $request->phone;
                $reservation->reservation_date = $request->reservation_date;
                $reservation->reservation_time = $request->reservation_time;
                $reservation->table_id         = $request->table_id;
                $reservation->notes            = $request->notes;
                $reservation->status           = 'pending';
                $reservation->payment_status   = 'unpaid';
                $reservation->total_price      = 0;
                $reservation->cart_data        = [];
                $reservation->save();

                Table::whereKey($request->table_id)->update(['status' => 'reserved']);
            });
        } catch (\DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Đặt bàn thành công! Chúng tôi sẽ liên hệ xác nhận.');
    }
}