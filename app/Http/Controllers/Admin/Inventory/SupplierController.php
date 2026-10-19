<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    use VietnameseValidation;

    public function index(Request $request)
    {
        $suppliers = Supplier::search($request->search)
            ->when($request->status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($request->status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->orderBy('name')
            ->paginate(15)
            ->appends($request->only('search', 'status'));

        return view('admin.inventory.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.inventory.suppliers.form', [
            'supplier'      => null,
            'suggestedCode' => Supplier::newCode(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['code'] = $data['code'] ?: Supplier::newCode();

        try {
            Supplier::create($data);
        } catch (QueryException $e) {
            if ($this->isDuplicateKey($e)) {
                return back()->withInput()->with('error', 'Mã nhà cung cấp vừa bị trùng, vui lòng bấm Lưu lại.');
            }
            throw $e;
        }

        return redirect()->route('admin.inventory.suppliers.index')
            ->with('success', 'Đã thêm nhà cung cấp.');
    }

    public function show(Supplier $supplier)
    {
        return view('admin.inventory.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.inventory.suppliers.form', [
            'supplier'      => $supplier,
            'suggestedCode' => $supplier->code,
        ]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $this->validated($request, $supplier);
        $data['code'] = $data['code'] ?: $supplier->code;

        $supplier->update($data);

        return redirect()->route('admin.inventory.suppliers.index')
            ->with('success', 'Đã cập nhật nhà cung cấp.');
    }

    public function destroy(Supplier $supplier)
    {
        try {
            $supplier->delete();
        } catch (QueryException $e) {
            // Khi đã có phiếu nhập tham chiếu (giai đoạn sau), khóa ngoại sẽ chặn xóa.
            return back()->with('error', "Không thể xóa \"{$supplier->name}\" vì đã có dữ liệu liên quan. Hãy chuyển sang trạng thái ngừng hợp tác.");
        }

        return redirect()->route('admin.inventory.suppliers.index')
            ->with('success', 'Đã xóa nhà cung cấp.');
    }

    private function validated(Request $request, ?Supplier $supplier = null): array
    {
        $ignore = $supplier ? ',' . $supplier->id : '';

        $data = $request->validate([
            'code'      => 'nullable|string|alpha_dash|max:30|unique:suppliers,code' . $ignore,
            'name'      => 'required|string|max:150',
            'phone'     => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:150',
            'address'   => 'nullable|string|max:255',
            'note'      => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ], $this->messages(), $this->attributes());

        $data['code']      = isset($data['code']) ? strtoupper($data['code']) : null;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}