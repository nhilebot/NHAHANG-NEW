<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; // 1. PHẢI CÓ DÒNG NÀY Ở TRÊN CÙNG

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 2. THÊM DÒNG NÀY ĐỂ ÉP HIỂN THỊ NÚT BOOTSTRAP
        Paginator::useBootstrap(); 
    }
}