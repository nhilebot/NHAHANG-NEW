@extends('layouts.admin')

@section('admin_content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menus-create.css') }}?v=1">
@endpush

<div class="form-container-luxury">
    <div class="form-header">
        <h2>✦ Thêm Món Mới</h2>
        <p style="color:#718096;">Điền thông tin để cập nhật món ăn vào danh sách thực đơn</p>
    </div>

    {{-- ✅ Hiển thị lỗi validation --}}
    @if ($errors->any())
    <div class="alert-errors">
        <strong>⚠️ Vui lòng kiểm tra lại:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            {{-- Tên món ăn --}}
            <div class="col-half">
                <label class="label-custom">Tên món ăn *</label>
                <input type="text" name="name"
                       class="input-custom {{ $errors->has('name') ? 'input-error' : '' }}"
                       value="{{ old('name') }}"
                       placeholder="Ví dụ: Salad Cá Hồi" required>
                @error('name')<span class="error-msg">{{ $message }}</span>@enderror
            </div>

            {{-- Danh mục --}}
            <div class="col-half">
                <label class="label-custom">Thuộc Danh Mục *</label>
                <select name="category_id"
                        class="select-custom {{ $errors->has('category_id') ? 'input-error' : '' }}" required>
                    <option value="">-- Chọn danh mục --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')<span class="error-msg">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="row">
            {{-- Giá bán --}}
            <div class="col-half">
                <label class="label-custom">Giá bán (VNĐ) *</label>
                <input type="number" name="price"
                       class="input-custom {{ $errors->has('price') ? 'input-error' : '' }}"
                       value="{{ old('price') }}"
                       placeholder="Ví dụ: 150000" min="0" required>
                @error('price')<span class="error-msg">{{ $message }}</span>@enderror
            </div>

            {{-- Số lượng tồn kho --}}
            <div class="col-half">
                <label class="label-custom">Số lượng tồn kho</label>
                <input type="number" name="stock"
                       class="input-custom {{ $errors->has('stock') ? 'input-error' : '' }}"
                       value="{{ old('stock', 0) }}"
                       placeholder="Ví dụ: 50" min="0">
                @error('stock')<span class="error-msg">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="row">
            {{-- Hình ảnh --}}
            <div class="col-half">
                <label class="label-custom">Hình ảnh món ăn</label>
                <input type="file" name="image"
                       class="input-custom {{ $errors->has('image') ? 'input-error' : '' }}"
                       accept="image/jpeg,image/png,image/jpg,image/gif">
                <span style="font-size:11px;color:#718096;margin-top:4px;display:block;">
                    JPG, PNG, GIF — tối đa 2MB
                </span>
                @error('image')<span class="error-msg">{{ $message }}</span>@enderror
            </div>

            {{-- Trạng thái --}}
            <div class="col-half">
                <label class="label-custom">Trạng thái *</label>
                <select name="status" class="select-custom {{ $errors->has('status') ? 'input-error' : '' }}" required>
                    <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Đang mở bán</option>
                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Ngừng bán</option>
                </select>
                @error('status')<span class="error-msg">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="row">
            {{-- Mô tả --}}
            <div class="col-full">
                <label class="label-custom">Mô tả món ăn</label>
                <textarea name="description"
                          class="textarea-custom {{ $errors->has('description') ? 'input-error' : '' }}"
                          rows="3"
                          placeholder="Hương vị, thành phần chính...">{{ old('description') }}</textarea>
                @error('description')<span class="error-msg">{{ $message }}</span>@enderror
            </div>
        </div>

        <button type="submit" class="btn-submit-luxury">
            ✦ Xác nhận thêm vào thực đơn
        </button>

        <div style="text-align:center;margin-top:16px;">
            <a href="{{ route('admin.menus.index') }}"
               style="text-decoration:none;color:#718096;font-size:13px;">
                ← Quay lại danh sách
            </a>
        </div>
    </form>
</div>
@endsection