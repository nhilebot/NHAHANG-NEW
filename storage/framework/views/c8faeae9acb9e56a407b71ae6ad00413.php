<?php $__env->startSection('title', 'Lịch sử phục vụ & Đơn hàng'); ?>

<?php $__env->startSection('head'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    /* ===== CSS LUXURY THEME ĐỒNG BỘ ===== */
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

    body { 
        background-color: #F9F8F6; 
        font-family: 'Plus Jakarta Sans', sans-serif; 
    }
    
    .history-container { 
        max-width: 900px; 
        margin: 60px auto; 
        padding: 0 20px; 
    }
    
    .title-page { 
        font-family: 'Playfair Display', serif; 
        color: #1A2228; 
        font-size: 32px; 
        text-align: center; 
        margin-bottom: 40px; 
        font-weight: 700;
        letter-spacing: 1.5px;
        margin-top: 100px;
    }
    .title-page::after {
        content: '';
        display: block;
        width: 60px;
        height: 3px;
        background: #D4AF37;
        margin: 15px auto 0;
    }
    
    .order-tabs { 
        display: flex; 
        background: #fff; 
        border-radius: 12px; 
        padding: 8px; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
        margin-bottom: 40px; 
        border: 1px solid #EAEAEA;
    }
    .tab-item { 
        padding: 14px 5px; 
        cursor: pointer; 
        font-weight: 600; 
        color: #718096; 
        transition: all 0.3s ease; 
        flex: 1; 
        text-align: center; 
        border-radius: 8px; 
        font-size: 14px; 
        letter-spacing: 0.5px;
    }
    .tab-item:hover { color: #1A2228; background: #F7FAFC; }
    .tab-item.active { 
        background: #1A2228; 
        color: #D4AF37; 
        box-shadow: 0 4px 15px rgba(26, 34, 40, 0.2); 
    }

    .order-block { 
        background: #fff; 
        border-radius: 12px; 
        margin-bottom: 30px; 
        box-shadow: 0 5px 25px rgba(0,0,0,0.04); 
        border: 1px solid #EAEAEA; 
        overflow: hidden; 
        transition: all 0.3s; 
    }
    .order-block:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 15px 35px rgba(0,0,0,0.08); 
        border-color: #D4AF37;
    }
    
    .order-header { 
        padding: 18px 25px; 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        background: #FCFBF8; 
        border-bottom: 1px solid #EAEAEA; 
    }
    .order-id { 
        font-weight: 700; 
        color: #1A2228; 
        font-size: 16px; 
        letter-spacing: 1px;
    }
    .order-id i { color: #D4AF37; margin-right: 5px; }
    
    .status-badge { 
        padding: 6px 16px; 
        border-radius: 30px; 
        font-size: 11px; 
        font-weight: 700; 
        text-transform: uppercase; 
        letter-spacing: 1px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .product-list { padding: 10px 0; }
    .product-row { 
        display: flex; 
        padding: 15px 25px; 
        align-items: center; 
        border-bottom: 1px dashed #F0F0F0;
    }
    .product-row:last-child { border-bottom: none; }
    .product-img { 
        width: 70px; height: 70px; object-fit: cover; border-radius: 8px; 
        box-shadow: 0 4px 10px rgba(0,0,0,0.06); 
    }
    .product-detail { flex: 1; padding-left: 20px; }
    .product-name { font-weight: 700; font-size: 15px; color: #1A2228; margin-bottom: 6px; }
    .product-qty { 
        color: #718096; font-size: 13px; font-weight: 500; background: #F7FAFC;
        padding: 4px 10px; border-radius: 20px; display: inline-block;
    }

    .order-footer { 
        padding: 20px 25px; border-top: 1px solid #EAEAEA; 
        display: flex; justify-content: space-between; align-items: center; 
        background: #FFF; 
    }
    .total-label { color: #718096; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-right: 10px;}
    .total-amount { font-size: 20px; font-weight: 700; color: #D4AF37; font-family: 'Playfair Display', serif;}
    
    .btn-detail { 
        background: transparent; color: #1A2228; border: 2px solid #EAEAEA; 
        padding: 8px 20px; border-radius: 8px; font-weight: 600; font-size: 13px; transition: all 0.3s; 
    }
    .btn-detail:hover { background: #1A2228; color: #D4AF37; border-color: #1A2228;}
    
    .btn-cancel { 
        background: #dc3545; color: #fff; border: 2px solid #dc3545; 
        padding: 8px 20px; border-radius: 8px; font-weight: 600; font-size: 13px; 
        cursor: pointer; transition: all 0.3s; 
    }
    .btn-cancel:hover { background: #c82333; border-color: #c82333; }

    .detail-box {
        padding: 25px; background: #FCFBF8; border-top: 1px solid #EAEAEA;
        border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;
    }
    .detail-box p { margin-bottom: 12px; font-size: 14px; color: #4A5568; }
    .detail-box strong { color: #1A2228;  display: inline-block; font-weight: 600; }
    .detail-box i { color: #D4AF37; width: 20px; text-align: center; margin-right: 5px; }

    .empty-state { text-align: center; padding: 100px 20px; }
    .empty-state i { font-size: 60px; color: #D4AF37; margin-bottom: 25px; opacity: 0.5; }
    .empty-state p { color: #718096; font-weight: 500; font-size: 16px; }

    /* Trạng thái */
    .status-pending { background: #fff4e5; color: #664d03; border: 1px solid #ffecb5; }
    .status-confirmed { background: #e7f6f8; color: #087990; border: 1px solid #cff4fc; }
    .status-arrived, .status-serving, .status-served { background: #fff9db; color: #856404; border: 1px solid #ffeeba; }
    .status-paid_cash, .status-paid_transfer { background: #e9f7ef; color: #198754; border: 1px solid #badbcc; }
    .status-completed { background: #1A2228; color: #D4AF37; }
    .status-cancelled { background: #fdf2f2; color: #dc3545; border: 1px solid #f8d7da; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="history-container">
    <h2 class="title-page">Lịch sử giao dịch</h2>

    <div class="order-tabs">
        <div class="tab-item active" onclick="filterByStatus('all', this)">Tất Cả</div>
        <div class="tab-item" onclick="filterByStatus('pending', this)">Chờ Duyệt</div>
        <div class="tab-item" onclick="filterByStatus('confirmed', this)">Đã Xác Nhận</div>
        <div class="tab-item" onclick="filterByStatus('serving_group', this)">Đang Phục Vụ</div>
        <div class="tab-item" onclick="filterByStatus('completed', this)">Hoàn Tất</div>
    </div>

    <div id="order-wrapper">
        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="order-block" data-status="<?php echo e($order->status); ?>">
           <div class="order-header">
                <span class="order-id"><i class="fa-solid fa-gem"></i> ĐƠN HÀNG #<?php echo e($order->id); ?></span>
                
                <?php 
                    $bgClass = \App\Helpers\StatusHelper::getBgClass($order->status);
                ?>

                <div class="status-badge <?php echo e($bgClass); ?>">
                    <?php echo e(\App\Helpers\StatusHelper::getTextWithEmoji($order->status)); ?>

                </div>
            </div>

            <div class="product-list">
                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    
                    <?php if($item->quantity > 0): ?>
                    <div class="product-row">
                       <?php
    $imageName = ($item->menu && $item->menu->image)
        ? $item->menu->image
        : 'default.jpg';

    $imageUrl = str_contains($imageName, 'images/')
        ? asset($imageName)
        : asset('images/' . $imageName);
?>
                        <img src="<?php echo e($imageUrl); ?>" class="product-img" onerror="this.src='<?php echo e(asset('images/default.jpg')); ?>'">
                        <div class="product-detail">
                            <div class="product-name"><?php echo e($item->product_name ?? 'Món ăn'); ?></div>
                            <div class="product-qty">Số lượng: <?php echo e($item->quantity); ?></div>
                            <?php if($item->is_extra): ?>
                                <span style="display:inline-block;margin-left:6px;padding:2px 8px;border-radius:10px;background:#FFF4D6;color:#B7791F;font-size:11px;font-weight:600;">
                                    Gọi thêm tại bàn · <?php echo e($item->created_at?->format('H:i')); ?>

                                </span>
                            <?php endif; ?>

                        </div>
                        <div style="font-weight: 700; color: #1A2228; font-size: 15px;">
                            <?php echo e(number_format($item->price * $item->quantity, 0, ',', '.')); ?>đ
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="order-footer">
                <div class="d-flex align-items-center">
                    <span class="total-label">Tổng hóa đơn:</span>
                    <span class="total-amount"><?php echo e(number_format($order->total_price, 0, ',', '.')); ?>đ</span>
                </div>
                <div style="display: flex; gap: 10px;">
                    <?php if($order->status === 'pending'): ?>
                    <button class="btn-cancel" onclick="cancelOrder(<?php echo e($order->id); ?>, this)">
                        <i class="fa-solid fa-ban" style="margin-right: 5px;"></i>Hủy đơn
                    </button>
                    <?php endif; ?>
                    <button class="btn-detail" onclick="toggleDetail('detail-<?php echo e($order->id); ?>')">Xem chi tiết</button>
                </div>
            </div>

            <div id="detail-<?php echo e($order->id); ?>" class="detail-box" style="display: none;">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong><i class="fa-solid fa-user-tie"></i> Khách hàng:</strong> <?php echo e($order->name); ?></p>
                        <p>
    <strong>
        <i class="fa-solid fa-chair"></i> Vị trí:
    </strong>

    <span style="
        background: #1A2228;
        color: #D4AF37;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 12px;
    ">
        <?php echo e($order->table->name ?? 'Chưa có bàn'); ?>

        -
        <?php echo e($order->table->capacity ?? 0); ?> Ghế
    </span>
</p>
                   <p class="mb-0">
                <strong><i class="fa-solid fa-note-sticky"></i> Ghi chú:</strong> 
                <span style="color: #D4AF37; font-style: italic;"><?php echo e($order->notes); ?></span>
            </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong><i class="fa-solid fa-wallet"></i> Thanh toán:</strong> 
                            <?php if(strtolower($order->payment_method) == 'cash'): ?> Tiền mặt 
                            <?php elseif(strtolower($order->payment_method) == 'transfer'): ?> Chuyển khoản 
                            <?php else: ?> <?php echo e($order->payment_method); ?> <?php endif; ?>
                        </p>
                        <p><strong><i class="fa-solid fa-clock"></i> Giờ đặt:</strong> <?php echo e($order->created_at?->format('H:i d/m/Y') ?? 'N/A'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="empty-state">
            <i class="fa-solid fa-concierge-bell"></i>
            <p>Quý khách chưa có giao dịch nào.</p>
        </div>
        <?php endif; ?>
    </div>

    <div id="no-orders" class="empty-state" style="display: none;">
        <i class="fa-solid fa-wine-glass-empty"></i>
        <p>Không tìm thấy đơn hàng nào ở trạng thái này.</p>
    </div>
</div>

<script>
    function filterByStatus(status, element) {
        document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
        element.classList.add('active');
        let orders = document.querySelectorAll('.order-block');
        orders.forEach(order => {
            let orderStatus = order.getAttribute('data-status').toLowerCase();
            let show = (status === 'all') || 
                       (status === 'serving_group' && ['arrived', 'serving', 'served'].includes(orderStatus)) ||
                       (status === 'completed' && ['paid_cash', 'paid_transfer', 'completed'].includes(orderStatus)) ||
                       (orderStatus === status);
            order.style.display = show ? 'block' : 'none';
        });
    }

    function toggleDetail(id) {
        const box = document.getElementById(id);
        box.style.display = (box.style.display === "none") ? "block" : "none";
    }

    function cancelOrder(orderId, btnElement) {
        if (!confirm('Bạn có chắc muốn hủy đơn hàng này? Hành động này không thể hoàn tác.')) {
            return;
        }

        btnElement.disabled = true;
        btnElement.innerHTML = '<i class="fa-solid fa-hourglass-spinner"></i> Đang xử lý...';

        fetch(`/order/${orderId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Đơn hàng đã bị hủy thành công!');
                window.location.reload();
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể hủy đơn hàng'));
                btnElement.disabled = false;
                btnElement.innerHTML = '<i class="fa-solid fa-ban" style="margin-right: 5px;"></i>Hủy đơn';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi kết nối: ' + error.message);
            btnElement.disabled = false;
            btnElement.innerHTML = '<i class="fa-solid fa-ban" style="margin-right: 5px;"></i>Hủy đơn';
        });
    }

    // ECHO REALTIME
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Echo !== 'undefined') {
            <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                Echo.channel('orders.<?php echo e($order->id); ?>')
                    .listen('.status.updated', (data) => {
                        window.location.reload();
                    });
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('shared', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/orders/history.blade.php ENDPATH**/ ?>