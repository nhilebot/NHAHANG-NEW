@extends('layouts.admin')
@section('title', 'Chi tiết công thức')
@section('page_title', 'Chi tiết công thức')
@section('topbar_title', 'Chi tiết công thức')

@section('admin_content')
@include('admin.inventory._style')

<div class="inv-panel">
    <div class="inv-head">
        <div>
            <h2 class="inv-title">{{ $recipe->menu->name ?? 'Món đã bị xóa' }}</h2>
            <p class="inv-sub">Định lượng cho <strong>một phần</strong> ăn.
                @if($recipe->is_sample)<span class="inv-badge low">Số liệu minh họa, cần nhà hàng xác nhận</span>
                @else<span class="inv-badge ok">Đã xác nhận</span>@endif
            </p>
        </div>
        <div class="inv-actions">
            <a href="{{ route('admin.inventory.recipes.edit', $recipe) }}" class="btn btn-primary">Sửa</a>
            <a href="{{ route('admin.inventory.recipes.index') }}" class="btn btn-ghost">Quay lại</a>
        </div>
    </div>

    <dl class="inv-dl">
        <dt>Danh mục</dt><dd>{{ $recipe->menu->category_relation->name ?? '—' }}</dd>
        <dt>Giá bán</dt><dd>{{ $recipe->menu ? number_format($recipe->menu->price, 0, ',', '.') . 'đ' : '—' }}</dd>
        <dt>Ghi chú</dt><dd>{{ $recipe->note ?: '—' }}</dd>
        <dt>Cập nhật</dt>
        <dd>{{ $recipe->updated_at?->format('d/m/Y H:i') }}@if($recipe->updater) bởi {{ $recipe->updater->name }}@endif</dd>
    </dl>

    @if($hasBroken)
        <div class="inv-readonly" style="margin-top:16px;border-style:solid;border-color:#FCA5A5;background:#FEF2F2">
            ⚠️ Có nguyên liệu mà đơn vị trong công thức <strong>không còn tương thích</strong> với đơn vị kho. Hãy sửa công thức trước khi dùng.
        </div>
    @endif

    <h3 class="inv-title" style="font-size:16px;margin-top:22px">Nguyên liệu</h3>
    <div class="inv-wrap">
        <table class="inv-table">
            <thead>
                <tr>
                    <th>Nguyên liệu</th><th class="r">Định lượng / phần</th>
                    <th class="r">Quy đổi theo kho</th><th class="r">Tồn kho</th><th class="r">Đủ cho (phần)</th>
                </tr>
            </thead>
            <tbody>
            @foreach($rows as $row)
                @php $ing = $row['ingredient']; $it = $row['item']; @endphp
                <tr class="{{ $row['broken'] ? 'is-out' : '' }}">
                    <td>
                        <a href="{{ route('admin.inventory.ingredients.show', $ing) }}"><strong>{{ $ing->name }}</strong></a>
                        <span class="inv-code">{{ $ing->code }}</span>
                        @if(!$ing->is_active)<span class="inv-badge off">Ngừng sử dụng</span>@endif
                    </td>
                    <td class="r"><strong>{{ \App\Models\Ingredient::fmt($it->quantity) }}</strong> {{ $it->unit }}</td>
                    <td class="r">
                        @if($row['broken'])<span class="inv-badge out">Sai đơn vị</span>
                        @else{{ \App\Models\Ingredient::fmt($row['perPortionStock']) }} {{ $ing->unit }}@endif
                    </td>
                    <td class="r">{{ \App\Models\Ingredient::fmt($ing->current_stock) }} {{ $ing->unit }}</td>
                    <td class="r">{{ $row['portions'] === null ? '—' : number_format($row['portions']) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <dl class="inv-dl" style="margin-top:18px">
        <dt>Có thể làm tối đa</dt>
        <dd>{{ $minPortions === null ? '—' : number_format($minPortions) . ' phần (theo tồn kho hiện tại, nguyên liệu thiếu nhất quyết định)' }}</dd>
        <dt>Giá vốn tham khảo / phần</dt>
        <dd>
            {{ number_format($cost, 0, ',', '.') }}đ
            @if(!$costComplete)<span class="inv-hint">(chưa đủ: có nguyên liệu chưa có giá nhập tham khảo hoặc sai đơn vị)</span>@endif
        </dd>
    </dl>
</div>
@endsection
