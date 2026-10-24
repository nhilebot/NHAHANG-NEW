<?php

namespace App\Services;

use InvalidArgumentException;

/**
 * Quy đổi đơn vị tính một cách AN TOÀN cho công thức món ăn và kho.
 *
 * Chỉ quy đổi giữa các đơn vị CÙNG NHÓM:
 *   - khối lượng: g <-> kg
 *   - thể tích:   ml <-> lít
 * Các đơn vị đếm (quả, chai, lon, gói, cái, ...) chỉ khớp với CHÍNH NÓ.
 * Không bao giờ đoán "1 chai = bao nhiêu ml", "1 quả = bao nhiêu g".
 *
 * Lớp này không phụ thuộc Laravel, nên kiểm thử được bằng PHP thuần.
 */
class UnitConverter
{
    private const MASS   = 'mass';
    private const VOLUME = 'volume';

    /** Tên đơn vị (đã chuẩn hóa) => [nhóm, hệ số về đơn vị gốc của nhóm (g hoặc ml)] */
    private const KNOWN = [
        'g'   => [self::MASS, 1],
        'kg'  => [self::MASS, 1000],
        'ml'  => [self::VOLUME, 1],
        'lít' => [self::VOLUME, 1000],
    ];

    /** Cách viết khác => tên chuẩn */
    private const ALIASES = [
        'gram' => 'g', 'gr' => 'g', 'gam' => 'g',
        'kilogram' => 'kg', 'kí' => 'kg', 'ký' => 'kg', 'kilo' => 'kg',
        'milliliter' => 'ml', 'millilit' => 'ml', 'mililit' => 'ml',
        'l' => 'lít', 'lit' => 'lít', 'liter' => 'lít', 'litre' => 'lít',
    ];

    /** Chuẩn hóa tên đơn vị: bỏ khoảng trắng thừa, chữ thường, gom các cách viết khác nhau. */
    public static function normalize(string $unit): string
    {
        $u = mb_strtolower(trim(preg_replace('/\s+/u', ' ', $unit)));

        return self::ALIASES[$u] ?? $u;
    }

    /** Nhóm của đơn vị: 'mass', 'volume', hoặc 'count:<tên>' (đơn vị đếm chỉ khớp với chính nó). */
    public static function group(string $unit): string
    {
        $u = self::normalize($unit);

        return self::KNOWN[$u][0] ?? ('count:' . $u);
    }

    /** Hai đơn vị có quy đổi được cho nhau không? */
    public static function compatible(string $a, string $b): bool
    {
        if (trim($a) === '' || trim($b) === '') {
            return false;
        }

        return self::group($a) === self::group($b);
    }

    /**
     * Các đơn vị được phép dùng trong CÔNG THỨC cho một nguyên liệu có đơn vị kho là $stockUnit.
     * Ví dụ kho "kg" => ['kg', 'g']; kho "lít" => ['lít', 'ml']; kho "gói" => ['gói'].
     *
     * @return string[]
     */
    public static function allowedFor(string $stockUnit): array
    {
        $u = self::normalize($stockUnit);

        return match (self::KNOWN[$u][0] ?? null) {
            self::MASS   => ['kg', 'g'],
            self::VOLUME => ['lít', 'ml'],
            default      => [$u],
        };
    }

    /**
     * Đổi $qty từ đơn vị $from sang $to (kết quả làm tròn 3 chữ số thập phân, đúng độ chính xác của kho).
     *
     * @throws InvalidArgumentException nếu hai đơn vị không cùng nhóm
     */
    public static function convert(float $qty, string $from, string $to): float
    {
        if (!self::compatible($from, $to)) {
            throw new InvalidArgumentException(
                "Không thể quy đổi từ \"{$from}\" sang \"{$to}\" (khác loại đơn vị)."
            );
        }

        $f = self::normalize($from);
        $t = self::normalize($to);

        // Đơn vị đếm hoặc cùng đơn vị: giữ nguyên
        if ($f === $t || !isset(self::KNOWN[$f], self::KNOWN[$t])) {
            return round($qty, 3);
        }

        return round($qty * self::KNOWN[$f][1] / self::KNOWN[$t][1], 3);
    }

    /**
     * Cộng nhiều số lượng (mỗi cái có đơn vị riêng) về MỘT đơn vị đích rồi mới trả kết quả.
     * Cộng ở đơn vị gốc (g/ml) để không mất độ chính xác, chỉ làm tròn MỘT lần ở cuối.
     *
     * @param array<int, array{0: float|int|string, 1: string}> $parts  [[số lượng, đơn vị], ...]
     * @throws InvalidArgumentException nếu có phần không cùng nhóm với đơn vị đích
     */
    public static function sumTo(array $parts, string $to): float
    {
        $t = self::normalize($to);
        $total = 0.0;

        foreach ($parts as [$qty, $unit]) {
            if (!self::compatible($unit, $to)) {
                throw new InvalidArgumentException(
                    "Không thể cộng \"{$unit}\" vào \"{$to}\" (khác loại đơn vị)."
                );
            }
            $u = self::normalize($unit);
            $total += (float) $qty * (self::KNOWN[$u][1] ?? 1);
        }

        $targetFactor = self::KNOWN[$t][1] ?? 1;

        return round($total / $targetFactor, 3);
    }
}
