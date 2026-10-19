<?php

namespace App\Models\Concerns;

/**
 * Sinh mã tự tăng dạng PREFIX + số (vd NL0001, NCC001) dựa trên mã lớn nhất hiện có.
 * Cột `code` có unique index nên nếu hai người tạo cùng lúc, bản ghi sau sẽ bị DB từ chối
 * (controller bắt lỗi và báo người dùng thử lại) chứ không bao giờ trùng mã.
 */
trait GeneratesCode
{
    public static function nextCode(string $prefix, int $pad): string
    {
        $max = 0;

        static::query()
            ->where('code', 'like', $prefix . '%')
            ->pluck('code')
            ->each(function ($code) use ($prefix, &$max) {
                $num = substr($code, strlen($prefix));
                if (ctype_digit($num)) {
                    $max = max($max, (int) $num);
                }
            });

        return $prefix . str_pad((string) ($max + 1), $pad, '0', STR_PAD_LEFT);
    }
}