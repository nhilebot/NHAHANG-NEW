<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Support\PasswordPolicy;

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
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'password' => array_merge(PasswordPolicy::rules(), ['confirmed']),
        ], array_merge(PasswordPolicy::messages(), [
            'otp.required' => 'Vui lòng nhập mã OTP.',
            'otp.digits' => 'Mã OTP phải gồm 6 chữ số.',
        ]));

        $email = $request->input('email');
        $verified = DB::transaction(function () use ($email, $request) {
            $reset = DB::table('password_reset_tokens')
                ->where('email', $email)
                ->lockForUpdate()
                ->first();

            if (
                ! $reset
                || ! $reset->created_at
                || now()->greaterThanOrEqualTo(\Illuminate\Support\Carbon::parse($reset->created_at)->addMinutes(10))
                || ! Hash::check($request->input('otp'), $reset->token)
            ) {
                return false;
            }

            $user = User::where('email', $email)->first();
            if (! $user) {
                return false;
            }

            $user->password = Hash::make($request->input('password'));
            $user->save();
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return true;
        });

        if (! $verified) {
            return back()->withErrors([
                'otp' => 'Mã OTP không hợp lệ hoặc đã hết hạn. Vui lòng yêu cầu mã mới.',
            ])->withInput($request->only('email'));
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập bằng mật khẩu mới.');
    }
}
