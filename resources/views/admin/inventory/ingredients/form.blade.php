@extends('layouts.admin')
@php $title = $ingredient ? 'Sửa nguyên liệu' : 'Thêm nguyên liệu'; @endphp
@section('title', $title)
@section('page_title', $title)
@section('topbar_title', $title)

@section('admin_content')
@include('admin.inventory._style')

<div class="inv-panel inv-form">
    <h2 class="inv-title">{{ $title }}</h2>

    @if($categories->isEmpty())
        <p class="inv-err" style="margin-top:14px">
            Chưa có nhóm nguyên liệu nào. <a href="{{ route('admin.inventory.categories.create') }}">Tạo nhóm trước</a> rồi quay lại.
        </p>
    @endif

    <form method="POST" action="{{ $ingredient ? route('admin.inventory.ingredients.update', $ingredient) : route('admin.inventory.ingredients.store') }}">
        @csrf
        @if($ingredient) @method('PUT') @endif

        <div class="row2">
            <div>
                <label for="code">Mã nguyên liệu</label>
                <input type="text" id="code" name="code" value="{{ old('code', $ingredient->code ?? '') }}" placeholder="{{ $suggestedCode }}" maxlength="30">
                <div class="inv-hint">Để trống để hệ thống tự đặt ({{ $suggestedCode }}).</div>
                @error('code')<div class="inv-err">{{ $message }}</div>@enderror
            </div>
            <div>
                <label for="name">Tên nguyên liệu</label>
                <input type="text" id="name" name="name" value="{{ old('name', $ingredient->name ?? '') }}" required maxlength="150">
                @error('name')<div class="inv-err">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row2">
            <div>
                <label for="category_id">Nhóm nguyên liệu</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Chọn nhóm</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(old('category_id', $ingredient->category_id ?? '') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<div class="inv-err">{{ $message }}</div>@enderror
            </div>
            <div>
                <label for="unit">Đơn vị tính</label>
                <input type="text" id="unit" name="unit" list="units" value="{{ old('unit', $ingredient->unit ?? '') }}" required maxlength="30">
                <datalist id="units">
                    @foreach(['kg','g','lít','ml','chai','lon','hộp','gói','cái','quả'] as $u)<option value="{{ $u }}">@endforeach
                </datalist>
                @error('unit')<div class="inv-err">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row2">
            <div>
                <label for="min_stock">Mức tồn tối thiểu</label>
                <input type="number" id="min_stock" name="min_stock" step="0.001" min="0" value="{{ old('min_stock', $ingredient->min_stock ?? 0) }}">
                <div class="inv-hint">Tồn bằng hoặc thấp hơn mức này sẽ báo "Sắp hết". Để 0 nếu không cần cảnh báo.</div>
                @error('min_stock')<div class="inv-err">{{ $message }}</div>@enderror
            </div>
            <div>
                <label for="reference_price">Giá nhập tham khảo (đ / đơn vị)</label>
                <input type="number" id="reference_price" name="reference_price" step="1" min="0" value="{{ old('reference_price', isset($ingredient->reference_price) ? (int) $ingredient->reference_price : '') }}">
                @error('reference_price')<div class="inv-err">{{ $message }}</div>@enderror
            </div>
        </div>

        <label>Tồn kho hiện tại</label>
        <div class="inv-readonly">
            @if($ingredient)
                <strong>{{ \App\Models\Ingredient::fmt($ingredient->current_stock) }} {{ $ingredient->unit }}</strong>
            @else
                <strong>0</strong>
            @endif
            <div class="inv-hint">Số lượng chỉ thay đổi qua phiếu nhập kho, phiếu xuất kho hoặc kiểm kê để luôn có lịch sử đối chiếu.</div>
        </div>

        <label for="note">Ghi chú</label>
        <input type="text" id="note" name="note" value="{{ old('note', $ingredient->note ?? '') }}" maxlength="255">
        @error('note')<div class="inv-err">{{ $message }}</div>@enderror

        <label class="inv-check">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $ingredient->is_active ?? true))>
            Đang sử dụng
        </label>

        <div class="inv-form-actions">
            <button type="submit" class="btn btn-primary">Lưu nguyên liệu</button>
            <a href="{{ route('admin.inventory.ingredients.index') }}" class="btn btn-ghost">Hủy</a>
        </div>
    </form>
</div>
@endsection
