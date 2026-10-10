<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;

class AdminMenuController extends Controller
{
    // ✅ Không cần __construct() — route đã có middleware('role:2')

    public function index(Request $request)
    {
        // 1. Khởi tạo query với relation
        $query = Menu::with('category_relation');

        // 2. Kiểm tra nếu có từ khóa tìm kiếm
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            
            // Sử dụng closure (hàm ẩn danh) để nhóm điều kiện tìm kiếm bằng ( )
            // Tránh việc orWhere làm hỏng các điều kiện lọc khác (nếu có sau này)
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('description', 'LIKE', '%' . $keyword . '%');
            });
        }

        // 3. Thực hiện sắp xếp và phân trang
        $menus = $query->orderBy('category_id', 'asc')
                       ->orderBy('name', 'asc')
                       ->paginate(10);

        // 4. Giữ lại tham số tìm kiếm trên URL khi người dùng bấm sang trang 2, 3...
        $menus->appends($request->all());

        return view('admin.menus.index', compact('menus'));
    }

    public function create(Request $request)
    {
        $categories          = Category::all();
        $selected_category_id = $request->category_id;

        return view('admin.menus.create', compact('categories', 'selected_category_id'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'stock'       => 'nullable|integer|min:0',
            'status'      => 'required|in:0,1',
        ]);

        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
            $data['image'] = 'images/' . $filename;
        }

        if (isset($data['price'])) {
            $data['price'] = (int) str_replace(['.', ',', ' ', 'đ', 'Đ'], '', $data['price']);
        }

        Menu::create($data);

        return redirect()->route('admin.menus.index')->with('success', 'Thêm món thành công!');
    }

    public function edit($id)
    {
        $menu       = Menu::findOrFail($id);
        $categories = Category::all();
        return view('admin.menus.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'stock'       => 'required|integer|min:0',
            'status'      => 'required|in:0,1',
        ]);

        if ($request->hasFile('image')) {
            if ($menu->image && file_exists(public_path($menu->image))) {
                unlink(public_path($menu->image));
            }
            $file     = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
            $data['image'] = 'images/' . $filename;
        }

        $menu->update($data);

        return redirect()->route('admin.menus.index')->with('success', 'Cập nhật món ăn thành công!');
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);

        if ($menu->image && file_exists(public_path($menu->image))) {
            unlink(public_path($menu->image));
        }

        $menu->delete();

        return redirect()->route('admin.menus.index')->with('success', 'Đã xóa món ăn thành công!');
    }
}