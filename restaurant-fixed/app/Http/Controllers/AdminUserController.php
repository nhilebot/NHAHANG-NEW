<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    // ✅ Dùng 'role:2' thống nhất với hệ thống, bỏ 'isAdmin' cũ
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                return redirect()->route('admin.index')
                                 ->with('error', 'Bạn không có quyền truy cập.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = User::with('role');

        if ($request->filled('role')) {
            $query->whereHas('role', fn($q) => $q->where('name', $request->role));
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(15);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role_id'  => ['required', Rule::in(array_values(Role::MAP))],
            'phone'    => 'nullable|string|max:20',
        ]);

        $user = new User($request->only('name', 'email', 'phone'));
        $user->password = Hash::make($request->password);
        $user->role_id  = (int) $request->role_id;
        $user->save();

        return redirect()->route('admin.users.index')
                         ->with('success', 'Tạo tài khoản thành công!');
    }

    public function edit($id)
    {
        $user  = User::with('role')->findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $id,
            'role_id'  => ['required', Rule::in(array_values(Role::MAP))],
            'phone'    => 'nullable|string|max:20',
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Không cho Admin tự hạ quyền của chính mình (tránh khoá hệ thống)
        if ($user->id === auth()->id() && (int) $request->role_id !== Role::ADMIN) {
            return back()->with('error', 'Không thể tự đổi vai trò của tài khoản đang đăng nhập!');
        }

        $user->fill($request->only('name', 'email', 'phone'));
        $user->role_id = (int) $request->role_id;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')
                         ->with('success', 'Cập nhật tài khoản thành công!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()
                             ->with('error', 'Không thể xóa tài khoản đang đăng nhập!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'Xóa tài khoản thành công!');
    }
 public function employeeIndex(Request $request)
{
    // 1. Lấy role từ URL (staff hoặc chef)
    $role = $request->query('role', 'staff'); 

    // 2. QUAN TRỌNG: Phải có with('role') để Laravel bốc dữ liệu từ bảng roles sang
    $employees = User::with('role') 
        ->whereHas('role', function($q) use ($role) {
            $q->where('name', $role);
        })->get();

    // 3. Lấy danh sách role để hiện trong select box
    $roles = Role::whereIn('name', ['staff', 'chef'])->get();

    // 4. Truyền đúng 3 biến này xuống View
    return view('admin.employees.index', compact('employees', 'roles', 'role'));
}

public function employeeStore(Request $request)
{
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users',
        'password' => 'required|min:8',
        'role_id'  => ['required', Rule::in([Role::STAFF, Role::CHEF])],
        'phone'    => 'nullable|string|max:20',
    ]);

    $user = new User($request->only('name', 'email', 'phone'));
    $user->password = Hash::make($request->password);
    $user->role_id  = (int) $request->role_id;
    $user->save();

    // Lấy tên role để redirect đúng tab
    $roleName = Role::find($request->role_id)?->name ?? 'staff';

    return redirect()->route('admin.employees.index', ['role' => $roleName])
                     ->with('success', 'Đã thêm nhân viên thành công!');
}

public function employeeDestroy($id)
{
    $user = User::findOrFail($id);

    if ($user->id === auth()->id()) {
        return redirect()->back()->with('error', 'Không thể xóa tài khoản đang đăng nhập!');
    }
    abort_unless(in_array((int) $user->role_id, [Role::STAFF, Role::CHEF], true), 403);

    $roleName = $user->role?->name ?? 'staff';
    $user->delete();

    return redirect()->route('admin.employees.index', ['role' => $roleName])
                     ->with('success', 'Đã xóa nhân viên thành công!');
}

public function updateShift(Request $request)
{
    // Nếu có bảng shifts riêng thì lưu vào đó
    // Tạm thời lưu vào JSON trong bảng users nếu có cột shifts
    $data = $request->validate([
        'user_id'     => ['required', 'exists:users,id'],
        'day_of_week' => ['required', 'string', 'max:20'],
        'shift'       => ['nullable', 'string', 'max:50'],
    ]);
    $user = User::findOrFail($data['user_id']);

    $shifts = $user->shifts ?? [];
    $shifts[$data['day_of_week']] = $data['shift'] ?? null;
    $user->shifts = $shifts;
    $user->save();

    return response()->json(['success' => true]);
}
}