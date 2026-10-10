@extends('shared')

@section('content')

<div class="container">
    <h1>Chọn món</h1>

    @foreach($menus as $menu)
        <form method="POST" action="{{ url('/add-item') }}">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order_id }}">
            <input type="hidden" name="menu_id" value="{{ $menu->id }}">

            <div style="border:1px solid #ccc; padding:10px; margin:10px;">
                {{ $menu->name }} - {{ $menu->price }}đ
                <button type="submit">Thêm</button>
            </div>
        </form>
    @endforeach

    <br>
    <a href="{{ url('/checkout/'.$order_id) }}">
        <button>Thanh toán</button>
    </a>
</div>

@endsection
<!-- PHÂN TRANG -->
<div class="text-center">
  
</div>