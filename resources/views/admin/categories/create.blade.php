@extends('layouts.admin')

@section('admin_content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/categories-create.css') }}?v=1">
@endpush

<div class="form-container">
    <h3 style="text-align: center; font-weight: bold; margin-bottom: 30px;">THÊM DANH MỤC MỚI</h3>
    
    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        <label style="font-weight: bold;">Tên danh mục (Ví dụ: Hải Sản, Đồ Uống)</label>
        <input type="text" name="name" class="input-custom" placeholder="Nhập tên danh mục..." required>
        
        <button type="submit" class="btn-submit">Lưu Danh Mục</button>
    </form>
</div>
@endsection