@extends('layouts.admin')

@section('admin_content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap');

    .form-container-luxury {
        max-width: 900px;
        margin: 30px auto;
        background: #ffffff;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.06);
        border-top: 5px solid #D4AF37;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .form-header { text-align: center; margin-bottom: 40px; }
    .form-header h2 {
        font-family: 'Playfair Display', serif;
        color: #1A2228;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .label-custom {
        display: block;
        font-weight: 700;
        color: #4A5568;
        font-size: 12px;
        text-transform: uppercase;
        margin-bottom: 8px;
        letter-spacing: 0.5px;
    }
    .input-custom, .select-custom, .textarea-custom {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        background-color: #F8FAFC;
        font-size: 15px;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }
    .input-custom:focus, .select-custom:focus, .textarea-custom:focus {
        background-color: #ffffff;
        border-color: #D4AF37;
        outline: none;
        box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
    }
    .input-error { border-color: #e53e3e !important; background-color: #fff5f5 !important; }
    .error-msg { color: #e53e3e; font-size: 12px; margin-top: 5px; display: block; }
    .alert-errors {
        background: #fff5f5;
        border: 1px solid #fed7d7;
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 24px;
        color: #c53030;
        font-size: 14px;
    }
    .alert-errors ul { margin: 8px 0 0; padding-left: 20px; }
    .btn-submit-luxury {
        background: #D4AF37;
        color: white;
        border: none;
        width: 100%;
        padding: 16px;
        border-radius: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 20px;
        box-shadow: 0 8px 20px rgba(212, 175, 55, 0.2);
    }
    .btn-submit-luxury:hover {
        background: #1A2228;
        color: #D4AF37;
        transform: translateY(-2px);
    }
    .row { display: flex; gap: 20px; flex-wrap: wrap; }
    .col-half { flex: 1; min-width: 200px; margin-bottom: 20px; }
    .col-full { flex: 100%; margin-bottom: 20px; }
</style>

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