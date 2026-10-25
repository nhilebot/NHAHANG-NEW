@extends('layouts.admin') {{-- Kiểm tra lại tên layout của bạn --}}

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/tables-index.css') }}?v=1">
@endpush

@section('admin_content')
<div class="panel">
    @php
        $isAdmin = auth()->user()->isAdmin();
        $prefix = $isAdmin ? 'admin' : 'staff';
    @endphp

    <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 class="title-category">Danh sách quản lý bàn</h3>
        {{-- Nút Thêm mới: Tự nhảy route theo Role --}}
        <a href="{{ route($prefix . '.tables.create') }}" class="btn-add" style="background: #1A2228; color: #D4AF37; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">
            + Tạo bàn mới
        </a>
    </div>

    <div class="table-wrap">
        <table class="table-custom" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;">ID</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;">Tên bàn</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;">Trạng thái</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables as $table)
                <tr>
                    <td style="padding: 12px; border-bottom: 1px solid #eee;">#{{ $table->id }}</td>
                    <td style="padding: 12px; border-bottom: 1px solid #eee;"><strong>{{ $table->name }}</strong></td>
                    <td style="padding: 12px; border-bottom: 1px solid #eee;">
                        @if($table->status === 'empty')
                            <span class="badge" style="background: #C6F6D5; color: #22543D; padding: 5px 10px; border-radius: 15px; font-size: 12px;">Sẵn sàng</span>
                        @elseif($table->status === 'reserved')
                            <span class="badge" style="background: #DBEAFE; color: #1E40AF; padding: 5px 10px; border-radius: 15px; font-size: 12px;">Đã đặt</span>
                        @else
                            <span class="badge" style="background: #FEE2E2; color: #742A2A; padding: 5px 10px; border-radius: 15px; font-size: 12px;">Đang phục vụ</span>
                        @endif
                    </td>
                  <td>
    {{-- Nút Sửa: Dùng chung cho cả Admin và Staff (Tự động đổi link nhờ biến $prefix) --}}
    <a href="{{ route($prefix . '.tables.edit', $table->id) }}" class="btn btn-warning btn-sm" style="border-radius: 6px; padding: 5px 12px; text-decoration: none; color: #212529; font-weight: bold; background-color: #ffc107; border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        ✏️ Sửa
    </a>

    {{-- Nút Xóa: Chỉ nên để Admin (role_id = 2) được quyền xóa bàn --}}
    @if(auth()->user()->role_id == 2)
        <form action="{{ route('admin.tables.destroy', $table->id) }}" method="POST" style="display:inline-block; margin-left: 5px;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm" style="border-radius: 6px; padding: 5px 12px; font-weight: bold; background-color: #dc3545; border: none; color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" onclick="return confirm('Bạn có chắc chắn muốn xóa bàn này?')">
                🗑️ Xóa
            </button>
        </form>
    @endif
</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper" style="margin-top: 20px;">
        {{ $tables->links() }}
    </div>
</div>
@endsection