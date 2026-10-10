<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
        $request->session()->regenerate(); // chống session fixation
        return redirect()->intended(Role::homeRoute((int) Auth::user()->role_id));
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