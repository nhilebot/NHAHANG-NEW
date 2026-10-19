<?php $__env->startSection('admin_content'); ?>
<style>
    .reservation-page {
        font-family: 'Be Vietnam Pro', sans-serif;
        color: #1a2228;
    }

    .reservation-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 22px;
    }

    .reservation-header h4 {
        font-weight: 700;
        margin: 0;
        color: #1a2228;
    }

    .reservation-subtitle {
        color: #718096;
        font-size: 13px;
        margin-top: 5px;
    }

    .reservation-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 4px 18px rgba(0,0,0,.04);
        overflow: hidden;
    }

    .reservation-card-header {
        padding: 18px 22px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .reservation-card-header h5 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
    }

    .reservation-table {
        width: 100%;
        min-width: 950px;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
    }

    .reservation-table thead th {
        background: #f8fafc;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        padding: 15px 16px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    .reservation-table tbody td {
        padding: 18px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #edf2f7;
        font-size: 13px;
    }

    .reservation-table tbody tr:last-child td {
        border-bottom: none;
    }

    .reservation-table tbody tr:hover {
        background: #fafcff;
    }

    .customer-name {
        font-weight: 700;
        font-size: 14px;
        color: #1a2228;
        margin-bottom: 5px;
    }

    .reservation-muted {
        color: #718096;
        font-size: 12px;
        margin-top: 4px;
    }

    .table-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eff6ff;
        color: #1d4ed8;
        padding: 7px 10px;
        border-radius: 8px;
        font-weight: 700;
        white-space: nowrap;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 7px 11px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .st-pending { background: #fff4e5; color: #92400e; }
    .st-confirmed { background: #e7f6f8; color: #087990; }
    .st-arrived { background: #e7f0ff; color: #1d4ed8; }
    .st-serving { background: #fff9db; color: #856404; }
    .st-served { background: #e0f7fa; color: #006064; }
    .st-success { background: #e9f7ef; color: #198754; }
    .st-danger { background: #fdf2f2; color: #dc3545; }
    .st-neutral { background: #edf2f7; color: #475569; }

    .reservation-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 6px;
        min-width: 180px;
    }

    .reservation-actions .btn {
        font-size: 11px;
        padding: 7px 10px;
        border-radius: 7px;
        font-weight: 600;
    }

    .btn-reservation-edit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        margin-top: 9px;
        padding: 6px 9px;
        font-size: 11px;
        border: 1px solid #cbd5e1;
        color: #2563eb;
        border-radius: 7px;
        text-decoration: none;
        background: white;
    }

    .btn-reservation-edit:hover {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .empty-reservations {
        text-align: center;
        padding: 45px 20px !important;
        color: #718096;
    }

    .empty-reservations .empty-icon {
        display: block;
        font-size: 34px;
        margin-bottom: 10px;
    }

    @media (max-width: 768px) {
        .reservation-header {
            align-items: flex-start;
        }

        .reservation-header .btn {
            width: 100%;
        }

        .reservation-card-header {
            padding: 15px;
        }
    }
</style>

<?php
    $role = auth()->user()->isAdmin() ? 'admin' : 'staff';

    $statusClasses = [
        'pending_deposit' => 'st-pending',
        'pending'         => 'st-pending',
        'confirmed'       => 'st-confirmed',
        'arrived'         => 'st-arrived',
        'preparing'       => 'st-serving',
        'serving'         => 'st-serving',
        'ready'           => 'st-served',
        'served'          => 'st-served',
        'completed'       => 'st-success',
        'cancelled'       => 'st-danger',
    ];
?>

<div class="reservation-page">

    <div class="reservation-header">
        <div>
            <h4>🗓️ Quản lý Đặt bàn & Phục vụ</h4>
            <div class="reservation-subtitle">
                Theo dõi đặt bàn, gọi món, thanh toán và tình trạng phục vụ khách hàng.
            </div>
        </div>

        <a href="<?php echo e(route('admin.reservations.create')); ?>"
           class="btn btn-primary">
            <i class="fas fa-plus"></i> Đặt bàn tại chỗ
        </a>
    </div>

    <div class="reservation-card">
        <div class="reservation-card-header">
            <h5><i class="fas fa-clipboard-list text-primary"></i> Danh sách đặt bàn & phục vụ</h5>
            <span class="reservation-muted">
    Tổng số: <?php echo e($reservations->count()); ?> đơn
</span>
        </div>

        <div class="table-responsive">
            <table class="reservation-table">
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th>Thông tin bàn</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Thao tác nghiệp vụ</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $reservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $res): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $status = $res->status;
                            $isPaid = in_array(
                                $status,
                                \App\Models\Reservation::PAID_STATUSES,
                                true
                            );

                            $statusClass = $statusClasses[$status] ?? 'st-neutral';
                        ?>

                        <tr>
                            
                            <td>
                                <div class="customer-name">
                                    <?php echo e($res->full_name); ?>

                                </div>

                                <div>
                                    <i class="fas fa-phone text-muted"></i>
                                    <?php echo e($res->phone); ?>

                                </div>

                                <?php if($res->email): ?>
                                    <div class="reservation-muted">
                                        <i class="fas fa-envelope"></i>
                                        <?php echo e($res->email); ?>

                                    </div>
                                <?php endif; ?>

                                <div class="reservation-muted">
                                    Mã đặt bàn: #<?php echo e($res->id); ?>

                                </div>
                            </td>

                            
                            <td>
                                <div class="table-label">
                                    <i class="fas fa-chair"></i>
                                    <?php echo e($res->table->name ?? 'Chưa xếp bàn'); ?>

                                </div>

                                <?php if($res->table): ?>
                                    <div class="reservation-muted">
                                        <?php echo e($res->table->capacity); ?> ghế
                                    </div>
                                <?php endif; ?>

                                <?php if(!in_array($status, ['completed', 'cancelled'], true)): ?>
                                    <?php if(!$isPaid): ?>
                                        <a href="<?php echo e(route('staff.reservations.edit_items', $res->id)); ?>"
                                           class="btn-reservation-edit">
                                            <i class="fas fa-pen-to-square"></i>
                                            Chỉnh sửa đơn
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>

                            
                            <td>
                                <div>
                                    <i class="far fa-calendar-alt text-primary"></i>
                                    <?php echo e($res->reservation_date ?? '—'); ?>

                                </div>
                                <div class="reservation-muted">
                                    <i class="far fa-clock"></i>
                                    <?php echo e($res->reservation_time ?? '—'); ?>

                                </div>
                            </td>

                            
                            <td>
                                <span class="badge-status <?php echo e($statusClass); ?>">
                                    <?php echo e(\App\Helpers\StatusHelper::getTextWithEmoji($status)); ?>

                                </span>

                                <?php if($isPaid): ?>
                                    <div class="reservation-muted">
                                        <i class="fas fa-check-circle text-success"></i>
                                        Đã thanh toán
                                    </div>
                                <?php endif; ?>
                            </td>

                            
                            <td>
                                <form action="<?php echo e(route($role . '.reservations.updateStatus', $res->id)); ?>"
                                      method="POST">
                                    <?php echo csrf_field(); ?>

                                    <div class="reservation-actions">
                                        <?php if($status === 'pending_deposit'): ?>
                                            <?php if($res->payment_status === 'deposit_submitted'): ?>
                                                <button type="submit"
                                                        name="status"
                                                        value="pending"
                                                        class="btn btn-warning"
                                                        onclick="return confirm('Xác nhận đã nhận tiền cọc <?php echo e(number_format($res->deposit_amount ?? 0, 0, ',', '.')); ?>đ?')">
                                                    <i class="fas fa-check"></i>
                                                    Xác nhận cọc
                                                </button>
                                            <?php else: ?>
                                                <span class="reservation-muted">
                                                    Chờ xác nhận chuyển khoản
                                                </span>
                                            <?php endif; ?>

                                        <?php elseif($status === 'pending'): ?>
                                            <button type="submit" name="status" value="confirmed"
                                                    class="btn btn-info text-white">
                                                <i class="fas fa-check"></i> Duyệt đơn
                                            </button>

                                        <?php elseif($status === 'confirmed'): ?>
                                            <button type="submit" name="status" value="arrived"
                                                    class="btn btn-primary">
                                                <i class="fas fa-chair"></i> Khách nhận bàn
                                            </button>

                                        <?php elseif($status === 'arrived'): ?>
                                            <button type="submit" name="status" value="serving"
                                                    class="btn btn-warning">
                                                <i class="fas fa-fire"></i> Báo bếp nấu
                                            </button>

                                        <?php elseif(in_array($status, ['serving', 'ready'], true)): ?>
                                            <button type="submit" name="status" value="served"
                                                    class="btn btn-info text-white">
                                                <i class="fas fa-concierge-bell"></i> Đã lên món
                                            </button>

                                        <?php elseif($status === 'served'): ?>
                                            <?php
                                                $payUrl = route($role . '.reservations.pay', $res->id);
                                            ?>

                                            <button type="submit" name="payment_method" value="cash"
                                                    formaction="<?php echo e($payUrl); ?>"
                                                    class="btn btn-success"
                                                    onclick="return confirm('Xác nhận thanh toán bằng tiền mặt?')">
                                                <i class="fas fa-money-bill-wave"></i> Tiền mặt
                                            </button>

                                            <button type="submit" name="payment_method" value="transfer"
                                                    formaction="<?php echo e($payUrl); ?>"
                                                    class="btn btn-dark"
                                                    onclick="return confirm('Xác nhận thanh toán chuyển khoản?')">
                                                <i class="fas fa-credit-card"></i> Chuyển khoản
                                            </button>
                                        <?php endif; ?>

                                        <?php if($isPaid && $status !== 'completed'): ?>
                                            <button type="submit" name="status" value="completed"
                                                    class="btn btn-secondary"
                                                    onclick="return confirm('Xác nhận hoàn tất và chuyển bàn sang trạng thái dọn dẹp?')">
                                                <i class="fas fa-broom"></i> Dọn bàn & Xong
                                            </button>
                                        <?php endif; ?>
                                    </div>

                                    <?php if(in_array($status, [
                                        'pending_deposit',
                                        'pending',
                                        'confirmed',
                                        'arrived'
                                    ], true)): ?>
                                        <div class="text-center mt-2">
                                            <button type="submit"
                                                    name="status"
                                                    value="cancelled"
                                                    class="btn btn-sm btn-link text-danger"
                                                    onclick="return confirm('Bạn có chắc muốn hủy đơn đặt bàn này?')">
                                                <i class="fas fa-times"></i> Hủy đơn
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="empty-reservations">
                                <span class="empty-icon">📅</span>
                                <strong>Chưa có đơn đặt bàn nào</strong>
                                <div class="reservation-muted">
                                    Các yêu cầu đặt bàn sẽ hiển thị tại đây.
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(method_exists($reservations, 'links')): ?>
            <div class="p-3">
                <?php echo e($reservations->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/admin/reservations/index.blade.php ENDPATH**/ ?>