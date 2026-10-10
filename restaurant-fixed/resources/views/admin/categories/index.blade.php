@extends('layouts.admin')

@section('admin_content')
<style>
    /* ===== CSS LUXURY THEME ===== */
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap');

    .panel-custom {
        background: #ffffff;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        margin-top: 20px;
        border-top: 4px solid #D4AF37;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .title-category {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        color: #1A2228;
        margin: 0;
        font-size: 26px;
        letter-spacing: 1px;
    }

    /* NÚT THÊM DANH MỤC */
    .btn-add-new {
        background-color: #D4AF37;
        color: #FFF;
        border-radius: 8px;
        padding: 12px 24px;
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.25);
        display: inline-flex;
        align-items: center;
        margin-left: auto; 
    }
    .btn-add-new i { font-size: 15px; }
    .btn-add-new:hover {
        background-color: #1A2228;
        color: #D4AF37;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(26, 34, 40, 0.2);
    }

    /* Bảng hiển thị */
    .table-custom {
        margin-top: 25px;
        width: 100%;
        margin-bottom: 0; 
    }
    .table-custom th {
        background-color: #FCFBF8;
        color: #4A5568;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 13px;
        letter-spacing: 1px;
        padding: 18px 15px;
        border-bottom: 2px solid #EAEAEA;
    }
    .table-custom td {
        padding: 18px 15px;
        vertical-align: middle;
        border-bottom: 1px dashed #EAEAEA;
        color: #1A2228;
        font-size: 15px;
    }
    
    .badge-count {
        background-color: #F7FAFC;
        color: #4A5568;
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid #E2E8F0;
    }

    /* Nhóm nút Hành động */
    .action-buttons { display: flex; gap: 8px; justify-content: center; }
    
    .btn-xem { background: #F0F4F8; color: #2B6CB0; border: 1px solid #BEE3F8; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 13px; transition: all 0.3s; text-decoration: none; }
    .btn-xem:hover { background: #2B6CB0; color: #FFF; }

    .btn-sua { background: #FFFBEB; color: #D97706; border: 1px solid #FDE68A; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 13px; transition: all 0.3s; text-decoration: none; }
    .btn-sua:hover { background: #D97706; color: #FFF; }

    .btn-xoa { background: #FFF5F5; color: #C53030; border: 1px solid #FED7D7; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 13px; transition: all 0.3s; cursor: pointer; }
    .btn-xoa:hover { background: #C53030; color: #FFF; }

    /* ===== CSS FIX PHÂN TRANG SIÊU XỊN ===== */
    .pagination-wrapper {
        margin-top: 25px;
        display: flex;
        justify-content: flex-end; /* Căn phải */
    }
    .pagination {
        display: flex;
        list-style: none; /* Xóa dấu chấm đen */
        padding: 0;
        margin: 0;
        gap: 5px; /* Khoảng cách giữa các nút */
    }
    .page-item .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        color: #4A5568;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none; /* Xóa gạch chân */
        transition: all 0.3s ease;
        background-color: #FFF;
    }
    
    /* Trạng thái đang chọn (Màu Vàng Đồng) */
    .page-item.active .page-link, 
    .page-item.active span.page-link {
        background-color: #D4AF37;
        border-color: #D4AF37;
        color: #FFF;
        box-shadow: 0 4px 10px rgba(212, 175, 55, 0.3);
    }

    /* Trạng thái rê chuột vào (Màu Đen) */
    .page-item .page-link:hover:not(.active) {
        background-color: #1A2228;
        color: #D4AF37;
        border-color: #1A2228;
    }

    /* Trạng thái không bấm được (Disable) */
    .page-item.disabled .page-link, 
    .page-item.disabled span.page-link {
        color: #A0AEC0;
        background-color: #F7FAFC;
        border-color: #E2E8F0;
        cursor: not-allowed;
    }
</style>

<section class="panel-custom">
    <div class="d-flex align-items-center mb-4">
        <h3 class="title-category">
            <i class="fa fa-list" style="color: #D4AF37; margin-right: 8px;"></i> Quản Lý Danh Mục
        </h3>
        
        <!-- <a href="{{ route('admin.categories.create') }}" class="btn-add-new">
            <i class="fa fa-plus mr-2"></i> Thêm Danh Mục
        </a> -->
    </div>

    <div class="table-responsive">
        <table class="table table-custom table-hover">
            <thead>
                <tr>
                    <th style="width: 80px; text-align: center;">ID</th>
                    <th>Tên danh mục</th>
                    <th style="width: 180px; text-align: center;">Số lượng món</th>
                    <th style="width: 250px; text-align: center;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td class="text-center font-weight-bold text-muted">{{ $category->id }}</td>
                        
                        <td>
                            <strong style="font-size: 16px;">{{ $category->name }}</strong>
                        </td>
                        
                        <td class="text-center">
                            @php
                                $count = \App\Models\Menu::where('category_id', $category->id)->count();
                            @endphp
                            <span class="badge-count">{{ $count }} món</span>
                        </td>
                        
                        <td class="text-center">
                            <div class="action-buttons">
                                <a href="{{ route('admin.categories.show', $category->id) }}" class="btn-xem" title="Xem danh sách món">
                                    <i class="fa fa-eye"></i> Xem món
                                </a>

                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn-sua" title="Đổi tên danh mục">
                                    <i class="fa fa-edit"></i> Sửa
                                </a>

                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn xóa danh mục {{ $category->name }} không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-xoa" title="Xóa danh mục">
                                        <i class="fa fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <i class="fa fa-folder-open" style="font-size: 40px; color: #E2E8F0; margin-bottom: 15px; display: block;"></i>
                            <p style="color: #718096; font-size: 15px; margin: 0;">Chưa có danh mục nào trong hệ thống.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- HIỂN THỊ CÁC NÚT BẤM PHÂN TRANG --}}
    <div class="pagination-wrapper">
        {{ $categories->links('pagination::bootstrap-4') }}
    </div>

</section>
@endsection