<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
{
    // Sử dụng directive thay vì macro
    \Illuminate\Support\Facades\Blade::directive('statusBadge', function ($status) {
        return "<?php 
            \$statusMap = [
                'pending' => ['label' => 'Chờ duyệt', 'class' => 'st-pending'],
                'confirmed' => ['label' => 'Đã xác nhận', 'class' => 'st-confirmed'],
                'ready' => ['label' => 'Chờ phục vụ', 'class' => 'st-ready'],
                'served' => ['label' => 'Đã lên món', 'class' => 'st-served'],
                'paid' => ['label' => 'Hoàn tất', 'class' => 'st-success'],
            ];
            \$current = \$statusMap[strtolower($status)] ?? ['label' => $status, 'class' => 'badge-secondary'];
            echo \"<span class='badge-status {\$current['class']}'>{\$current['label']}</span>\";
        ?>";
    });
}
}