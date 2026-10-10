<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Khách đặt hàng online (không qua đặt bàn)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'table_number'   => ['nullable', 'string', 'max:50'],
            'items'          => ['required', 'array', 'min:1', 'max:50'],
            'items.*.id'     => ['required', 'integer', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        try {
            $order = DB::transaction(function () use ($data) {
                // Giá, tên món LUÔN lấy từ DB — không tin dữ liệu client gửi lên
                $menus = Menu::whereIn('id', collect($data['items'])->pluck('id'))->get()->keyBy('id');

                $total = 0;
                $lines = [];
                foreach ($data['items'] as $item) {
                    $menu = $menus[$item['id']];
                    $qty  = (int) $item['quantity'];
                    $total += $menu->price * $qty;
                    $lines[] = [
                        'menu_id'      => $menu->id,
                        'product_name' => $menu->name,
                        'quantity'     => $qty,
                        'price'        => $menu->price,
                    ];
                }

                $order = Order::create([
                    'user_id'      => Auth::id(),
                    'table_number' => $data['table_number'] ?? 'Mang về',
                    'total_price'  => $total,
                    'status'       => 'pending',
                    'name'         => Auth::user()->name,
                    'phone'        => Auth::user()->phone,
                ]);
                $order->orderItems()->createMany($lines);

                return $order;
            });

            return response()->json(['success' => true, 'message' => 'Đã chuyển đơn hàng vào hệ thống!', 'order_id' => $order->id]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Không thể tạo đơn hàng, vui lòng thử lại.'], 500);
        }
    }

    /**
     * Lịch sử đơn hàng của khách
     */
    public function history()
    {
       // Chỉ trả đơn CỦA CHÍNH người đăng nhập (tránh lộ đơn của khách khác)
       $orders = Order::with(['items.menu'])
                      ->where('user_id', Auth::id())
                      ->latest()
                      ->paginate(20);
    
    return view('orders.history', compact('orders'));
    }

    /**
     * Chi tiết đơn hàng — FIX: Admin có thể xem bất kỳ đơn nào
     */
    public function show($id)
{
    $order = Order::with([
        'items',
        'items.menu',
        'user',
        'table'
    ])->findOrFail($id);

    return view('orders.show', compact('order'));
}

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => $request->input('status')]);
        return redirect()->back()->with('success', 'Đã cập nhật trạng thái!');
    }

    /**
     * Hủy đơn hàng (chỉ khi ở trạng thái pending)
     */
    public function cancel($id)
    {
        try {
            $order = Order::where('user_id', Auth::id())->findOrFail($id); // chỉ huỷ đơn của chính mình

            // Chỉ cho phép hủy khi ở trạng thái pending
            if ($order->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể hủy đơn ở trạng thái chờ duyệt!'
                ], 400);
            }

            // Cập nhật trạng thái thành cancelled
            $order->update(['status' => 'cancelled']);

            // Broadcast cập nhật cho admin, nhân viên và khách hàng qua Echo
            event(new \App\Events\OrderStatusUpdated($order));

            return response()->json([
                'success' => true,
                'message' => 'Đơn hàng đã bị hủy thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // =========================================================
    // MÀN HÌNH BẾP (KDS - Kitchen Display System)
    // Luồng: pending → confirmed → arrived → serving → ready → served
    // =========================================================

    /**
     * Dashboard bếp — Bếp thấy đơn khi status = 'serving' hoặc 'arrived'
     */
    public function chefIndex()
    {
        // Đơn đang cần nấu (Staff đã gửi bếp)
        $pendingOrders = Reservation::with('table')
            ->whereIn('status', ['arrived', 'serving'])
            ->oldest('updated_at')
            ->get();

        // Đơn đã nấu xong (chờ Staff bưng ra)
        $readyOrders = Reservation::where('status', 'ready')
            ->latest('updated_at')
            ->get();

        $pendingItems = 0;
        $cookingItems = 0;

        foreach ($pendingOrders as $res) {
            $statuses = $res->chef_statuses ?? [];
            foreach (($res->cart_data ?? []) as $item) {
                $st  = $statuses[$item['id']] ?? 'pending';
                $qty = $item['quantity'] ?? 1;
                if ($st === 'pending') $pendingItems += $qty;
                if ($st === 'cooking') $cookingItems += $qty;
            }
        }

        return view('chef.dashboard', compact('pendingOrders', 'readyOrders', 'pendingItems', 'cookingItems'));
    }

    /**
     * Cập nhật trạng thái từng món (pending → cooking → done)
     */
    public function updateItemStatus(Request $request, $reservationId, $menuId)
    {
        $data = $request->validate([
            'chef_status' => ['required', Rule::in(['pending', 'cooking', 'done'])],
        ]);

        $reservation = Reservation::findOrFail($reservationId);
        abort_unless(in_array($reservation->status, ['arrived', 'serving'], true), 422, 'Đơn này không còn ở bếp.');

        $itemIds = collect($reservation->cart_data ?? [])->pluck('id')->map(fn ($i) => (string) $i);
        abort_unless($itemIds->contains((string) $menuId), 404);

        $statuses            = $reservation->chef_statuses ?? [];
        $statuses[$menuId]   = $data['chef_status'];
        $reservation->chef_statuses = $statuses;

        // Món đầu tiên được nấu → đơn chuyển sang 'serving'
        if ($data['chef_status'] === 'cooking' && $reservation->status === 'arrived') {
            $reservation->status = 'serving';
        }

        $reservation->save();
        return back()->with('success', 'Đã cập nhật trạng thái món!');
    }

    /**
     * Bếp bấm "Bắt đầu nấu" toàn bộ đơn
     */
    public function startOrder($id)
    {
        $reservation = Reservation::findOrFail($id);

        if (!in_array($reservation->status, ['arrived', 'serving'], true)) {
            return back()->with('error', 'Đơn này không ở trạng thái chờ nấu.');
        }

        $statuses = $reservation->chef_statuses ?? [];
        foreach (($reservation->cart_data ?? []) as $item) {
            if (($statuses[$item['id']] ?? 'pending') === 'pending') {
                $statuses[$item['id']] = 'cooking';   // không ghi đè món đã 'done'
            }
        }

        $reservation->chef_statuses = $statuses;
        $reservation->status        = 'serving';
        $reservation->save();

        return back()->with('success', 'Đã bắt đầu chế biến toàn bộ món!');
    }

    /**
     * Bếp bấm "Nấu xong" → Staff biết để bưng lên
     */
    public function finishOrder($id)
    {
        $reservation = Reservation::findOrFail($id);

        if (!Reservation::canTransition($reservation->status, 'ready')) {
            return back()->with('error', 'Đơn chưa ở trạng thái đang nấu nên không thể báo xong.');
        }

        $statuses = [];
        foreach (($reservation->cart_data ?? []) as $item) {
            $statuses[$item['id']] = 'done';
        }

        $reservation->chef_statuses = $statuses;
        $reservation->status        = 'ready';
        $reservation->save();

        if ($order = $reservation->linkedOrder()) {
            $order->update(['status' => 'ready']);
        }

        return back()->with('success', 'Bếp đã nấu xong! Staff hãy bưng món ra.');
    }

    /**
     * Bếp đánh dấu đơn đã hoàn tất (Served)
     */
    public function markAsDone($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update(['status' => 'served']);
        return back()->with('success', 'Đã báo hoàn thành!');
    }
    // Danh sách đơn (Admin)
public function adminIndex()
{
    $orders = Order::with('user')->latest()->paginate(20);
    return view('admin.orders.index', compact('orders'));
}

// Form sửa trạng thái
public function edit($id)
{
    $order = Order::with('user')->findOrFail($id);
    return view('admin.orders.edit', compact('order'));
}

// Lưu trạng thái mới
// Lưu trạng thái mới (Hàm cập nhật trạng thái dành cho Admin)
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', Rule::in(['pending', 'pending_payment', 'serving', 'ready', 'paid', 'completed', 'cancelled'])],
        ]);

        $order = Order::with('orderItems')->findOrFail($id);
        
        // Lưu lại trạng thái cũ của hóa đơn trước khi cập nhật dữ liệu mới
        $oldStatus = $order->status; 
        $newStatus = $request->status;

        // Tiến hành cập nhật trạng thái mới từ form gửi lên
        $order->update(['status' => $newStatus]);

        // 🔥 LOGIC NGHIỆP VỤ: Kích hoạt gửi Gmail khi Admin duyệt thanh toán thành công
        // Nếu đơn hàng cũ ở trạng thái chờ tiền (pending_payment) và được duyệt sang trạng thái hợp lệ (paid hoặc pending)
        if ($oldStatus === 'pending_payment' && ($newStatus === 'paid' || $newStatus === 'pending')) {
            
            // Tái cấu trúc danh sách món ăn từ mối quan hệ orderItems trong Database để truyền sang Mail
            $cartToEmail = [];
            foreach ($order->orderItems as $item) {
                $cartToEmail[] = [
                    'name'     => $item->product_name,
                    'quantity' => $item->quantity,
                    'price'    => $item->price,
                    'total'    => $item->price * $item->quantity
                ];
            }

            $mailData = [
                'invoice' => $order->code,
                'total'   => $order->total_price,
                'table'   => $order->table_number,
                'cart'    => $cartToEmail
            ];

            // Tìm thông tin email của tài khoản khách hàng để thực hiện gửi
            $customerEmail = $order->user->email ?? null;

            if ($customerEmail) {
                try {
                    \Illuminate\Support\Facades\Mail::to($customerEmail)->send(new \App\Mail\OrderConfirmed($mailData));
                } catch (\Exception $e) {
                    \Log::error('Lỗi khi Admin duyệt đơn gửi email tự động thất bại: ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('admin.orders.show', $id)
                         ->with('success', 'Cập nhật trạng thái và gửi email xác nhận thành công!');
    }

// Xoá đơn
public function destroy($id)
{
    Order::findOrFail($id)->delete();
    return redirect()->route('admin.index')
                     ->with('success', 'Đã xoá đơn hàng!');
}
}
