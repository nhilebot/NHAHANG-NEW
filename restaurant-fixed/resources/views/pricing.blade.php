@extends('shared')

@section('title', 'Restaurant')

@section('content')
<div id="top" class="starter_container bg">
            <div class="follow_container">
                <div class="col-md-6 col-md-offset-3">
                    <h2 class="top-title"> Nhà Hàng</h2>
                    <h2 class="white second-title">" Tốt Nhất Trong Thành Phố "</h2>
                    <hr>
                </div>
            </div>
        </div>


    

       <!-- ============ Pricing  ============= -->


        <section id ="pricing" class="description_content">
             <div class="pricing background_content">
                <h1><span>Affordable</span> pricing</h1>
             </div>
            <div class="text-content container"> 
                <div class="container">
                    <div class="row">
                        <div id="w">
                            <ul id="filter-list" class="clearfix">
                                <li class="filter" data-filter="all">All</li>
                                <li class="filter" data-filter="breakfast">Breakfast</li>
                                <li class="filter" data-filter="special">Special</li>
                                <li class="filter" data-filter="desert">Desert</li>
                                <li class="filter" data-filter="dinner">Dinner</li>
                            </ul><!-- @end #filter-list -->    
                            <ul id="portfolio">
                                <li class="item breakfast"><img src="{{ asset('images/food_icon01.jpg') }}" alt="Food" >
                                    <h2 class="white">$20</h2>
                                </li>

                                <li class="item dinner special"><img src="{{ asset('images/food_icon02.jpg') }}">
                                    <h2 class="white">$20</h2>
                                </li>
                                <li class="item dinner breakfast"><img src="{{ asset('images/food_icon03.jpg') }}">
                                    <h2 class="white">$18</h2>
                                </li>
                                <li class="item special"><img src="{{ asset('images/food_icon04.jpg') }}">
                                    <h2 class="white">$15</h2>
                                </li>
                                <li class="item dinner"><img src="{{ asset('images/food_icon05.jpg') }}" alt="Food" >
                                    <h2 class="white">$20</h2>
                                </li>
                                <li class="item special"><img src="{{ asset('images/food_icon06.jpg') }}" alt="Food" >
                                    <h2 class="white">$22</h2>
                                </li>
                                <li class="item desert"><img src="{{ asset('images/food_icon07.jpg') }}" alt="Food" >
                                    <h2 class="white">$32</h2>
                                </li>
                                <li class="item desert breakfast"><img src="{{ asset('images/food_icon08.jpg') }}" alt="Food" >
                                    <h2 class="white">$38</h2>
                                </li>
                            </ul><!-- @end #portfolio -->
                        </div><!-- @end #w -->
                    </div>
                </div>
            </div>  
        </section>



        <!-- jQuery MỚI -->
        

        <!-- Bootstrap -->
        

        <!-- jQuery UI (datepicker) -->
        
        

        <!-- Plugin -->
        

        <!-- Main -->
@endsection
