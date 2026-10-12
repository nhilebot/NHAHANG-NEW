 

<style>
    /* Container bao quanh phân trang */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 30px;
        padding: 20px 0;
    }

    .pagination-wrapper nav {
        display: flex;
        gap: 5px;
    }

    .pagination-wrapper .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        gap: 8px;
    }

    /* Style cho các nút số */
    .pagination-wrapper .page-item .page-link {
        color: #1A2228;
        background-color: #fff;
        border: 1px solid #E5E7EB;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    /* Khi di chuột qua nút (Hover) */
    .pagination-wrapper .page-item .page-link:hover {
        background-color: #1A2228;
        color: #D4AF37;
        border-color: #1A2228;
        transform: translateY(-2px);
    }

    /* Nút đang được chọn (Active) */
    .pagination-wrapper .page-item.active .page-link {
        background-color: #D4AF37;
        color: #fff;
        border-color: #D4AF37;
        box-shadow: 0 4px 10px rgba(212, 175, 55, 0.3);
    }

    /* Nút bị vô hiệu hóa (Disabled) */
    .pagination-wrapper .page-item.disabled .page-link {
        color: #9CA3AF;
        background-color: #F3F4F6;
        border-color: #E5E7EB;
        cursor: not-allowed;
    }

    /* Ẩn bớt các text thừa của Laravel */
    .pagination-wrapper nav div:first-child {
        display: none !important;
    }
</style>

<?php $__env->startSection('admin_content'); ?>
<div class="panel">
    <?php
        $isAdmin = auth()->user()->isAdmin();
        $prefix = $isAdmin ? 'admin' : 'staff';
    ?>

    <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 class="title-category">Danh sách quản lý bàn</h3>
        
        <a href="<?php echo e(route($prefix . '.tables.create')); ?>" class="btn-add" style="background: #1A2228; color: #D4AF37; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">
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
                <?php $__currentLoopData = $tables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="padding: 12px; border-bottom: 1px solid #eee;">#<?php echo e($table->id); ?></td>
                    <td style="padding: 12px; border-bottom: 1px solid #eee;"><strong><?php echo e($table->name); ?></strong></td>
                    <td style="padding: 12px; border-bottom: 1px solid #eee;">
                        <?php if($table->status === 'empty'): ?>
                            <span class="badge" style="background: #C6F6D5; color: #22543D; padding: 5px 10px; border-radius: 15px; font-size: 12px;">Sẵn sàng</span>
                        <?php elseif($table->status === 'reserved'): ?>
                            <span class="badge" style="background: #DBEAFE; color: #1E40AF; padding: 5px 10px; border-radius: 15px; font-size: 12px;">Đã đặt</span>
                        <?php else: ?>
                            <span class="badge" style="background: #FEE2E2; color: #742A2A; padding: 5px 10px; border-radius: 15px; font-size: 12px;">Đang phục vụ</span>
                        <?php endif; ?>
                    </td>
                  <td>
    
    <a href="<?php echo e(route($prefix . '.tables.edit', $table->id)); ?>" class="btn btn-warning btn-sm" style="border-radius: 6px; padding: 5px 12px; text-decoration: none; color: #212529; font-weight: bold; background-color: #ffc107; border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        ✏️ Sửa
    </a>

    
    <?php if(auth()->user()->role_id == 2): ?>
        <form action="<?php echo e(route('admin.tables.destroy', $table->id)); ?>" method="POST" style="display:inline-block; margin-left: 5px;">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn btn-danger btn-sm" style="border-radius: 6px; padding: 5px 12px; font-weight: bold; background-color: #dc3545; border: none; color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" onclick="return confirm('Bạn có chắc chắn muốn xóa bàn này?')">
                🗑️ Xóa
            </button>
        </form>
    <?php endif; ?>
</td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper" style="margin-top: 20px;">
        <?php echo e($tables->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/admin/tables/index.blade.php ENDPATH**/ ?>