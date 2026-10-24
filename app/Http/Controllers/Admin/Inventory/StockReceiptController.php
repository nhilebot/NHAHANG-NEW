<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\StockMovement;
use App\Models\StockReceipt;
use App\Models\Supplier;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class StockReceiptController extends Controller
{
    public function index(Request $request)
    {
        $receipts = StockReceipt::with('supplier')
            ->withCount('items')
            ->when($request->filled('search'), fn ($q) => $q->where('code', 'like', '%' . $request->search . '%'))
            ->when($request->filled('supplier_id'), fn ($q) => $q->where('supplier_id', $request->supplier_id))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('received_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('received_at', '<=', $request->to))
            ->latest('received_at')->latest('id')
            ->paginate(15)->withQueryString();

        return view('admin.inventory.receipts.index', [
            'receipts'  => $receipts,
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.inventory.receipts.create', [
            'suppliers'   => Supplier::where('is_active', true)->orderBy('name')->get(),
            'ingredients' => Ingredient::where('is_active', true)->orderBy('name')
                ->get(['id', 'code', 'name', 'unit', 'reference_price']),
        ]);
    }

    public function store(Request $request, StockService $stock)
    {
        $data = $request->validate([
            'supplier_id'           => ['nullable', 'exists:suppliers,id'],
            'received_at'           => ['required', 'date', 'before_or_equal:today'],
            'note'                  => ['nullable', 'string', 'max:255'],
            'update_price'          => ['nullable', 'boolean'],
            'items'                 => ['required', 'array', 'min:1'],
            'items.*.ingredient_id' => ['required', 'distinct', 'exists:ingredients,id'],
            'items.*.quantity'      => ['required', 'numeric', 'gt:0', 'max:99999999'],
            'items.*.unit_cost'     => ['required', 'numeric', 'min:0', 'max:999999999'],
        ], [
            'items.required'                 => 'Hãy thêm ít nhất một nguyên liệu.',
            'items.*.ingredient_id.required' => 'Hãy chọn nguyên liệu cho mọi dòng.',
            'items.*.ingredient_id.distinct' => 'Một nguyên liệu chỉ nên xuất hiện một dòng trong phiếu.',
            'items.*.quantity.gt'            => 'Số lượng phải lớn hơn 0.',
            'received_at.before_or_equal'    => 'Ngày nhập không được ở tương lai.',
        ]);

        try {
            $receipt = DB::transaction(function () use ($data, $request, $stock) {
                $receipt = StockReceipt::create([
                    'code'        => 'TMP-' . Str::uuid(),   // mã thật đặt sau khi có id
                    'supplier_id' => $data['supplier_id'] ?? null,
                    'received_at' => $data['received_at'],
                    'note'        => $data['note'] ?? null,
                    'created_by'  => $request->user()?->id,
                ]);
                $receipt->update(['code' => sprintf('PN%06d', $receipt->id)]);

                $total = 0;
                foreach ($data['items'] as $row) {
                    $qty  = (float) $row['quantity'];
                    $cost = (float) $row['unit_cost'];
                    $line = round($qty * $cost, 2);
                    $total += $line;

                    $receipt->items()->create([
                        'ingredient_id' => $row['ingredient_id'],
                        'quantity'      => $qty,
                        'unit_cost'     => $cost,
                        'line_total'    => $line,
                    ]);

                    $stock->move(
                        ingredientId: (int) $row['ingredient_id'],
                        type: StockMovement::TYPE_IN,
                        signedQty: $qty,
                        reference: $receipt,
                        unitCost: $cost,
                        note: 'Nhập kho theo phiếu ' . $receipt->code,
                        userId: $request->user()?->id,
                    );

                    if ($request->boolean('update_price') && $cost > 0) {
                        Ingredient::whereKey($row['ingredient_id'])->update(['reference_price' => $cost]);
                    }
                }

                $receipt->update(['total_amount' => $total]);
                return $receipt;
            });
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['items' => $e->getMessage()]);
        }

        return redirect()->route('admin.inventory.receipts.show', $receipt)
            ->with('success', "Đã lập phiếu nhập {$receipt->code} và cập nhật tồn kho.");
    }

    public function show(StockReceipt $receipt)
    {
        $receipt->load(['supplier', 'creator', 'items.ingredient']);
        return view('admin.inventory.receipts.show', compact('receipt'));
    }
}
