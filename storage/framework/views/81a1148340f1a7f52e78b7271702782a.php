
<?php $__env->startSection('topbar_title', 'Quản lý nhân viên'); ?>

<?php $__env->startSection('admin_content'); ?>
<style>
    /* ─── TABS ─── */
    .emp-tabs { display:flex; gap:8px; margin-bottom:24px; }
    .emp-tab  { padding:8px 22px; border-radius:9px; font-size:.875rem; font-weight:600;
                cursor:pointer; border:1.5px solid #e2e8f0; text-decoration:none;
                color:#64748b; background:#fff; transition:all .18s; }
    .emp-tab.active  { background:#5b3cff; color:#fff; border-color:#5b3cff; }
    .emp-tab:hover:not(.active) { background:#f1f5f9; }

    /* ─── LAYOUT ─── */
    .emp-layout { display:grid; grid-template-columns:1fr 340px; gap:20px; align-items:start; }

    /* ─── BẢNG NHÂN VIÊN ─── */
    .emp-card  { background:#fff; border-radius:16px; box-shadow:0 4px 20px rgba(15,23,42,.07); overflow:hidden; }
    .emp-card-header { padding:18px 22px; border-bottom:1px solid #f1f5f9;
                       display:flex; justify-content:space-between; align-items:center; }
    .emp-card-title  { font-size:1rem; font-weight:700; color:#0f172a; }

    .emp-table { width:100%; border-collapse:collapse; }
    .emp-table th { padding:11px 16px; font-size:.74rem; font-weight:700; text-transform:uppercase;
                    color:#64748b; background:#f8fafc; border-bottom:2px solid #f1f5f9; text-align:left; }
    .emp-table td { padding:13px 16px; border-bottom:1px solid #f8fafc;
                    font-size:.88rem; vertical-align:middle; }
    .emp-table tr:last-child td { border-bottom:none; }
    .emp-table tr:hover td { background:#fafbff; }

    .avatar-circle { width:36px; height:36px; border-radius:50%;
                     background:linear-gradient(135deg,#5b3cff,#a78bff);
                     display:grid; place-items:center; font-weight:700;
                     font-size:.82rem; color:#fff; flex-shrink:0; }

    /* ─── LỊCH CA ─── */
    .shift-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; margin-top:6px; }
    .shift-cell { text-align:center; }
    .shift-day  { font-size:.68rem; color:#94a3b8; margin-bottom:3px; font-weight:600; }
    .shift-pill { font-size:.65rem; font-weight:700; padding:4px 0; border-radius:6px;
                  cursor:pointer; border:none; width:100%; transition:all .15s; }
    .shift-morning   { background:#eff6ff; color:#1d4ed8; }
    .shift-afternoon { background:#fefce8; color:#854d0e; }
    .shift-full      { background:#fdf4ff; color:#7e22ce; }
    .shift-off       { background:#f1f5f9; color:#94a3b8; }
    .shift-pill:hover { opacity:.75; }

    /* ─── FORM THÊM ─── */
    .add-card { background:#fff; border-radius:16px; box-shadow:0 4px 20px rgba(15,23,42,.07);
                padding:22px; position:sticky; top:20px; }
    .add-title { font-size:.95rem; font-weight:700; color:#0f172a; margin-bottom:18px;
                 display:flex; align-items:center; gap:8px; }
    .form-group { margin-bottom:14px; }
    .form-label { font-size:.8rem; font-weight:600; color:#374151; display:block; margin-bottom:5px; }
    .form-control { width:100%; padding:9px 13px; border:1.5px solid #e2e8f0; border-radius:9px;
                    font-size:.875rem; outline:none; transition:border .2s; font-family:inherit; box-sizing:border-box; }
    .form-control:focus { border-color:#5b3cff; }
    .form-select { appearance:none;
                   background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
                   background-repeat:no-repeat; background-position:right 12px center; padding-right:36px; }
    .btn-add { width:100%; padding:10px; background:#5b3cff; color:#fff; border:none;
               border-radius:9px; font-size:.875rem; font-weight:700; cursor:pointer; transition:background .18s; }
    .btn-add:hover { background:#4a30e0; }

    /* ─── DELETE ─── */
    .btn-del { background:none; border:none; cursor:pointer; color:#ef4444; font-size:1.1rem;
               padding:4px 8px; border-radius:7px; transition:background .15s; }
    .btn-del:hover { background:#fef2f2; }

    /* ─── BADGE ROLE ─── */
    .rbadge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:.72rem; }
    .rbadge-staff { background:#eff6ff; color:#1d4ed8; }
    .rbadge-chef  { background:#fff7ed; color:#c2410c; }
</style>

<div class="container-fluid" style="max-width:1200px;margin:0 auto;">

    
    <div class="emp-tabs">
        <a href="<?php echo e(route('admin.employees.index', ['role'=>'staff'])); ?>"
           class="emp-tab <?php echo e($role === 'staff' ? 'active' : ''); ?>">🧑‍💼 Nhân viên phục vụ</a>
        <a href="<?php echo e(route('admin.employees.index', ['role'=>'chef'])); ?>"
           class="emp-tab <?php echo e($role === 'chef' ? 'active' : ''); ?>">👨‍🍳 Đầu bếp</a>
    </div>

    <div class="emp-layout">

        
        <div>
            <div class="emp-card">
                <div class="emp-card-header">
                    <span class="emp-card-title">
                        <?php echo e($role === 'staff' ? '🧑‍💼 Nhân viên phục vụ' : '👨‍🍳 Đầu bếp'); ?>

                        <span style="font-size:.8rem;color:#94a3b8;font-weight:500;margin-left:6px;">
                            (<?php echo e($employees->count()); ?> người)
                        </span>
                    </span>
                </div>

                <?php if($employees->isEmpty()): ?>
                <div style="text-align:center;padding:48px 20px;color:#94a3b8;">
                    <div style="font-size:2.5rem;margin-bottom:10px;">👤</div>
                    Chưa có nhân viên nào. Thêm mới ở bên phải.
                </div>
                <?php else: ?>
                <div style="overflow-x:auto;">
                    <table class="emp-table">
                        <thead>
                            <tr>
                                <th>Nhân viên</th>
                                <th>Liên hệ</th>
                                <th>Lịch ca làm việc</th>
                                <th class="text-center">Xóa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $days = ['T2','T3','T4','T5','T6','T7','CN'];
                                $userShifts = $emp->shifts ?? []; 
                            ?>
                            <tr>
                                
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div class="avatar-circle">
                                            <?php echo e(mb_strtoupper(mb_substr($emp->name, 0, 1))); ?>

                                        </div>
                                        <div>
                                            <div style="font-weight:600;color:#0f172a;font-size:.88rem;"><?php echo e($emp->name); ?></div>


<?php
    $roleLabel = ($role === 'staff') ? 'Nhân viên phục vụ' : 'Đầu bếp';
?>

<span class="rbadge rbadge-<?php echo e($role); ?>">
    <?php echo e($roleLabel); ?>

</span>
                                    </div>
                                </td>

                                
                                <td>
                                    <div style="font-size:.82rem;color:#374151;"><?php echo e($emp->email); ?></div>
                                    <div style="font-size:.78rem;color:#94a3b8;"><?php echo e($emp->phone ?? '—'); ?></div>
                                </td>

                                
                                <td style="min-width:260px;">
                                    <div class="shift-grid">
                                        <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $current = $userShifts[$day] ?? 'off';
                                            $labels = ['morning'=>'Sáng','afternoon'=>'Chiều','full'=>'Full','off'=>'Nghỉ'];
                                        ?>
                                        <div class="shift-cell">
                                            <div class="shift-day"><?php echo e($day); ?></div>
                                            <button class="shift-pill shift-<?php echo e($current); ?>"
                                                    data-uid="<?php echo e($emp->id); ?>"
                                                    data-day="<?php echo e($day); ?>"
                                                    data-current="<?php echo e($current); ?>"
                                                    onclick="cycleShift(this)">
                                                <?php echo e($labels[$current]); ?>

                                            </button>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </td>

                                
                                <td class="text-center">
                                    <form action="<?php echo e(route('admin.employees.destroy', $emp->id)); ?>"
                                          method="POST"
                                          onsubmit="return confirm('Xóa nhân viên <?php echo e($emp->name); ?>?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn-del" title="Xóa">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="add-card">
            <div class="add-title">➕ Thêm nhân viên mới</div>

            <form action="<?php echo e(route('admin.employees.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <label class="form-label">Họ và tên *</label>
                    <input type="text" name="name" class="form-control"
                           placeholder="Nguyễn Văn A" value="<?php echo e(old('name')); ?>" required>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div style="color:#ef4444;font-size:.78rem;margin-top:4px;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control"
                           placeholder="nhanvien@nhahang.vn" value="<?php echo e(old('email')); ?>" required>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div style="color:#ef4444;font-size:.78rem;margin-top:4px;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control"
                           placeholder="09xx xxx xxx" value="<?php echo e(old('phone')); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Mật khẩu *</label>
                    <input type="password" name="password" class="form-control"
                           placeholder="Tối thiểu 6 ký tự" required>
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div style="color:#ef4444;font-size:.78rem;margin-top:4px;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label class="form-label">Vai trò *</label>
                    <select name="role_id" class="form-control form-select" required>
                        <option value="">-- Chọn vai trò --</option>
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($r->id); ?>"
                            <?php echo e(old('role_id') == $r->id || $role === $r->name ? 'selected' : ''); ?>>
                            <?php echo e($r->name === 'staff' ? '🧑‍💼 Nhân viên phục vụ' : '👨‍🍳 Đầu bếp'); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['role_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div style="color:#ef4444;font-size:.78rem;margin-top:4px;"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <button type="submit" class="btn-add">✅ Thêm nhân viên</button>
            </form>

            
            <div style="margin-top:20px;padding-top:16px;border-top:1px solid #f1f5f9;">
                <div style="font-size:.78rem;font-weight:700;color:#64748b;margin-bottom:8px;">
                    Ghi chú lịch ca:
                </div>
                <div style="display:flex;flex-direction:column;gap:5px;">
                    <span style="font-size:.78rem;"><span style="display:inline-block;width:40px;text-align:center;background:#eff6ff;color:#1d4ed8;border-radius:4px;padding:1px 4px;font-weight:600;">Sáng</span> &nbsp;6:00 – 13:00</span>
                    <span style="font-size:.78rem;"><span style="display:inline-block;width:40px;text-align:center;background:#fefce8;color:#854d0e;border-radius:4px;padding:1px 4px;font-weight:600;">Chiều</span> 13:00 – 20:00</span>
                    <span style="font-size:.78rem;"><span style="display:inline-block;width:40px;text-align:center;background:#fdf4ff;color:#7e22ce;border-radius:4px;padding:1px 4px;font-weight:600;">Full</span> &nbsp;&nbsp;6:00 – 20:00</span>
                    <span style="font-size:.78rem;"><span style="display:inline-block;width:40px;text-align:center;background:#f1f5f9;color:#94a3b8;border-radius:4px;padding:1px 4px;font-weight:600;">Nghỉ</span> Không làm</span>
                </div>
                <div style="margin-top:10px;font-size:.75rem;color:#94a3b8;">
                    💡 Nhấn vào ô ca để chuyển: Sáng → Chiều → Full → Nghỉ
                </div>
            </div>
        </div>

    </div>
</div>

<script>
const shiftCycle = ['morning', 'afternoon', 'full', 'off'];
const shiftLabel = { morning:'Sáng', afternoon:'Chiều', full:'Full', off:'Nghỉ' };

function cycleShift(btn) {
    const cur   = btn.dataset.current;
    const next  = shiftCycle[(shiftCycle.indexOf(cur) + 1) % shiftCycle.length];
    const uid   = btn.dataset.uid;
    const day   = btn.dataset.day; // Bây giờ day là T2, T3...

    btn.textContent       = '...';
    btn.className         = 'shift-pill shift-off';
    btn.dataset.current   = next;

    fetch('<?php echo e(route("admin.employees.shift")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        // Gửi day (ví dụ: "T2") thay vì index 0-6
        body: JSON.stringify({ user_id: uid, day_of_week: day, shift: next })
    })
    .then(r => r.json())
    .then(() => {
        btn.textContent = shiftLabel[next];
        btn.className   = 'shift-pill shift-' + next;
    })
    .catch(() => {
        btn.textContent = shiftLabel[cur];
        btn.className   = 'shift-pill shift-' + cur;
        btn.dataset.current = cur;
    });
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\restaurant-fixed\resources\views/admin/employees/index.blade.php ENDPATH**/ ?>