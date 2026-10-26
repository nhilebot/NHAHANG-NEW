<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Quản lý khuyến mãi (mã giảm giá) — chỉ Admin (route đã bọc role:2).
 * Giai đoạn 1: CHỈ CRUD. Controller này không đọc/ghi reservations, orders, order_items.
 */
class AdminPromotionController extends Controller
{
    public function index(Request $request)
    {
        $query = Promotion::query();

        if ($q = trim((string) $request->input('q'))) {
            $query->where(function ($w) use ($q) {
                $w->where('code', 'like', "%{$q}%")->orWhere('name', 'like', "%{$q}%");
            });
        }

        switch ($request->input('status')) {
            case 'active':
                $query->where('is_active', true)
                      ->where(fn ($w) => $w->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
                      ->where(fn ($w) => $w->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
                      ->where(fn ($w) => $w->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit'));
                break;
            case 'inactive':
                $query->where('is_active', false);
                break;
            case 'expired':
                $query->whereNotNull('ends_at')->where('ends_at', '<', now());
                break;
        }

        $promotions = $query->latest()->paginate(10)->withQueryString();

        return view('admin.promotions.index', compact('promotions'));
    }

    public function create()
    {
        return view('admin.promotions.create', ['promotion' => new Promotion([
            'discount_type'    => Promotion::TYPE_PERCENT,
            'min_order_amount' => 0,
            'is_active'        => true,
        ])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['used_count'] = 0;

        $promotion = Promotion::create($data);

        return redirect()->route('admin.promotions.index')
            ->with('success', 'Đã tạo mã khuyến mãi "' . $promotion->code . '" thành công!');
    }

    public function edit(Promotion $promotion)
    {
        return view('admin.promotions.edit', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $data = $this->validated($request, $promotion);

        // Không cho đặt giới hạn thấp hơn số lượt đã dùng
        if ($data['usage_limit'] !== null && $data['usage_limit'] < $promotion->used_count) {
            return back()->withInput()->withErrors([
                'usage_limit' => 'Giới hạn lượt dùng không được nhỏ hơn số lượt đã dùng (' . $promotion->used_count . ').',
            ]);
        }

        $promotion->update($data);

        return redirect()->route('admin.promotions.index')
            ->with('success', 'Đã cập nhật mã "' . $promotion->code . '".');
    }

    /** Bật/tắt nhanh */
    public function toggle(Promotion $promotion)
    {
        $promotion->update(['is_active' => !$promotion->is_active]);

        return back()->with('success', 'Mã "' . $promotion->code . '" đã được '
            . ($promotion->is_active ? 'bật' : 'tắt') . '.');
    }

    public function destroy(Promotion $promotion)
    {
        // Mã đã được dùng thì giữ lại để đối soát hóa đơn → chỉ cho tắt
        if ($promotion->used_count > 0) {
            return back()->with('error', 'Mã này đã được sử dụng ' . $promotion->used_count
                . ' lần, không thể xóa. Hãy tắt mã thay vì xóa.');
        }

        $code = $promotion->code;
        $promotion->delete();

        return redirect()->route('admin.promotions.index')
            ->with('success', 'Đã xóa mã "' . $code . '".');
    }

    /* --------------------------------------------------------- */

    private function validated(Request $request, ?Promotion $promotion = null): array
    {
        // Chuẩn hóa mã trước khi kiểm tra trùng (AURORA10 == aurora10)
        $request->merge(['code' => strtoupper(trim((string) $request->input('code')))]);

        $data = $request->validate([
            'code'                => ['required', 'string', 'max:30', 'regex:/^[A-Z0-9_-]+$/',
                                      Rule::unique('promotions', 'code')->ignore($promotion?->id)],
            'name'                => ['required', 'string', 'max:255'],
            'description'         => ['nullable', 'string', 'max:1000'],
            'discount_type'       => ['required', Rule::in([Promotion::TYPE_PERCENT, Promotion::TYPE_FIXED])],
            'discount_value'      => ['required', 'integer', 'min:1',
                                      Rule::when($request->input('discount_type') === Promotion::TYPE_PERCENT, ['max:100'])],
            'min_order_amount'    => ['nullable', 'integer', 'min:0'],
            'usage_limit'         => ['nullable', 'integer', 'min:1'],
            'starts_at'           => ['nullable', 'date'],
            'ends_at'             => ['nullable', 'date', 'after:starts_at'],
        ], [
            'code.required'      => 'Vui lòng nhập mã khuyến mãi.',
            'code.regex'         => 'Mã chỉ gồm chữ cái không dấu, số, dấu - hoặc _.',
            'code.unique'        => 'Mã này đã tồn tại.',
            'name.required'      => 'Vui lòng nhập tên chương trình.',
            'discount_value.max' => 'Giảm theo % tối đa là 100.',
            'discount_value.min' => 'Giá trị giảm phải lớn hơn 0.',
            'ends_at.after'      => 'Ngày kết thúc phải sau ngày bắt đầu.',
        ]);

        $data['min_order_amount']    = (int) ($data['min_order_amount'] ?? 0);
        $data['usage_limit']         = $data['usage_limit'] ?? null;
        $data['is_active']           = $request->boolean('is_active');

        return $data;
    }
}
