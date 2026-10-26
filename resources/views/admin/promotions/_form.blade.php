@php
    $dt = fn ($v) => $v ? $v->format('Y-m-d\TH:i') : '';
    $type = old('discount_type', $promotion->discount_type);
@endphp

@if ($errors->any())
    <div class="promo-errors">
        @foreach ($errors->all() as $err)<div>• {{ $err }}</div>@endforeach
    </div>
@endif

<div class="promo-grid">
    <div>
        <label class="promo-label">Mã khuyến mãi *</label>
        <input type="text" name="code" class="promo-input" style="text-transform:uppercase"
               value="{{ old('code', $promotion->code) }}" maxlength="30" placeholder="VD: AURORA10" required>
        <small class="promo-hint">Chữ không dấu, số, - hoặc _. Hệ thống tự viết HOA.</small>
    </div>
    <div>
        <label class="promo-label">Tên chương trình *</label>
        <input type="text" name="name" class="promo-input"
               value="{{ old('name', $promotion->name) }}" placeholder="VD: Giảm 10% mừng khai trương" required>
    </div>
</div>

<label class="promo-label">Mô tả</label>
<textarea name="description" rows="2" class="promo-input" placeholder="Ghi chú nội bộ / điều kiện áp dụng">{{ old('description', $promotion->description) }}</textarea>

<div class="promo-grid">
    <div>
        <label class="promo-label">Hình thức giảm *</label>
        <select name="discount_type" id="discount_type" class="promo-input">
            <option value="percent" @selected($type === 'percent')>Giảm theo phần trăm (%)</option>
            <option value="fixed"   @selected($type === 'fixed')>Giảm số tiền cố định (VNĐ)</option>
        </select>
    </div>
    <div>
        <label class="promo-label" id="value_label">Giá trị giảm *</label>
        <input type="number" name="discount_value" class="promo-input" min="1"
               value="{{ old('discount_value', $promotion->discount_value) }}" required>
    </div>
</div>

<div class="promo-grid">
    <div>
        <label class="promo-label">Tiền món tối thiểu (VNĐ)</label>
        <input type="number" name="min_order_amount" class="promo-input" min="0"
               value="{{ old('min_order_amount', $promotion->min_order_amount ?? 0) }}">
        <small class="promo-hint">Đơn đạt mức này mới dùng được mã. Nhập 0 = không yêu cầu.</small>
    </div>
    <div>
        <label class="promo-label">Giới hạn tổng lượt dùng</label>
        <input type="number" name="usage_limit" class="promo-input" min="1"
               value="{{ old('usage_limit', $promotion->usage_limit) }}" placeholder="Để trống = không giới hạn">
    </div>
</div>

<div class="promo-grid">
    <div>
        <label class="promo-label">Bắt đầu</label>
        <input type="datetime-local" name="starts_at" class="promo-input"
               value="{{ old('starts_at', $dt($promotion->starts_at)) }}">
    </div>
    <div>
        <label class="promo-label">Kết thúc</label>
        <input type="datetime-local" name="ends_at" class="promo-input"
               value="{{ old('ends_at', $dt($promotion->ends_at)) }}">
    </div>
</div>

<div style="margin:6px 0 4px;">
    <label style="display:flex;gap:8px;align-items:center;cursor:pointer;font-weight:600;">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $promotion->is_active))>
        Kích hoạt mã
    </label>
</div>

<script>
(function () {
    var type = document.getElementById('discount_type');
    var label = document.getElementById('value_label');
    function sync() {
        var pct = type.value === 'percent';
        label.textContent = pct ? 'Phần trăm giảm (1–100) *' : 'Số tiền giảm (VNĐ) *';
    }
    type.addEventListener('change', sync);
    sync();
})();
</script>

<style>
    .promo-card { max-width: 820px; margin: 30px auto; background: #fff; padding: 36px; border-radius: 12px;
                  box-shadow: 0 10px 30px rgba(0,0,0,.05); border-top: 4px solid #D4AF37; }
    .promo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 20px; }
    @media (max-width: 640px) { .promo-grid { grid-template-columns: 1fr; } }
    .promo-label { display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px; color: #334155; }
    .promo-input { width: 100%; padding: 11px 12px; border: 1px solid #E2E8F0; border-radius: 8px; margin-bottom: 6px; background: #fff; }
    .promo-hint { display: block; color: #94a3b8; font-size: 12px; margin-bottom: 16px; }
    .promo-errors { background: #FFF5F5; color: #C53030; border: 1px solid #FED7D7; padding: 12px 16px; border-radius: 8px; margin-bottom: 18px; font-size: 14px; }
    .promo-actions { display: flex; gap: 12px; margin-top: 18px; }
    .promo-btn { padding: 12px 24px; border-radius: 8px; font-weight: 700; border: none; cursor: pointer; text-decoration: none; }
    .promo-btn-primary { background: #D4AF37; color: #fff; }
    .promo-btn-ghost { background: #F1F5F9; color: #334155; }
</style>
