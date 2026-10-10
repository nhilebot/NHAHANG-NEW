<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;   // Bắt buộc phải có dòng này để gọi data Thực đơn
use App\Models\Table;  // Bắt buộc phải có dòng này để gọi data Bàn

class TableController extends Controller
{
    public function index()
{
    // 1. Lấy tất cả bàn từ database
   $tables = Table::paginate(10); // Lấy 10 dòng mỗi trang và cho phép phân trang 

    // 2. Truyền biến $tables sang view của khách hàng
   return view('admin.tables.index', compact('tables'));
}

    // Thêm hàm create() để lấy dữ liệu chuẩn từ Database
    public function create()
    {
        // Sử dụng ::all() để lấy dữ liệu dưới dạng Danh sách (Collection)
        // Điều này giúp loại bỏ hoàn toàn lỗi "string given" trong foreach
        $menus = Menu::all();
        $tables = Table::all();

        // Trả về đúng đường dẫn file giao diện bạn đang làm
        return view('admin.tables.create', compact('menus', 'tables'));
    }
    /**
     * Hiển thị Form chỉnh sửa bàn (Dùng chung cho Admin và Staff)
     */
    public function edit($id)
    {
        // 1. Tìm bàn cần sửa trong Database
        $table = \App\Models\Table::findOrFail($id);
        
        // 2. ÉP TRẢ VỀ DÙNG CHUNG GIAO DIỆN Ở THƯ MỤC ADMIN
        // (Không lo bị lỗi quyền vì trong file giao diện đó chúng ta đã có code tự động nhận diện admin/staff rồi)
        return view('admin.tables.edit', compact('table'));
    }

    /**
     * Xử lý lưu dữ liệu khi bấm nút "Lưu thay đổi"
     */
    public function update(Request $request, $id)
    {
        // 1. Kiểm tra dữ liệu đầu vào
        $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'required|string'
            // Nếu bảng tables của bạn có sức chứa (capacity), hãy thêm vào: 'capacity' => 'required|integer|min:1'
        ], [
            'name.required' => 'Vui lòng nhập tên bàn',
        ]);

        // 2. Cập nhật vào Database
        $table = \App\Models\Table::findOrFail($id);
        $table->update([
            'name'   => $request->name,
            'status' => $request->status,
        ]);

        // 3. Quay về trang danh sách tương ứng
        $roleId = auth()->user()->role_id;
        $prefix = ((int) $roleId === \App\Models\Role::ADMIN) ? 'admin' : 'staff';

        return redirect()->route($prefix . '.tables.index')
                         ->with('success', 'Đã cập nhật thông tin bàn thành công!');
    }
}