@extends('layouts.admin')

@section('admin_content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menus-edit.css') }}?v=1">
@endpush

<div class="form-edit-luxury">
    <h3 class="text-center" style="font-family: 'Playfair Display', serif; font-weight: 700; margin-bottom: 30px;">CHỈNH SỬA MÓN ĂN</h3>

    <form action="{{ route('admin.menus.update', $menu->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{-- Lưu ý: Trong web.php bạn dùng POST cho update nên ở đây không cần @method('PUT') --}}
        
        <div class="row">
            <div class="col-md-6">
                <label class="label-custom">Tên món ăn</label>
                <input type="text" name="name" class="input-custom" value="{{ $menu->name }}" required>
            </div>
            <div class="col-md-6">
                <label class="label-custom">Thuộc Danh mục</label>
                <select name="category_id" class="input-custom" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $menu->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <label class="label-custom">Giá bán (VNĐ)</label>
                <input type="number" name="price" class="input-custom" value="{{ $menu->price }}" required>
            </div>
            <div class="col-md-4">
                <label class="label-custom">Số lượng (Kho)</label>
                <input type="number" name="stock" class="input-custom" value="{{ $menu->stock }}" required>
            </div>
            <div class="col-md-4">
                <label class="label-custom">Trạng thái</label>
                <select name="status" class="input-custom">
                    <option value="1" {{ $menu->status == 1 ? 'selected' : '' }}>Đang bán</option>
                    <option value="0" {{ $menu->status == 0 ? 'selected' : '' }}>Ngừng bán</option>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="label-custom">Hình ảnh (Để trống nếu không đổi)</label>
            <input type="file" name="image" class="input-custom">
            @if($menu->image)
                <p class="small text-muted">Ảnh hiện tại:</p>
                <img src="{{ asset($menu->image) }}" width="100" class="rounded shadow-sm">
            @endif
        </div>

        <div class="mb-4">
            <label class="label-custom">Mô tả món ăn</label>
            <textarea name="description" class="input-custom" rows="4">{{ $menu->description }}</textarea>
        </div>

        <button type="submit" class="btn-update">Cập nhật thay đổi</button>
        
        <div class="text-center mt-3">
            <a href="{{ route('admin.menus.index') }}" class="text-muted" style="text-decoration:none; font-size: 13px;"> Quay lại danh sách</a>
        </div>
    </form>
</div>
@endsection