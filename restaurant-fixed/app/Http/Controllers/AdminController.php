<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Menu;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        // Chỉ cần auth — việc kiểm tra role:2 đã được xử lý ở routes/web.php
        $this->middleware('auth');
    }

    public function index()
    {
        $reservations = Reservation::with(['user', 'table'])->latest()->get();
        $orders       = Order::with('user')->latest()->get();
        $menus        = Menu::all();

        $totalRevenue = Order::sum('total_price');
        $totalToday   = Order::whereDate('created_at', today())->sum('total_price');
        $totalMonth   = Order::whereMonth('created_at', now()->month)->sum('total_price');

        // Thống kê nhanh
        $stats = [
            'pending'    => $reservations->where('status', 'pending')->count(),
            'confirmed'  => $reservations->where('status', 'confirmed')->count(),
            'serving'    => $reservations->where('status', 'serving')->count(),
            'completed'  => $reservations->where('status', 'completed')->count(),
        ];

        return view('admin.index', compact(
            'reservations',
            'orders',
            'menus',
            'totalRevenue',
            'totalToday',
            'totalMonth',
            'stats'
        ));
    }

    public function updateStock(Request $request, Menu $menu)
    {
        $request->validate(['stock' => 'required|integer|min:0']);
        $menu->update(['stock' => $request->stock]);
        return redirect()->back()->with('success', 'Cập nhật stock thành công.');
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,serving,paid']);
        $order->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }
}
