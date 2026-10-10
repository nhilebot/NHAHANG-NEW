@extends('layouts.admin')

@section('admin_content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap');

    .edit-table-box {
        background: #fff;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        max-width: 600px;
        margin: 40px auto;
        border-top: 5px solid #D4AF37;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
</style>

<div class="container">
    <div class="edit-table-box">
        <h3 class="text-center" style="color: #1A2228; font-family: 'Playfair Display', serif; font-weight: 700; margin-bottom: 5px;">
            Chỉnh Sửa Bàn
        </h3>
        <p class="text-center text-muted mb-4">Đang thao tác: <strong style="color: #D4AF37;">{{ $table->name }}</strong></p>

        <form action="{{ route('admin.tables.update', $table->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group mb-4">
                <label style="font-weight: 600; color: #4A5568;">Tên bàn <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ $table->name }}" required style="height: 50px; border-radius: 8px; border: 1px solid #E2E8F0;">
            </div>

            <div class="form-group mb-4">
                <label style="font-weight: 600; color: #4A5568;">Trạng thái bàn <span class="text-danger">*</span></label>
                <select name="status" class="form-control" required style="height: 50px; border-radius: 8px; border: 1px solid #E2E8F0;">
                    <option value="empty" {{ $table->status == 'empty' ? 'selected' : '' }}>Trống</option>
                    <option value="serving" {{ $table->status == 'serving' ? 'selected' : '' }}>Đang phục vụ</option>
                    <option value="reserved" {{ $table->status == 'reserved' ? 'selected' : '' }}>Đã đặt</option>
                </select>
            </div>

            <div class="d-flex justify-content-between mt-5">
                <a href="{{ route('admin.tables.index') }}" class="btn btn-secondary" style="border-radius: 8px; padding: 12px 25px; font-weight: 600;">
                    <i class="fa fa-arrow-left"></i> Quay lại
                </a>
                <button type="submit" class="btn" style="background: #D4AF37; color: #FFF; border: none; border-radius: 8px; padding: 12px 25px; font-weight: bold;">
                    <i class="fa fa-save"></i> CẬP NHẬT
                </button>
            </div>
        </form>
    </div>
</div>
@endsection