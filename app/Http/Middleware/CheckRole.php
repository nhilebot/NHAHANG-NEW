<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Sử dụng: ->middleware('role:admin') | 'role:staff' | 'role:chef' | 'role:admin,staff'
 *
 * - Chỉ đúng vai trò mới được vào.
 * - Ngoại lệ duy nhất: Admin được vào khu vực Staff (để hỗ trợ vận hành).
 *   Staff/Chef/Customer KHÔNG thể vào khu vực Admin; Chef không vào khu vực Staff.
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRoleId = (int) Auth::user()->role_id;

        $allowed = array_map(
            fn ($r) => Role::MAP[strtolower(trim($r))] ?? (int) $r,
            $roles
        );

        if (in_array($userRoleId, $allowed, true)) {
            return $next($request);
        }

        if ($userRoleId === Role::ADMIN && in_array(Role::STAFF, $allowed, true)) {
            return $next($request);
        }

        // Request AJAX/JSON: trả 403 thay vì redirect
        if ($request->expectsJson()) {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        return redirect(Role::homeRoute($userRoleId))
            ->with('error', 'Bạn không có quyền vào khu vực này!');
    }
}
