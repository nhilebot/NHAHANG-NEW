<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class CheckEmployeeShift
{
    protected array $dayMap = [
        Carbon::MONDAY    => 'T2',
        Carbon::TUESDAY   => 'T3',
        Carbon::WEDNESDAY => 'T4',
        Carbon::THURSDAY  => 'T5',
        Carbon::FRIDAY    => 'T6',
        Carbon::SATURDAY  => 'T7',
        Carbon::SUNDAY    => 'CN',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cho phép truy cập trang lịch của cả Staff và Chef
        if ($request->routeIs('staff.schedule') || $request->routeIs('chef.schedule')) {
            return $next($request);
        }

        $user = Auth::user();

        // 2. Bỏ qua nếu là Admin
        if (!$user || $user->role_id == \App\Models\Role::ADMIN) {
            return $next($request);
        }

        // 3. Xác định ca hôm nay
        $todayKey = $this->dayMap[Carbon::now()->dayOfWeek] ?? 'T2';
        $shifts = $user->shifts ?? [];
        $todayShift = $shifts[$todayKey] ?? 'off';

        $errorMessage = null;

        // 4. Kiểm tra ca nghỉ
        if ($todayShift === 'off') {
            $errorMessage = "Hôm nay ({$todayKey}) bạn không có ca trực. Bạn được chuyển đến trang xem lịch!";
        } else {
            // 5. Kiểm tra khung giờ ca
            $shiftHours = [
                'morning'   => ['start' => '05:30', 'end' => '13:30', 'label' => 'Ca Sáng (06:00 - 13:00)'],
                'afternoon' => ['start' => '12:30', 'end' => '20:30', 'label' => 'Ca Chiều (13:00 - 20:00)'],
                'full'      => ['start' => '05:30', 'end' => '20:30', 'label' => 'Cả Ngày (06:00 - 20:00)'],
            ];

            $currentTime = Carbon::now()->format('H:i');
            if (isset($shiftHours[$todayShift])) {
                $shift = $shiftHours[$todayShift];
                if ($currentTime < $shift['start'] || $currentTime > $shift['end']) {
                    $errorMessage = "Hiện tại ({$currentTime}) ngoài khung giờ của {$shift['label']}!";
                }
            }
        }

        // Nếu có lỗi (nghỉ hoặc ngoài giờ), điều hướng về đúng trang lịch của vai trò đó
        if ($errorMessage) {
            if ($user->role_id == \App\Models\Role::CHEF || $user->role === 'chef') {
                return redirect()->route('chef.schedule')->with('error', $errorMessage);
            }

            return redirect()->route('staff.schedule')->with('error', $errorMessage);
        }

        return $next($request);
    }
}