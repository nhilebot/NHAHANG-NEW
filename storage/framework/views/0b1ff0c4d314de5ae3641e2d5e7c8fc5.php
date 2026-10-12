<?php $__env->startSection('topbar_title', 'Hệ thống bếp – Quản lý đơn hàng'); ?>

<?php $__env->startSection('admin_content'); ?>
<style>
    .kds-wrapper {
        background-color: #f1f5f9;
        min-height: calc(100vh - 100px);
        padding: 20px;
        overflow-x: auto;
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }
    .kds-card {
        min-width: 300px;
        max-width: 320px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .kds-header { padding: 15px; color: #fff; position: relative; }
    .bg-new-order     { background-color: #f87171; }
    .bg-cooking-order { background-color: #fbbf24; }
    .kds-header .table-name { font-size: 1.4rem; font-weight: 800; margin: 0; display: block; }
    .kds-header .order-time { font-size: 0.9rem; opacity: 0.9; }
    .kds-body { padding: 0; flex-grow: 1; }
    .kds-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        border-bottom: 1px solid #f1f5f9;
    }
    .kds-item:last-child { border-bottom: none; }
    .item-info { display: flex; align-items: center; gap: 10px; }
    .item-qty  { font-weight: 800; color: #ef4444; font-size: 1.1rem; }
    .item-name { font-weight: 600; color: #1e293b; }
    .item-done { text-decoration: line-through; opacity: 0.4; }
    .kds-footer { padding: 12px; background: #f8fafc; display: flex; gap: 8px; }
    .btn-kds {
        flex: 1; border: none; padding: 10px; border-radius: 8px;
        font-weight: 700; text-transform: uppercase; font-size: 0.75rem; cursor: pointer;
    }
    .status-bar {
        position: fixed; bottom: 0; left: 0; right: 0;
        background: #fff; padding: 10px 20px;
        border-top: 2px solid #e2e8f0;
        display: flex; gap: 15px; z-index: 999;
    }
    .status-mini {
        padding: 5px 15px; border-radius: 4px;
        font-size: 0.8rem; font-weight: 600;
    }
    .badge-item {
        font-size: .72rem; font-weight: 700; padding: 2px 8px;
        border-radius: 20px; white-space: nowrap;
    }
    .bi-pending  { background:#fff4e5; color:#92400e; }
    .bi-cooking  { background:#fef3c7; color:#b45309; }
    .bi-done     { background:#d1fae5; color:#065f46; }
</style>

<div class="kds-wrapper" style="padding-bottom: 60px;">

    <?php $__empty_1 = true; $__currentLoopData = $pendingOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php
        $statuses  = $reservation->chef_statuses ?? [];
        $cartItems = $reservation->cart_data ?? [];
        $isCooking = $reservation->status === 'serving';
        $tableName = $reservation->table->name ?? ('Bàn ' . $reservation->table_id);
    ?>

    <div class="kds-card">
        
        <div class="kds-header <?php echo e($isCooking ? 'bg-cooking-order' : 'bg-new-order'); ?>">
            <span class="table-name"><?php echo e($tableName); ?></span>
            <div class="d-flex justify-content-between align-items-center mt-1">
                <span class="order-time">
                    <?php echo e($reservation->updated_at->format('H:i')); ?> •
                    <?php echo e($reservation->full_name); ?>

                </span>
                <span style="font-size:.8rem; background:rgba(0,0,0,.2); padding:2px 8px; border-radius:10px;">
                    <?php echo e($isCooking ? '🔥 Đang nấu' : '🆕 Đơn mới'); ?>

                </span>
            </div>
             <?php if(!empty($reservation->notes)): ?>
        <div style="margin-top: 8px; background: rgba(0,0,0,0.2); border-radius: 6px; padding: 6px 10px; font-size: 0.82rem; line-height: 1.4;">
            <span style="opacity: 0.85;">📝 Ghi chú:</span>
            <span style="font-weight: 700;"><?php echo e($reservation->notes); ?></span>
        </div>
    <?php endif; ?>
        </div>

        
        <div class="kds-body">
            <?php $__empty_2 = true; $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
            <?php
                $menuId    = $item['id'];
                $itemSt    = $statuses[$menuId] ?? 'pending';
            ?>
            <div class="kds-item">
                <div class="item-info">
                    <span class="item-qty"><?php echo e($item['quantity']); ?></span>
                    <span class="item-name <?php echo e($itemSt === 'done' ? 'item-done' : ''); ?>">
                        <?php echo e($item['name']); ?>

                    </span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    
                    <span class="badge-item <?php echo e(\App\Helpers\StatusHelper::getBgClass($itemSt)); ?>">
    <?php echo e(\App\Helpers\StatusHelper::getTextWithEmoji($itemSt)); ?>

</span>

                    
                    <?php if($itemSt !== 'done'): ?>
                    <form action="<?php echo e(route('chef.item.status', [$reservation->id, $menuId])); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php if($itemSt === 'pending'): ?>
                            <button name="chef_status" value="cooking"
                                class="btn btn-sm btn-outline-warning p-1" title="Bắt đầu nấu món này">🍳</button>
                        <?php else: ?>
                            <button name="chef_status" value="done"
                                class="btn btn-sm btn-outline-success p-1" title="Món này đã xong">✅</button>
                        <?php endif; ?>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
            <div class="text-center text-muted py-3" style="font-size:.85rem;">Chưa có món trong đơn này</div>
            <?php endif; ?>
        </div>

        
        <div class="kds-footer">
            <?php if($reservation->status === 'arrived'): ?>
            <form action="<?php echo e(route('chef.order.start', $reservation->id)); ?>" method="POST" class="w-100">
                <?php echo csrf_field(); ?>
                <button class="btn-kds w-100" style="background:#fbbf24; color:#fff;">▶ Bắt đầu nấu tất cả</button>
            </form>
            <?php endif; ?>
            <form action="<?php echo e(route('chef.order.finish', $reservation->id)); ?>" method="POST" class="w-100">
                <?php echo csrf_field(); ?>
                <button class="btn-kds w-100" style="background:#10b981; color:#fff;">✅ Xong tất cả</button>
            </form>
        </div>
    </div>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="w-100 text-center py-5">
        <div style="font-size: 3rem;">🎉</div>
        <h4 class="text-muted mt-3">Đang đợi đơn hàng mới từ khách...</h4>
        <p class="text-muted" style="font-size:.85rem;">
            Đơn sẽ xuất hiện khi nhân viên xác nhận khách đã nhận bàn
        </p>
    </div>
    <?php endif; ?>

</div>


<div class="status-bar">
    <div class="status-mini bg-warning text-dark">⏳ Đang chờ: <?php echo e($pendingItems); ?> món</div>
    <div class="status-mini bg-danger text-white">🔥 Đang nấu: <?php echo e($cookingItems); ?> món</div>
    <div class="status-mini bg-success text-white">✅ Đã xong: <?php echo e($readyOrders->count()); ?> đơn</div>
    <div class="status-mini" style="background:#e2e8f0; color:#475569; margin-left:auto;">
        🕐 <?php echo e(now()->format('H:i:s')); ?>

    </div>
</div>


<script>setTimeout(() => location.reload(), 30000);</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/chef/dashboard.blade.php ENDPATH**/ ?>