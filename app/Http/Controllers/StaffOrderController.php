<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\OrderItem;
use App\Models\Reservation;
use App\Services\ReservationItemService;
use Illuminate\Support\Facades\DB;
class StaffOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Hiển thị form tạo order mới cho bàn
     */
    public function create($reservationId)
    {
        // Luồng cũ đã ngừng: món được quản lý qua màn hình "Sửa món" (edit_items).
        return redirect()->route('staff.reservations.edit_items', $reservationId)
            ->with('error', 'Chức năng này đã ngừng. Vui lòng dùng màn hình sửa món của đơn.');
    }

    /**
     * (Đã ngừng) Trước đây ghi đè toàn bộ cart_data không kiểm tra trạng thái, làm lệch dữ liệu món.
     * Chặn lại; mọi thay đổi món đi qua update() -> ReservationItemService.
     */
    public function store(Request $request, $reservationId)
    {
        return redirect()->route('staff.reservations.edit_items', $reservationId)
            ->with('error', 'Chức năng này đã ngừng. Vui lòng dùng màn hình sửa món của đơn.');
    }

    /**
     * Gửi đơn lên bếp — FIX: dùng đúng tên cột 'status'
     * Luồng: Staff xác nhận khách đã ngồi → status = 'serving' → Bếp nhận đơn
     */
    public function sendToKitchen($reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);

        // Đảm bảo đã có món mới được gửi bếp
        if (empty($reservation->cart_data)) {
            return redirect()->back()->with('error', 'Vui lòng thêm món trước khi gửi bếp!');
        }

        if (!Reservation::canTransition($reservation->status, 'serving')) {
            return redirect()->back()->with('error', 'Chỉ gửi bếp được khi khách đã vào bàn.');
        }

        $reservation->update(['status' => 'serving']);

        // Đánh dấu các món chưa gửi bếp là đã gửi (theo từng dòng)
        app(ReservationItemService::class)->markSentToKitchen($reservation);

        return redirect()->route('staff.reservations.index')
                         ->with('success', 'Đã gửi bếp! Bếp sẽ bắt đầu chế biến.');
    }

    /**
     * Hiển thị form sửa order
     */
    public function edit($reservationId)
    {
        $reservation = Reservation::with('table')->findOrFail($reservationId);
        $menus       = Menu::all();

        return view('staff.order-edit', compact('reservation', 'menus'));
    }

    /**
     * Cập nhật order (Staff sửa món)
     */
    /**
     * Cập nhật order (Staff sửa món) và Đồng bộ sang Hóa đơn khách hàng
     */
   /**
     * Cập nhật đơn món ăn (Staff gọi thêm món cho khách tại bàn)
     */
    public function update(Request $request, $reservationId, ReservationItemService $items)
    {
        $request->validate([
            'items'            => 'required|array',
            'items.*.id'       => 'required|integer|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:0|max:99',
            'cancel_reason'    => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($request, $reservationId, $items) {
                $reservation = Reservation::lockForUpdate()->findOrFail($reservationId);

                // Không cho sửa món sau khi đã thanh toán / huỷ
                if (!in_array($reservation->status, ['arrived', 'serving', 'ready', 'served'], true)) {
                    throw new \DomainException('Đơn này không còn ở trạng thái có thể gọi thêm món.');
                }

                // Số phần MONG MUỐN theo form (gộp nếu một món xuất hiện nhiều dòng)
                $wanted = [];
                foreach ($request->input('items') as $item) {
                    $key = (int) $item['id'];
                    $wanted[$key] = ($wanted[$key] ?? 0) + (int) $item['quantity'];
                }

                // Món đang có trong đơn mà không còn trong form => số phần mong muốn = 0.
                // Lấy từ cả cart_data (đơn cũ chưa chuyển) lẫn reservation_items.
                $oldIds = collect($reservation->cart_data ?? [])->pluck('id')->filter()
                    ->merge(array_keys($items->activeQuantities($reservation)))
                    ->map(fn ($id) => (int) $id)->unique();
                foreach ($oldIds as $id) {
                    $wanted[$id] = $wanted[$id] ?? 0;
                }

                // Chỉ admin mới được hủy phần đã gửi bếp/đã nấu (và phải có lý do).
                $canCancelCooked = (bool) auth()->user()?->isAdmin();

                $result = $items->setQuantities(
                    $reservation, $wanted, auth()->id(), $canCancelCooked, $request->input('cancel_reason')
                );

                // Trừ số phần có thể bán (menus.stock) cho PHẦN GỌI THÊM, như trước đây.
                // Không đủ thì ném lỗi => cả giao dịch (kể cả dòng vừa tạo) bị hủy.
                foreach ($result['extras'] as $menuId => $qty) {
                    $menu = Menu::whereKey($menuId)->lockForUpdate()->first();
                    if (!$menu) continue;
                    if ($menu->stock < $qty) {
                        throw new \DomainException("Món '{$menu->name}' hiện không đủ phần đáp ứng (Chỉ còn {$menu->stock} phần).");
                    }
                    $menu->decrement('stock', $qty);
                }

                // Không đổi trạng thái đơn khi gọi thêm: bếp tự thấy các dòng món "chờ nấu"
                // (kể cả khi đơn đã ready/served), nên không còn ép đơn về 'arrived'.
            });
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->with('error', 'Có lỗi phát sinh, vui lòng thử lại.');
        }

        return redirect()->back()->with('success', 'Đã lưu thay đổi và chuyển món gọi thêm xuống bếp thành công!');
    }

    public function editItems($id)
{
    $reservation = \App\Models\Reservation::with('table')->findOrFail($id);
    $menus = \App\Models\Menu::all();
    
    // THÊM DÒNG NÀY: Trích xuất giỏ hàng để truyền sang View
    $cartItems = $reservation->cart_data ?? []; 

    return view('staff.reservations.edit-items', compact('reservation', 'menus', 'cartItems'));
}
}