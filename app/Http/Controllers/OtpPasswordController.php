<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class OtpPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Bạn phải nhập email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Email này không tồn tại trong hệ thống.'])->withInput();
        }

        $otp = random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        Mail::raw(
            "Mã OTP khôi phục mật khẩu của bạn là: {$otp}\nMã có hiệu lực trong 10 phút.",
            function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Mã OTP khôi phục mật khẩu');
            }
        );

        return redirect()->route('password.verify')->with('status', 'Mã OTP đã được gửi đến email của bạn.');
    }

    public function showVerifyForm()
    {
        return view('verify-otp');
    }

    public function verifyOtp(Request $request) 
{
    // 1. Validate dữ liệu từ Form
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required',
        'password' => 'required|confirmed|min:8',
    ]);

    // 2. Tìm User dựa trên email trong session hoặc request
    $email = session('reset_email') ?? $request->email;
    $user = \App\Models\User::where('email', $email)->first();

    if ($user) {
        // Cập nhật mật khẩu mới
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        // 3. QUAN TRỌNG: Đảm bảo không có lệnh Auth::login($user) ở đây.
        // Nếu lỡ có phiên đăng nhập nào cũ, hãy xóa sạch:
        \Illuminate\Support\Facades\Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 4. Xóa OTP trong bảng password_reset_tokens
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();

        // 5. Điều hướng về trang Login kèm thông báo thành công
        return redirect()->route('login')->with('status', 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập bằng mật khẩu mới.');
    }

    return back()->withErrors(['otp' => 'Mã OTP không hợp lệ hoặc lỗi hệ thống.']);
}
}
