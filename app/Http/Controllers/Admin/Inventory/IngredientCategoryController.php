<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\IngredientCategory;
use Illuminate\Http\Request;

class IngredientCategoryController extends Controller
{
    use VietnameseValidation;

    public function index(Request $request)
    {
        $categories = IngredientCategory::withCount('ingredients')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . trim($request->search) . '%');
            })
            ->orderBy('name')
            ->paginate(15)
            ->appends($request->only('search'));

        return view('admin.inventory.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.inventory.categories.form', ['category' => null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:ingredient_categories,name',
            'description' => 'nullable|string|max:255',
        ], $this->messages(), $this->attributes());

        IngredientCategory::create($data);

        return redirect()->route('admin.inventory.categories.index')
            ->with('success', 'Đã thêm nhóm nguyên liệu.');
    }

    public function edit(IngredientCategory $category)
    {
        return view('admin.inventory.categories.form', compact('category'));
    }

    public function update(Request $request, IngredientCategory $category)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:ingredient_categories,name,' . $category->id,
            'description' => 'nullable|string|max:255',
        ], $this->messages(), $this->attributes());

        $category->update($data);

        return redirect()->route('admin.inventory.categories.index')
            ->with('success', 'Đã cập nhật nhóm nguyên liệu.');
    }

    public function destroy(IngredientCategory $category)
    {
        $count = $category->ingredients()->count();
        if ($count > 0) {
            return back()->with('error', "Nhóm \"{$category->name}\" đang có {$count} nguyên liệu, không thể xóa. Hãy chuyển các nguyên liệu sang nhóm khác trước.");
        }

        $category->delete();

        return redirect()->route('admin.inventory.categories.index')
            ->with('success', 'Đã xóa nhóm nguyên liệu.');
    }
}