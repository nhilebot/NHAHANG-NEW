<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuchitietController extends Controller
{
    public function index(Request $request)
{
    // Lấy tất cả danh mục
    $categories = \App\Models\Category::all();

    // Khởi tạo query
    $menus = \App\Models\Menu::with('category_relation')
                ->where('status', 1);

    // Tìm kiếm theo tên món
    if ($request->filled('search')) {
        $menus->where(function($query) use ($request) {
            $query->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('description', 'LIKE', '%' . $request->search . '%');
        });
    }

    // Lọc theo danh mục
    if ($request->filled('category_id')) {
        $menus->where('category_id', $request->category_id);
    }

    // Lấy dữ liệu
    $menus = $menus->get();

    return view('menu', compact('menus', 'categories'));
}

    public function special()
    {
         $menus = Menu::where('category', 'special')->where('status', 1)->get();
        return view('special', compact('menus'));
    }

    public function salad()
    {
        $menus = Menu::where('category', 'salad')->where('status', 1)->get();
        return view('salad', compact('menus'));
    }

    public function desserts()
    {
        // Nếu trong DB category là 'dessert'
        // Thay get() bằng paginate(số_món_1_trang)
        $menus = Menu::where('category', 'dessert')->where('status', 1)->get();
        return view('desserts', compact('menus'));
    }

    public function drinks()
    {
        $menus = Menu::where('category', 'drink')->where('status', 1)->get();
        return view('drinks', compact('menus'));
    }

    public function seafood()
    {
        $menus = Menu::where('category', 'seafood')->where('status', 1)->get();
        return view('seafood', compact('menus'));
    }

    public function vietnamese()
    {
        $menus = Menu::where('category', 'vietnamese')->where('status', 1)->get();
        return view('vietnamese', compact('menus'));
    }

    public function showDetail($id)
    {
        // 1. Lấy thông tin món đang xem
        $menu = Menu::with(['comments.user'])
                    ->where('status', 1)
                    ->findOrFail($id);
                    
        // 2. Lấy 4 món liên quan (Ưu tiên cùng danh mục)
        $relatedMenus = Menu::where('category', $menu->category)
                    ->where('id', '!=', $id) // Bỏ qua món đang xem
                    ->where('status', 1)
                    ->inRandomOrder()
                    ->limit(4)
                    ->get();

        // 3. Trả về đúng view 'detail' và nhét cả 2 biến vào
        return view('detail', compact('menu', 'relatedMenus'));
    }
}