<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Xác nhận đặt bàn chính thức</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f8f6; color: #1a2228; padding: 20px; }
        .email-container { background: #fff; max-width: 600px; margin: 0 auto; border-radius: 8px; border-top: 5px solid #D4AF37; padding: 30px; }
        .info-box { background: #fcfbf8; padding: 15px; border-left: 4px solid #D4AF37; margin: 20px 0; }
        .info-box p { margin: 7px 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #1a2228; color: #fff; text-align: left; padding: 10px; font-size: 13px; }
        td { padding: 10px; border-bottom: 1px dashed #e2e8f0; font-size: 14px; }
        .total { font-weight: bold; background: #fcfbf8; }
    </style>
</head>
<body>
<?php
    $total = (int) ($booking->total_price ?? 0);
    $deposit = min((int) ($booking->deposit_amount ?? 0), $total);
    $remaining = max(0, $total - $deposit);
?>
<div class="email-container">
    <h2>NHÀ HÀNG AURORA GARDEN</h2>
    <p>Xin chào <?php echo e($booking->full_name); ?>,</p>
    <p>Nhân viên đã xác minh tiền cọc. <strong>Đặt bàn của quý khách đã được xác nhận chính thức.</strong></p>

    <div class="info-box">
        <p><strong>Bàn:</strong> <?php echo e($booking->table->name ?? ('Số ' . $booking->table_id)); ?></p>
        <p><strong>Ngày:</strong> <?php echo e($booking->reservation_date?->format('d/m/Y') ?? $booking->reservation_date); ?></p>
        <p><strong>Giờ:</strong> <?php echo e(\Illuminate\Support\Carbon::parse($booking->reservation_time)->format('H:i')); ?></p>
        <p><strong>Tổng tiền thực đơn:</strong> <?php echo e(number_format($total, 0, ',', '.')); ?>đ</p>
        <p><strong>Tiền cọc đã xác minh:</strong> <?php echo e(number_format($deposit, 0, ',', '.')); ?>đ</p>
        <p><strong>Số tiền còn lại:</strong> <?php echo e(number_format($remaining, 0, ',', '.')); ?>đ</p>
        <p><strong>Trạng thái tiền cọc:</strong> Đã được nhân viên xác nhận</p>
    </div>

    <h3>Thực đơn đã đặt</h3>
    <?php if(!empty($booking->cart_data)): ?>
        <table>
            <thead>
                <tr><th>Món</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th></tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $booking->cart_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($item['name'] ?? 'Món ăn'); ?></td>
                        <td><?php echo e($item['quantity'] ?? 0); ?></td>
                        <td><?php echo e(number_format((float) ($item['price'] ?? 0), 0, ',', '.')); ?>đ</td>
                        <td><?php echo e(number_format((float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0), 0, ',', '.')); ?>đ</td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr class="total">
                    <td colspan="3" style="text-align: right;">TỔNG TIỀN</td>
                    <td><?php echo e(number_format($total, 0, ',', '.')); ?>đ</td>
                </tr>
            </tbody>
        </table>
    <?php else: ?>
        <p>Quý khách chưa chọn món trước.</p>
    <?php endif; ?>
</div>
</body>
</html>
<?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/emails/booking_confirmation.blade.php ENDPATH**/ ?>