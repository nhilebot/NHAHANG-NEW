<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * Nguồn duy nhất cho giờ ca làm việc (đọc từ config/shifts.php).
 */
class ShiftSchedule
{
    public static function types(): array
    {
        return config('shifts.types', []);
    }

    /** Chuỗi giờ hiển thị, ví dụ "06:00 – 14:00". */
    public static function timeText(array $shift): string
    {
        return $shift['start'] . ' – ' . $shift['end'];
    }

    /**
     * Dữ liệu hiển thị cho trang lịch Staff / Chef và phần chú thích giờ ca.
     * Mỗi ca gồm: label, short, time ("06:00 – 14:00"), hours (số tiếng), color, bg, badge.
     * Có thêm ca "off" (Nghỉ) để view tra cứu được mọi giá trị trong users.shifts.
     */
    public static function viewConfigs(): array
    {
        $out = [];
        foreach (self::types() as $key => $s) {
            $out[$key] = [
                'label' => $s['label'],
                'short' => $s['short'] ?? $s['label'],
                'time'  => self::timeText($s),
                // Số tiếng của ca = (giờ kết thúc - giờ bắt đầu), làm tròn
                'hours' => (int) round((strtotime($s['end']) - strtotime($s['start'])) / 3600),
                'color' => $s['color'],
                'bg'    => $s['bg'],
                'badge' => $s['badge'],
            ];
        }
        $out['off'] = [
            'label' => 'Nghỉ',
            'short' => 'Nghỉ',
            'time'  => 'Không có ca',
            'hours' => 0,
            'color' => '#94a3b8',
            'bg'    => '#f1f5f9',
            'badge' => '☕',
        ];

        return $out;
    }

    /** Tên ca đầy đủ kèm giờ, ví dụ "Ca Sáng (06:00 – 14:00)" – dùng cho modal ở dashboard admin. */
    public static function names(): array
    {
        $out = [];
        foreach (self::types() as $key => $s) {
            $out[$key] = $s['label'] . ' (' . self::timeText($s) . ')';
        }

        return $out;
    }

    /**
     * Khung giờ cho phép đăng nhập / thao tác, đã nới thêm grace_minutes ở hai đầu.
     * Ví dụ ca Sáng 06:00–14:00 với grace 30 phút => cho phép từ 05:30 đến 14:30.
     * Dùng ở CheckEmployeeShift (middleware) và AuthController (lúc đăng nhập).
     *
     * Lưu ý: khung giờ bị giới hạn trong CÙNG MỘT NGÀY (00:00 – 23:59). Nếu không giới hạn,
     * ca bắt đầu 00:00 trừ grace sẽ quay vòng thành 23:30 và chặn nhầm cả ca.
     * Hệ thống chưa hỗ trợ ca qua đêm (ví dụ 18:00 – 02:00).
     */
    public static function windows(): array
    {
        $grace = (int) config('shifts.grace_minutes', 30);
        $out = [];
        foreach (self::types() as $key => $s) {
            $startMin = self::toMinutes($s['start']) - $grace;
            $endMin   = self::toMinutes($s['end']) + $grace;

            $out[$key] = [
                // Cắt trong khoảng 0 .. 1439 phút (00:00 .. 23:59) để không bị quay vòng qua nửa đêm
                'start' => self::fromMinutes(max(0, $startMin)),
                'end'   => self::fromMinutes(min(1439, $endMin)),
                'label' => $s['label'] . ' (' . $s['start'] . ' - ' . $s['end'] . ')',
            ];
        }

        return $out;
    }

    /** "06:30" => 390 (số phút tính từ 00:00). */
    private static function toMinutes(string $hhmm): int
    {
        [$h, $m] = array_map('intval', explode(':', $hhmm));

        return $h * 60 + $m;
    }

    /** 390 => "06:30". */
    private static function fromMinutes(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }
}
