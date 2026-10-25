@extends('layouts.admin')

@section('admin_content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/categories-index.css') }}?v=1">
@endpush

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