<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\OrderItem;
use App\Models\Reservation;
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
        $reservation = Reservation::with('table')->findOrFail($reservationId);
        $menus       = Menu::all();
        $categories  = Menu::distinct()->pluck('category')->filter();

        return view('staff.order-create', compact('reservation', 'menus', 'categories'));
    }

    /**
     * Lưu order mới (chưa gửi bếp)
     */
    public function store(Request $request, $reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);

        $cartData   = [];
        $totalPrice = 0;

        foreach ($request->items ?? [] as $item) {
            $menu = Menu::find($item['id']);    
            if (!$menu) continue;

            $qty         = (int) $item['quantity'];
            $totalPrice += $menu->price * $qty;

            $cartData[] = [
                'id'       => $menu->id,
                'name'     => $menu->name,
                'price'    => $menu->price,
                'quantity' => $qty,
                'image'    => $menu->image,
            ];
        }

        $reservation->update([
            'cart_data'   => $cartData,
            'total_price' => $totalPrice,
            'notes'       => $request->notes ?? $reservation->notes,
        ]);

        return redirect()->route('staff.reservations.index')
                         ->with('success', 'Đã lưu đơn thành công!');
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
    public function update(Request $request, $reservationId)
    {
        $request->validate([
            'items'            => 'required|array',
            'items.*.id'       => 'required|integer|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:0|max:99',
        ]);

        try {
            DB::transaction(function () use ($request, $reservationId) {
                $reservation = Reservation::lockForUpdate()->findOrFail($reservationId);

                // Không cho sửa món sau khi đã thanh toán / huỷ
                if (!in_array($reservation->status, ['arrived', 'serving', 'ready', 'served'], true)) {
                    throw new \DomainException('Đơn này không còn ở trạng thái có thể gọi thêm món.');
                }

                // Số lượng cũ lấy từ SERVER (không tin old_quantity do client gửi)
                $oldCart = collect($reservation->cart_data ?? [])->keyBy(fn ($i) => (string) $i['id']);

                $wanted = [];
                foreach ($request->input('items') as $item) {
                    $key = (string) $item['id'];
                    $wanted[$key] = ($wanted[$key] ?? 0) + (int) $item['quantity'];
                }
                // Món có trong đơn cũ mà không còn trong form => số lượng mới = 0
                foreach ($oldCart->keys() as $key) {
                    $wanted[$key] = $wanted[$key] ?? 0;
                }

                $chefStatuses = is_array($reservation->chef_statuses) ? $reservation->chef_statuses : [];
                $newCartData  = [];
                $newTotal     = 0;
                $extras       = [];   // [menu, số lượng gọi thêm]
                $reductions   = [];   // [menu_id, số lượng giảm]

                foreach ($wanted as $menuId => $newQty) {
                    $menu = Menu::find($menuId);
                    if (!$menu) continue;

                    $oldQty = (int) ($oldCart[$menuId]['quantity'] ?? 0);
                    $diff   = $newQty - $oldQty;
                    // Giữ nguyên đơn giá đã chốt cho món cũ; món mới lấy giá hiện tại
                    $price  = isset($oldCart[$menuId]) ? $oldCart[$menuId]['price'] : $menu->price;

                    if ($diff > 0) {
                        if ($menu->stock < $diff) {
                            throw new \DomainException("Món '{$menu->name}' hiện không đủ phần đáp ứng (Chỉ còn {$menu->stock} phần).");
                        }
                        $menu->decrement('stock', $diff);
                        $chefStatuses[$menuId] = 'pending';   // bếp nhận diện món gọi thêm
                        $extras[] = [$menu, $diff];
                    } elseif ($diff < 0) {
                        $reductions[] = [$menuId, -$diff];
                    }

                    if ($newQty > 0) {
                        $newCartData[] = [
                            'id'       => $menu->id,
                            'name'     => $menu->name,
                            'price'    => $price,
                            'quantity' => $newQty,
                            'image'    => $menu->image,
                        ];
                        $newTotal += $newQty * $price;
                    } else {
                        unset($chefStatuses[$menuId]);
                    }
                }

                $update = [
                    'cart_data'     => $newCartData,
                    'total_price'   => $newTotal,
                    'chef_statuses' => $chefStatuses,
                ];
                // Có món gọi thêm => đưa đơn lại hàng đợi bếp (KDS)
                if (!empty($extras)) {
                    $update['status'] = 'arrived';
                }
                $reservation->update($update);

                // Đồng bộ hoá đơn của khách (hiện ở /order-history)
                if ($order = $reservation->linkedOrder()) {
                    foreach ($extras as [$menu, $qty]) {
                        OrderItem::create([
                            'order_id'     => $order->id,
                            'menu_id'      => $menu->id,
                            'product_name' => $menu->name,
                            'quantity'     => $qty,
                            'price'        => $menu->price,
                            'is_extra'     => true,           // đánh dấu "Gọi thêm tại bàn"
                        ]);
                    }

                    foreach ($reductions as [$menuId, $qty]) {
                        // giảm dần từ dòng gọi thêm mới nhất, rồi mới đến dòng đặt trước
                        $rows = OrderItem::where('order_id', $order->id)->where('menu_id', $menuId)
                            ->orderByDesc('is_extra')->orderByDesc('id')->get();
                        foreach ($rows as $row) {
                            if ($qty <= 0) break;
                            $take = min($row->quantity, $qty);
                            $row->quantity -= $take;
                            $qty -= $take;
                            $row->quantity > 0 ? $row->save() : $row->delete();
                        }
                    }

                    $order->update(['total_price' => $newTotal]);
                }
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
