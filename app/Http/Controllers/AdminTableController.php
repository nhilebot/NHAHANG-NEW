<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class AdminTableController extends Controller
{
    // ✅ Không cần __construct() vì route đã có middleware('role:2')
    // Constructor cũ bị lỗi: strtolower(2) = "2" không khớp với 'admin','staff'

    public function index()
    {
        $tables = Table::paginate(10);
        return view('admin.tables.index', compact('tables'));
    }

    public function create()
    {
        $tables = Table::all();
        $menus  = \App\Models\Menu::all();
        return view('admin.tables.create', compact('tables', 'menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'required'
        ]);

        $table = new Table();
        $table->name   = $request->name;
        $table->status = $request->status;
        $table->save();

        return redirect()->back()->with('success', 'Đã thêm bàn mới!');
    }

    public function show($id)
    {
        $table = Table::findOrFail($id);
        return view('admin.tables.show', compact('table'));
    }

    public function edit($id)
    {
        $table = Table::findOrFail($id);
        return view('admin.tables.edit', compact('table'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'required|in:empty,serving,reserved'
        ]);

        $table = Table::findOrFail($id);
        $table->name   = $request->name;
        $table->status = $request->status;
        $table->save();

        return redirect()->back()->with('success', 'Đã cập nhật bàn thành công!');
    }

    public function destroy($id)
    {
        Table::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Đã xóa bàn thành công!');
    }
}