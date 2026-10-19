@extends('layouts.admin')
@php $title = $supplier ? 'Sửa nhà cung cấp' : 'Thêm nhà cung cấp'; @endphp
@section('title', $title)
@section('page_title', $title)
@section('topbar_title', $title)

@section('admin_content')
@include('admin.inventory._style')

<div class="inv-panel inv-form">
    <h2 class="inv-title">{{ $title }}</h2>

    <form method="POST" action="{{ $supplier ? route('admin.inventory.suppliers.update', $supplier) : route('admin.inventory.suppliers.store') }}">
        @csrf
        @if($supplier) @method('PUT') @endif

        <div class="row2">
            <div>
                <label for="code">Mã nhà cung cấp</label>
                <input type="text" id="code" name="code" value="{{ old('code', $supplier->code ?? '') }}" placeholder="{{ $suggestedCode }}" maxlength="30">
                <div class="inv-hint">Để trống để hệ thống tự đặt ({{ $suggestedCode }}).</div>
                @error('code')<div class="inv-err">{{ $message }}</div>@enderror
            </div>
            <div>
                <label for="name">Tên nhà cung cấp</label>
                <input type="text" id="name" name="name" value="{{ old('name', $supplier->name ?? '') }}" required maxlength="150">
                @error('name')<div class="inv-err">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row2">
            <div>
                <label for="phone">Số điện thoại</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}" maxlength="20">
                @error('phone')<div class="inv-err">{{ $message }}</div>@enderror
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $supplier->email ?? '') }}" maxlength="150">
                @error('email')<div class="inv-err">{{ $message }}</div>@enderror
            </div>
        </div>

        <label for="address">Địa chỉ</label>
        <input type="text" id="address" name="address" value="{{ old('address', $supplier->address ?? '') }}" maxlength="255">
        @error('address')<div class="inv-err">{{ $message }}</div>@enderror

        <label for="note">Ghi chú</label>
        <textarea id="note" name="note" rows="3" maxlength="1000">{{ old('note', $supplier->note ?? '') }}</textarea>
        @error('note')<div class="inv-err">{{ $message }}</div>@enderror

        <label class="inv-check">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $supplier->is_active ?? true))>
            Đang hợp tác
        </label>

        <div class="inv-form-actions">
            <button type="submit" class="btn btn-primary">Lưu nhà cung cấp</button>
            <a href="{{ route('admin.inventory.suppliers.index') }}" class="btn btn-ghost">Hủy</a>
        </div>
    </form>
</div>
@endsection
