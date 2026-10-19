@extends('layouts.admin')
@php $title = $category ? 'Sửa nhóm nguyên liệu' : 'Thêm nhóm nguyên liệu'; @endphp
@section('title', $title)
@section('page_title', $title)
@section('topbar_title', $title)

@section('admin_content')
@include('admin.inventory._style')

<div class="inv-panel inv-form">
    <h2 class="inv-title">{{ $title }}</h2>

    <form method="POST" action="{{ $category ? route('admin.inventory.categories.update', $category) : route('admin.inventory.categories.store') }}">
        @csrf
        @if($category) @method('PUT') @endif

        <label for="name">Tên nhóm</label>
        <input type="text" id="name" name="name" value="{{ old('name', $category->name ?? '') }}" required maxlength="100">
        @error('name')<div class="inv-err">{{ $message }}</div>@enderror

        <label for="description">Mô tả (không bắt buộc)</label>
        <input type="text" id="description" name="description" value="{{ old('description', $category->description ?? '') }}" maxlength="255">
        @error('description')<div class="inv-err">{{ $message }}</div>@enderror

        <div class="inv-form-actions">
            <button type="submit" class="btn btn-primary">Lưu nhóm</button>
            <a href="{{ route('admin.inventory.categories.index') }}" class="btn btn-ghost">Hủy</a>
        </div>
    </form>
</div>
@endsection
