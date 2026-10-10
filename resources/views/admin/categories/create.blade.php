@extends('layouts.admin')

@section('admin_content')
<style>
    .form-container { max-width: 600px; margin: 30px auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-top: 4px solid #D4AF37; }
    .input-custom { width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; margin-top: 8px; margin-bottom: 20px; }
    .btn-submit { background: #D4AF37; color: white; width: 100%; padding: 12px; border: none; border-radius: 8px; font-weight: bold; text-transform: uppercase; }
</style>

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