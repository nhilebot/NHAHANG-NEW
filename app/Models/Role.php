<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * ID cố định của các vai trò (được seed bởi RoleSeeder).
     * Giữ nguyên 2..5 để tương thích dữ liệu cũ; ID 1 (Thu ngân) đã bị loại bỏ.
     */
    public const ADMIN    = 2;
    public const STAFF    = 3;
    public const CHEF     = 4;
    public const CUSTOMER = 5;

    /** Tên vai trò => ID */
    public const MAP = [
        'admin'    => self::ADMIN,
        'staff'    => self::STAFF,
        'chef'     => self::CHEF,
        'customer' => self::CUSTOMER,
    ];

    protected $table = 'roles';

    protected $fillable = ['name', 'display_name'];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'id');
    }

    /** Trang chủ của từng vai trò sau khi đăng nhập. */
    public static function homeRoute(int $roleId): string
    {
        return match ($roleId) {
            self::ADMIN => route('admin.index'),
            self::STAFF => route('staff.reservations.index'),
            self::CHEF  => route('chef.index'),
            default     => url('/'),
        };
    }
}
