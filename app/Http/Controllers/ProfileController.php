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
            'name.required'   => 'Vui lòng nhập họ và tên.',
            'avatar.image'    => 'File phải là hình ảnh (JPG hoặc PNG).',
            'avatar.mimes'    => 'Ảnh đại diện chỉ nhận định dạng JPG, PNG hoặc GIF.',
            'avatar.max'      => 'Ảnh quá nặng, vui lòng chọn ảnh dưới 2MB.',
            'avatar.uploaded' => 'Tải ảnh lên thất bại (thường do ảnh quá nặng). Vui lòng chọn ảnh dưới 2MB.',
        ]);

        // Tạo mảng dữ liệu để cập nhật
        $updateData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
        ];

        // 2. Xử lý lưu File Ảnh nếu Nhi có chọn ảnh mới
        $hasNewAvatar = $request->hasFile('avatar');
        if ($hasNewAvatar) {
            // Lưu thẳng vào public/uploads/avatars => KHÔNG phụ thuộc vào lệnh "php artisan storage:link"
            // (nếu dùng storage/app/public mà chưa có liên kết public/storage thì ảnh sẽ không hiện)
            $file = $request->file('avatar');
            $dir  = public_path('uploads/avatars');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $fileName = 'u' . $user->id . '_' . time() . '.' . $file->extension();
            $file->move($dir, $fileName);

            // Xóa ảnh cũ (chỉ xóa ảnh do chức năng này tạo ra)
            if ($user->avatar && str_starts_with($user->avatar, 'uploads/avatars/')) {
                @unlink(public_path($user->avatar));
            }

            // Lưu đường dẫn (tính từ thư mục public) vào Database
            $updateData['avatar'] = 'uploads/avatars/' . $fileName;
        }

        // 3. Cập nhật vào Database
        \App\Models\User::where('id', $user->id)->update($updateData);

        // 4. Quay lại trang profile kèm thông báo thành công
        return redirect()->back()->with('success', $hasNewAvatar
            ? 'Thông tin và ảnh đại diện của ' . $request->name . ' đã được cập nhật!'
            : 'Thông tin của ' . $request->name . ' đã được cập nhật!');
    }
}