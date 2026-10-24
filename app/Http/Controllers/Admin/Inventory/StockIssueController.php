<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\StockIssue;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class StockIssueController extends Controller
{
    public function index(Request $request)
    {
        $issues = StockIssue::withCount('items')
            ->when($request->filled('search'), fn ($q) => $q->where('code', 'like', '%' . $request->search . '%'))
            ->when($request->filled('reason'), fn ($q) => $q->where('reason', $request->reason))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('issued_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('issued_at', '<=', $request->to))
            ->latest('issued_at')->latest('id')
            ->paginate(15)->withQueryString();

        return view('admin.inventory.issues.index', compact('issues'));
    }

    public function create()
    {
        return view('admin.inventory.issues.create', [
            // chỉ nguyên liệu đang còn hàng mới xuất được
            'ingredients' => Ingredient::where('is_active', true)->where('current_stock', '>', 0)
                ->orderBy('name')->get(['id', 'code', 'name', 'unit', 'current_stock']),
        ]);
    }

    public function store(Request $request, StockService $stock)
    {
        $data = $request->validate([
            'reason'                => ['required', Rule::in(array_keys(StockIssue::REASONS))],
            'issued_at'             => ['required', 'date', 'before_or_equal:today'],
            'note'                  => ['nullable', 'string', 'max:255'],
            'items'                 => ['required', 'array', 'min:1'],
            'items.*.ingredient_id' => ['required', 'distinct', 'exists:ingredients,id'],
            'items.*.quantity'      => ['required', 'numeric', 'gt:0', 'max:99999999'],
        ], [
            'items.required'                 => 'Hãy thêm ít nhất một nguyên liệu.',
            'items.*.ingredient_id.required' => 'Hãy chọn nguyên liệu cho mọi dòng.',
            'items.*.ingredient_id.distinct' => 'Một nguyên liệu chỉ nên xuất hiện một dòng trong phiếu.',
            'items.*.quantity.gt'            => 'Số lượng phải lớn hơn 0.',
            'issued_at.before_or_equal'      => 'Ngày xuất không được ở tương lai.',
        ]);

        // Hao hụt / hỏng / hủy / khác nên có ghi chú để sau này truy nguyên nhân.
        if ($data['reason'] !== 'kitchen' && blank($data['note'] ?? null)) {
            return back()->withInput()->withErrors(['note' => 'Hãy ghi chú nguyên nhân khi xuất vì lý do này.']);
        }

        try {
            $issue = DB::transaction(function () use ($data, $request, $stock) {
                $issue = StockIssue::create([
                    'code'       => 'TMP-' . Str::uuid(),
                    'reason'     => $data['reason'],
                    'issued_at'  => $data['issued_at'],
                    'note'       => $data['note'] ?? null,
                    'created_by' => $request->user()?->id,
                ]);
                $issue->update(['code' => sprintf('PX%06d', $issue->id)]);

                $total = 0;
                foreach ($data['items'] as $row) {
                    $qty  = (float) $row['quantity'];
                    $cost = (float) (Ingredient::whereKey($row['ingredient_id'])->value('reference_price') ?? 0);
                    $line = round($qty * $cost, 2);
                    $total += $line;

                    $issue->items()->create([
                        'ingredient_id' => $row['ingredient_id'],
                        'quantity'      => $qty,
                        'unit_cost'     => $cost,
                        'line_total'    => $line,
                    ]);

                    // Số lượng ÂM = giảm tồn. Không đủ tồn thì StockService ném lỗi -> hủy cả phiếu.
                    $stock->move(
                        ingredientId: (int) $row['ingredient_id'],
                        type: StockMovement::TYPE_OUT,
                        signedQty: -$qty,
                        reference: $issue,
                        unitCost: $cost,
                        note: StockIssue::REASONS[$data['reason']] . ' - phiếu ' . $issue->code,
                        userId: $request->user()?->id,
                    );
                }

                $issue->update(['total_amount' => $total]);
                return $issue;
            });
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['items' => $e->getMessage()]);
        }

        return redirect()->route('admin.inventory.issues.show', $issue)
            ->with('success', "Đã lập phiếu xuất {$issue->code} và trừ tồn kho.");
    }

    public function show(StockIssue $issue)
    {
        $issue->load(['creator', 'items.ingredient']);
        return view('admin.inventory.issues.show', compact('issue'));
    }
}
