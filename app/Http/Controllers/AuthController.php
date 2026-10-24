<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class AuthController extends Controller
{
    // Hiển thị trang đăng nhập
    public function showLogin()
    {
        return view('login');
    }

   // Xử lý đăng nhập
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate(); // Chống session fixation

            $user = Auth::user();

            // 🟢 KIỂM TRA CA TRỰC CHO CẢ STAFF VÀ CHEF
            if (in_array($user->role_id, [Role::STAFF, Role::CHEF]) || in_array($user->role, ['staff', 'chef'])) {
                $dayMap = [
                    Carbon::MONDAY    => 'T2',
                    Carbon::TUESDAY   => 'T3',
                    Carbon::WEDNESDAY => 'T4',
                    Carbon::THURSDAY  => 'T5',
                    Carbon::FRIDAY    => 'T6',
                    Carbon::SATURDAY  => 'T7',
                    Carbon::SUNDAY    => 'CN',
                ];

                $todayKey = $dayMap[Carbon::now()->dayOfWeek] ?? 'T2';
                $todayShift = ($user->shifts ?? [])[$todayKey] ?? 'off';
                $currentTime = Carbon::now()->format('H:i');

                // Khung giờ từng ca lấy từ config/shifts.php (đã cộng thời gian cho phép sớm/trễ).
                // Muốn đổi giờ làm: sửa config/shifts.php, không sửa ở đây.
                $shiftHours = \App\Support\ShiftSchedule::windows();

                // 🔥 ĐOẠN BẠN HỎI NẰM NGAY ĐÂY:
                // Xác định route chuyển hướng khi ngoài ca
                $scheduleRoute = ($user->role_id == Role::CHEF || $user->role === 'chef') 
                                 ? 'chef.schedule' 
                                 : 'staff.schedule';

                // Nếu hôm nay nghỉ (off) -> Đưa về trang lịch tương ứng
                if ($todayShift === 'off') {
                    return redirect()->route($scheduleRoute)->with('error', "Hôm nay ({$todayKey}) bạn không có ca trực. Bạn được chuyển đến trang xem lịch làm việc.");
                }

                // Nếu ngoài khung giờ quy định -> Đưa về trang lịch tương ứng
                if (isset($shiftHours[$todayShift])) {
                    $shift = $shiftHours[$todayShift];
                    if ($currentTime < $shift['start'] || $currentTime > $shift['end']) {
                        return redirect()->route($scheduleRoute)->with('error', "Hiện tại ({$currentTime}) ngoài khung giờ làm việc của {$shift['label']}. Bạn được chuyển đến trang xem lịch làm việc.");
                    }
                }

                // Đúng ca và đúng giờ -> Chuyển về Dashboard làm việc của từng vai trò
                return redirect()->intended(Role::homeRoute((int) $user->role_id));
            }

            // Các vai trò khác (Admin, Customer) chuyển hướng bình thường
            return redirect()->intended(Role::homeRoute((int) $user->role_id));
        }

        return back()->withErrors([
            'email' => 'Sai tài khoản hoặc mật khẩu'
        ])->onlyInput('email');
    }

    // Hiển thị trang đăng ký
    public function showRegister()
    {
        return view('register');
    }

    // Xử lý đăng ký
   public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phone' => 'required|string|max:20',
        'password' => 'required|confirmed|min:8'
    ]);

    // Thêm role_id vào đây để sửa lỗi SQL 1364
    $user = new User([
        'name'     => $request->name,
        'email'    => $request->email,
        'phone'    => $request->phone,
        'password' => $request->password, // cast 'hashed' tự băm
    ]);
    $user->role_id = Role::CUSTOMER; // gán tường minh, không qua mass-assignment
    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'Đăng ký thành công'
    ]);
}

    // Xử lý đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Đã đăng xuất thành công');
    }
    protected function loggedOut(Request $request)
{
    return redirect('/');
}
// protected function authenticated(Request $request, $user)
// {
//     // Kiểm tra nếu là nhân viên hoặc admin thì đẩy vào trang quản lý đặt bàn
//     if (in_array($user->role_id, ['admin', 'staff', 'chef'])) {
//         return redirect()->route('admin.reservations.index');
//     }

//     return redirect('/'); // Khách thì về trang chủ
// }
}