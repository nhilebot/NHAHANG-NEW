<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $menuId)
    {
        $request->validate([
            'content' => 'required|string|min:3|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $menu = Menu::findOrFail($menuId);

        Comment::create([
            'menu_id' => $menu->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'rating' => $request->rating,
            'status' => 1, // Tự động hiển thị
        ]);

        return redirect()->back()->with('success', 'Bình luận của bạn đã được gửi thành công!');
    }

    // ĐÂY LÀ HÀM UPDATE VỪA ĐƯỢC THÊM VÀO
   // ĐÂY LÀ HÀM UPDATE ĐÃ ĐƯỢC FIX LỖI ÉP KIỂU
    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|min:3|max:1000',
            'rating'  => 'required|integer|min:1|max:5',
        ]);

        $comment = Comment::findOrFail($id);

        // 🔥 FIX CHÍNH TẠI ĐÂY: Dùng != thay vì !== để chống lệch kiểu String vs Integer
        if ($comment->user_id != Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền sửa bình luận này!');
        }

        // Cập nhật dữ liệu
        $comment->update([
            'content' => $request->content,
            'rating'  => $request->rating,
        ]);

        return redirect()->back()->with('success', 'Đã cập nhật bình luận thành công!');
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        
        if ($comment->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Bạn không có quyền xóa bình luận này!');
        }
        
        $comment->delete();
        
        return redirect()->back()->with('success', 'Đã xóa bình luận!');
    }
}