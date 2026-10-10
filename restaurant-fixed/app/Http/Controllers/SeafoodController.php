<?php

namespace App\Http\Controllers;

class SeafoodController extends Controller
{
    // app/Http/Controllers/SeafoodController.php

public function index()
{
    // Lấy các món thuộc danh mục hải sản (hoặc lấy tất cả nếu chưa phân loại)
    $menus = \App\Models\Menu::all(); 
    return view('seafood', compact('menus'));
}
}