<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // ✅ Dùng role_id số thay vì so sánh chuỗi
        // Admin (2) và Staff (3) đều được vào
        $this->middleware(function ($request, $next) {
            $roleId = (int) auth()->user()->role_id;

            if (!in_array($roleId, [\App\Models\Role::ADMIN, \App\Models\Role::STAFF], true)) {
                return redirect('/')->with('error', 'Bạn không có quyền truy cập.');
            }

            return $next($request);
        });
    }

    public function index()
    {
        $categories = Category::paginate(5);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.'
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->save();

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Đã thêm danh mục "' . $request->name . '" thành công!');
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        $menus    = Menu::where('category_id', $id)->get();
        return view('admin.categories.show', compact('category', 'menus'));
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $menus    = Menu::where('category_id', $id)->get();
        return view('admin.categories.edit', compact('category', 'menus'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.'
        ]);

        $category = Category::findOrFail($id);
        $category->name = $request->name;
        $category->save();

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Đã cập nhật danh mục "' . $request->name . '" thành công!');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->menus()->count() > 0) {
            return redirect()->route('admin.categories.index')
                             ->with('error', 'Không thể xóa danh mục "' . $category->name . '" vì còn ' . $category->menus()->count() . ' món ăn!');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Đã xóa danh mục "' . $category->name . '" thành công!');
    }
}