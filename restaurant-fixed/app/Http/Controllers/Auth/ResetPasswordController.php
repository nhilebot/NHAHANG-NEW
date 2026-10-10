<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    /**
     * BƯỚC 2: Xác thực OTP
     */
    public function verifyOtp(Request $request) 
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|numeric'
        ]);

        // Tìm mã OTP trong bảng password_resets
        // Lưu ý: Laravel mặc định lưu thời gian tạo tại cột 'created_at'
        $resetData = DB::table('password_resets')->where([
            ['email', $request->email],
            ['token', $request->otp], 
        ])->first();

        // Kiểm tra OTP có tồn tại và còn hạn không (ví dụ: 15 phút)
        if ($resetData && Carbon::parse($resetData->created_at)->addMinutes(15)->isFuture()) {
            
            // Xác thực thành công: Lưu email vào Session để "đánh dấu" đã qua bước OTP
            session(['reset_email' => $request->email]);
            
            return redirect()->route('password.reset.form')
                             ->with('status', 'Mã OTP chính xác. Hãy đặt mật khẩu mới.');
        }

        return back()->withErrors(['otp' => 'Mã OTP không chính xác hoặc đã hết hạn.']);
    }

    /**
     * BƯỚC 3: Hiển thị giao diện nhập mật khẩu mới
     */
    public function showResetForm() 
{
    if (!session()->has('reset_email')) {
        return redirect()->route('password.request');
    }
    // Đảm bảo file này tồn tại trong resources/views/auth/reset-password-new.blade.php
    return view('auth.reset-password-new'); 
}

    /**
     * BƯỚC CUỐI: Lưu mật khẩu mới vào Database
     */
    public function updatePassword(Request $request) 
{
    $request->validate([
        'password' => 'required|confirmed|min:8',
    ], [
        'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        'password.min' => 'Mật khẩu phải từ 8 ký tự trở lên.'
    ]);

    $email = session('reset_email');
    $user = User::where('email', $email)->first();

    if ($user) {
        // 1. Cập nhật mật khẩu
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // 2. Dọn dẹp dữ liệu thừa
        DB::table('password_resets')->where('email', $email)->delete();

        // 3. ĐĂNG XUẤT VÀ LÀM MỚI SESSION (Cách này không làm mất flash message)
        \Illuminate\Support\Facades\Auth::logout();
        
        // Chỉ xóa email reset, không dùng invalidate() để giữ lại flash data
        session()->forget('reset_email');
        
        // Làm mới ID session để bảo mật
        $request->session()->regenerateToken();
        
        // 4. Redirect kèm thông báo (Dùng success vì Blade của bạn đang check success)
        return redirect()->route('login')
    ->with('success', 'Chúc mừng! Bạn đã đổi mật khẩu thành công.');
    }

    return redirect()->route('password.request')
                     ->withErrors(['email' => 'Đã có lỗi xảy ra. Vui lòng thử lại.']);
}
}