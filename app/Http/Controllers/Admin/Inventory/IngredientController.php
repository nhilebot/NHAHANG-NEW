<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\IngredientCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    use VietnameseValidation;

    public function index(Request $request)
    {
        $ingredients = Ingredient::with('category')
            ->search($request->search)
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->category_id))
            ->stockLevel($request->level)
            ->when($request->status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($request->status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->orderBy('name')
            ->paginate(15)
            ->appends($request->only('search', 'category_id', 'level', 'status'));

        return view('admin.inventory.ingredients.index', [
            'ingredients' => $ingredients,
            'categories'  => IngredientCategory::orderBy('name')->get(),
            'lowCount'    => Ingredient::stockLevel('low')->count(),
            'outCount'    => Ingredient::stockLevel('out')->count(),
            'totalCount'  => Ingredient::count(),
        ]);
    }

    public function create()
    {
        return view('admin.inventory.ingredients.form', [
            'ingredient'    => null,
            'categories'    => IngredientCategory::orderBy('name')->get(),
            'suggestedCode' => Ingredient::newCode(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['code'] = $data['code'] ?: Ingredient::newCode();

        // Tồn đầu luôn = 0: số lượng chỉ thay đổi qua phiếu nhập/xuất/kiểm kê (có lịch sử).
        try {
            Ingredient::create($data);
        } catch (QueryException $e) {
            if ($this->isDuplicateKey($e)) {
                return back()->withInput()->with('error', 'Mã nguyên liệu vừa bị trùng, vui lòng bấm Lưu lại.');
            }
            throw $e;
        }

        return redirect()->route('admin.inventory.ingredients.index')
            ->with('success', 'Đã thêm nguyên liệu. Tồn kho ban đầu là 0, hãy dùng phiếu nhập kho để ghi số lượng.');
    }

    public function show(Ingredient $ingredient)
    {
        $ingredient->load('category');

        return view('admin.inventory.ingredients.show', compact('ingredient'));
    }

    public function edit(Ingredient $ingredient)
    {
        return view('admin.inventory.ingredients.form', [
            'ingredient'    => $ingredient,
            'categories'    => IngredientCategory::orderBy('name')->get(),
            'suggestedCode' => $ingredient->code,
        ]);
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $data = $this->validated($request, $ingredient);
        $data['code'] = $data['code'] ?: $ingredient->code;

        $ingredient->update($data);

        return redirect()->route('admin.inventory.ingredients.index')
            ->with('success', 'Đã cập nhật nguyên liệu.');
    }

    public function destroy(Ingredient $ingredient)
    {
        if ($ingredient->current_stock > 0) {
            return back()->with('error', "\"{$ingredient->name}\" còn tồn " . Ingredient::fmt($ingredient->current_stock) . " {$ingredient->unit}, không thể xóa. Hãy xuất hết hoặc chuyển sang ngừng sử dụng.");
        }

        try {
            $ingredient->delete();
        } catch (QueryException $e) {
            // Khi đã có lịch sử nhập/xuất (giai đoạn sau), khóa ngoại sẽ chặn xóa.
            return back()->with('error', "Không thể xóa \"{$ingredient->name}\" vì đã có lịch sử kho. Hãy chuyển sang ngừng sử dụng.");
        }

        return redirect()->route('admin.inventory.ingredients.index')
            ->with('success', 'Đã xóa nguyên liệu.');
    }

    private function validated(Request $request, ?Ingredient $ingredient = null): array
    {
        $ignore = $ingredient ? ',' . $ingredient->id : '';

        $data = $request->validate([
            'code'            => 'nullable|string|alpha_dash|max:30|unique:ingredients,code' . $ignore,
            'name'            => 'required|string|max:150|unique:ingredients,name' . $ignore,
            'category_id'     => 'required|exists:ingredient_categories,id',
            'unit'            => 'required|string|max:30',
            'min_stock'       => 'nullable|numeric|min:0|max:99999999999',
            'reference_price' => 'nullable|numeric|min:0|max:99999999999',
            'note'            => 'nullable|string|max:255',
            'is_active'       => 'nullable|boolean',
        ], $this->messages(), $this->attributes());

        $data['code']      = isset($data['code']) ? strtoupper($data['code']) : null;
        $data['min_stock'] = $data['min_stock'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}