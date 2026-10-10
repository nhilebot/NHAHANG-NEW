<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User; 

class ProfileController extends Controller
{
    // Trang hiển thị thông tin
    public function index()
    {
        $user = Auth::user(); 
        return view('profile', compact('user'));
    }

    // Hàm xử lý việc CHỈNH SỬA thông tin
    // Hàm xử lý việc CHỈNH SỬA thông tin
    public function update(Request $request)
    {
        $user = Auth::user(); // Lấy user hiện tại

        // 1. Kiểm tra dữ liệu Nhi nhập vào (Thêm kiểm tra cho file avatar)
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Tối đa 2MB
        ], [
            'name.required' => 'Nhi ơi, không được để trống họ tên đâu nhé!',
            'avatar.image' => 'File phải là hình ảnh nha Nhi!',
            'avatar.max' => 'Ảnh nặng quá, dưới 2MB thôi Nhi nhé!'
        ]);

        // Tạo mảng dữ liệu để cập nhật
        $updateData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
        ];

        // 2. Xử lý lưu File Ảnh nếu Nhi có chọn ảnh mới
        if ($request->hasFile('avatar')) {
            // Lưu file vào storage/app/public/avatars
            $path = $request->file('avatar')->store('avatars', 'public');
            
            // Thêm đường dẫn ảnh vào mảng dữ liệu để lưu vào Database
            $updateData['avatar'] = $path;
        }

        // 3. Cập nhật vào Database
        \App\Models\User::where('id', $user->id)->update($updateData);

        // 4. Quay lại trang profile kèm thông báo thành công
        return redirect()->back()->with('success', 'Thông tin và ảnh của Nhi đã được cập nhật!');
    }
}