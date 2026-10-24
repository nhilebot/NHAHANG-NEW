<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\Recipe;
use App\Services\UnitConverter;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

/**
 * Quản lý công thức định lượng món ăn (chỉ Admin, nằm trong nhóm route admin/inventory).
 * Định lượng luôn tính cho MỘT phần ăn. Chưa liên quan tới xuất kho ở bước này.
 */
class RecipeController extends Controller
{
    use VietnameseValidation;

    // ---------------------------------------------------------------- danh sách

    public function index(Request $request)
    {
        $menus = Menu::with(['category_relation', 'recipe' => fn ($q) => $q->withCount('items')])
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . trim($request->search) . '%'))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->status === 'none', fn ($q) => $q->doesntHave('recipe'))
            ->when($request->status === 'has', fn ($q) => $q->whereHas('recipe'))
            ->when($request->status === 'sample', fn ($q) => $q->whereHas('recipe', fn ($r) => $r->where('is_sample', true)))
            ->orderBy('name')
            ->paginate(20)
            ->appends($request->only('search', 'category_id', 'status'));

        $totalMenus = Menu::count();
        $withRecipe = Menu::has('recipe')->count();

        return view('admin.inventory.recipes.index', [
            'menus'       => $menus,
            'categories'  => Category::orderBy('name')->get(['id', 'name']),
            'totalMenus'  => $totalMenus,
            'withRecipe'  => $withRecipe,
            'noRecipe'    => $totalMenus - $withRecipe,
            'sampleCount' => Recipe::where('is_sample', true)->count(),
        ]);
    }

    // ---------------------------------------------------------------- thêm

    public function create(Request $request)
    {
        // Món đã có công thức thì chuyển sang sửa, không tạo trùng.
        if ($request->filled('menu_id') && ($existing = Recipe::where('menu_id', $request->menu_id)->first())) {
            return redirect()->route('admin.inventory.recipes.edit', $existing)
                ->with('error', 'Món này đã có công thức, bạn đang ở trang sửa.');
        }

        return view('admin.inventory.recipes.form', $this->formData(null) + [
            'selectedMenuId' => $request->query('menu_id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, null);

        try {
            $recipe = DB::transaction(function () use ($data) {
                $recipe = Recipe::create([
                    'menu_id'    => $data['menu_id'],
                    'is_sample'  => $data['is_sample'],
                    'note'       => $data['note'],
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);

                foreach ($data['items'] as $row) {
                    $recipe->items()->create($row);
                }

                return $recipe;
            });
        } catch (QueryException $e) {
            // Hai người bấm lưu cùng lúc cho cùng một món (unique menu_id)
            if ($this->isDuplicateKey($e)) {
                return back()->withInput()->with('error', 'Món này vừa được tạo công thức rồi. Hãy mở công thức đó để sửa.');
            }
            throw $e;
        }

        return redirect()->route('admin.inventory.recipes.show', $recipe)
            ->with('success', 'Đã lưu công thức.');
    }

    // ---------------------------------------------------------------- xem

    public function show(Recipe $recipe)
    {
        $recipe->load(['menu.category_relation', 'items.ingredient', 'creator', 'updater']);

        $rows = [];
        $minPortions = null;
        $cost = 0.0;
        $costComplete = true;
        $hasBroken = false;

        foreach ($recipe->items as $item) {
            $ing = $item->ingredient;
            $row = ['item' => $item, 'ingredient' => $ing, 'perPortionStock' => null, 'portions' => null, 'broken' => false];

            try {
                $perPortion = $item->quantityInStockUnit();   // quy đổi về đơn vị kho
                $row['perPortionStock'] = $perPortion;
                if ($perPortion > 0) {
                    $row['portions'] = (int) floor(((float) $ing->current_stock) / $perPortion);
                    $minPortions = $minPortions === null ? $row['portions'] : min($minPortions, $row['portions']);
                }
                if ($ing->reference_price !== null) {
                    $cost += $perPortion * (float) $ing->reference_price;
                } else {
                    $costComplete = false;
                }
            } catch (InvalidArgumentException $e) {
                $row['broken'] = true;
                $hasBroken = true;
                $costComplete = false;
            }

            $rows[] = $row;
        }

        return view('admin.inventory.recipes.show', [
            'recipe'       => $recipe,
            'rows'         => $rows,
            'minPortions'  => $hasBroken ? null : $minPortions,
            'cost'         => $cost,
            'costComplete' => $costComplete,
            'hasBroken'    => $hasBroken,
        ]);
    }

    // ---------------------------------------------------------------- sửa

    public function edit(Recipe $recipe)
    {
        $recipe->load(['menu', 'items']);

        return view('admin.inventory.recipes.form', $this->formData($recipe) + ['selectedMenuId' => $recipe->menu_id]);
    }

    public function update(Request $request, Recipe $recipe)
    {
        $data = $this->validated($request, $recipe);

        DB::transaction(function () use ($recipe, $data) {
            $recipe = Recipe::whereKey($recipe->id)->lockForUpdate()->firstOrFail();

            $recipe->update([
                'is_sample'  => $data['is_sample'],
                'note'       => $data['note'],
                'updated_by' => auth()->id(),
            ]);

            // Đồng bộ theo nguyên liệu: sửa dòng đã có, thêm dòng mới, bỏ dòng không còn trong form.
            $existing = $recipe->items()->get()->keyBy('ingredient_id');
            $keep = [];

            foreach ($data['items'] as $row) {
                $keep[] = $row['ingredient_id'];
                if ($line = $existing->get($row['ingredient_id'])) {
                    $line->update(['quantity' => $row['quantity'], 'unit' => $row['unit']]);
                } else {
                    $recipe->items()->create($row);
                }
            }

            $recipe->items()->whereNotIn('ingredient_id', $keep)->delete();
        });

        return redirect()->route('admin.inventory.recipes.show', $recipe)
            ->with('success', 'Đã cập nhật công thức.');
    }

    // ---------------------------------------------------------------- xóa

    public function destroy(Recipe $recipe)
    {
        $name = $recipe->menu->name ?? 'món này';
        $recipe->delete();   // các dòng nguyên liệu bị xóa theo (cascade); không ảnh hưởng tồn kho hay lịch sử kho

        return redirect()->route('admin.inventory.recipes.index')
            ->with('success', "Đã xóa công thức của \"{$name}\".");
    }

    // ---------------------------------------------------------------- nội bộ

    /** Dữ liệu cho form thêm/sửa. */
    private function formData(?Recipe $recipe): array
    {
        $usedIds = $recipe ? $recipe->items->pluck('ingredient_id')->all() : [];

        // Chỉ nguyên liệu đang sử dụng (giữ lại nguyên liệu đã ngừng nếu công thức cũ vẫn đang dùng)
        $ingredients = Ingredient::where(function ($q) use ($usedIds) {
                $q->where('is_active', true)->orWhereIn('id', $usedIds);
            })
            ->orderBy('name')->get(['id', 'code', 'name', 'unit', 'is_active']);

        $ingredientData = [];
        foreach ($ingredients as $i) {
            $ingredientData[$i->id] = [
                'name'    => $i->name,
                'code'    => $i->code,
                'unit'    => $i->unit,
                'allowed' => UnitConverter::allowedFor($i->unit),
                'active'  => (bool) $i->is_active,
            ];
        }

        return [
            'recipe'         => $recipe,
            'menusAvailable' => $recipe ? collect() : Menu::doesntHave('recipe')->orderBy('name')->get(['id', 'name']),
            'ingredientData' => $ingredientData,
        ];
    }

    /**
     * Kiểm tra dữ liệu form. Trả về mảng đã chuẩn hóa:
     * ['menu_id', 'is_sample', 'note', 'items' => [['ingredient_id','quantity','unit'], ...]]
     */
    private function validated(Request $request, ?Recipe $recipe): array
    {
        $rules = [
            'is_sample'             => 'nullable|boolean',
            'note'                  => 'nullable|string|max:255',
            'items'                 => 'required|array|min:1|max:50',
            'items.*.ingredient_id' => 'required|distinct|exists:ingredients,id',
            'items.*.quantity'      => 'required|numeric|gt:0|max:99999999',
            'items.*.unit'          => 'required|string|max:30',
        ];
        if (!$recipe) {
            $rules['menu_id'] = 'required|exists:menus,id|unique:recipes,menu_id';
        }

        $messages = $this->messages() + [
            'gt'                  => ':attribute phải lớn hơn 0.',
            'distinct'            => 'Một nguyên liệu chỉ được xuất hiện một lần trong công thức.',
            'items.required'      => 'Hãy thêm ít nhất một nguyên liệu.',
            'items.min'           => 'Hãy thêm ít nhất một nguyên liệu.',
            'menu_id.unique'      => 'Món này đã có công thức. Hãy mở công thức đó để sửa.',
            'boolean'             => ':attribute không hợp lệ.',
            'array'               => ':attribute không hợp lệ.',
        ];
        $attributes = $this->attributes() + [
            'menu_id'               => 'Món',
            'items'                 => 'Danh sách nguyên liệu',
            'items.*.ingredient_id' => 'Nguyên liệu',
            'items.*.quantity'      => 'Định lượng',
            'items.*.unit'          => 'Đơn vị',
        ];

        $data = $request->validate($rules, $messages, $attributes);

        // --- kiểm tra theo từng dòng: nguyên liệu còn dùng được, đơn vị tương thích, định lượng không bị làm tròn về 0
        $ingredients = Ingredient::whereIn('id', collect($data['items'])->pluck('ingredient_id'))->get()->keyBy('id');
        $alreadyInRecipe = $recipe ? $recipe->items()->pluck('ingredient_id')->all() : [];

        $errors = [];
        $items = [];

        foreach ($data['items'] as $key => $row) {
            $ing = $ingredients[$row['ingredient_id']];
            $unit = UnitConverter::normalize($row['unit']);
            $qty = round((float) $row['quantity'], 3);

            if (!$ing->is_active && !in_array($ing->id, $alreadyInRecipe, true)) {
                $errors["items.$key.ingredient_id"] = "Nguyên liệu \"{$ing->name}\" đã ngừng sử dụng, không thể thêm vào công thức.";
            }
            if (!UnitConverter::compatible($unit, $ing->unit)) {
                $errors["items.$key.unit"] = "Đơn vị \"{$row['unit']}\" không dùng được cho \"{$ing->name}\" (kho tính theo \"{$ing->unit}\"). Chỉ được chọn: "
                    . implode(', ', UnitConverter::allowedFor($ing->unit)) . '.';
            }
            if ($qty <= 0) {
                $errors["items.$key.quantity"] = "Định lượng của \"{$ing->name}\" quá nhỏ (tối thiểu 0,001).";
            }

            $items[] = ['ingredient_id' => $ing->id, 'quantity' => $qty, 'unit' => $unit];
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        return [
            'menu_id'   => $recipe?->menu_id ?? (int) $data['menu_id'],
            'is_sample' => $request->boolean('is_sample'),
            'note'      => $data['note'] ?? null,
            'items'     => $items,
        ];
    }
}
