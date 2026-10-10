<?php

namespace App\Helpers;

class StatusHelper
{
    /**
     * Tất cả các trạng thái và bản dịch tiếng Việt
     */
    const STATUS_TRANSLATIONS = [
        // Trạng thái đơn hàng (Order)
        'pending' => [
            'text' => 'Chờ duyệt',
            'text_full' => 'Đơn hàng đang chờ duyệt',
            'emoji' => '⏳',
            'color' => 'warning',
            'bg_class' => 'status-pending',
            'badge_class' => 'badge-warning',
        ],
        'pending_payment' => [
    'text' => 'Chờ thanh toán',
    'text_full' => 'Đơn hàng đang chờ thanh toán',
    'emoji' => '💰',
    'color' => 'warning',
    'bg_class' => 'status-pending_payment',
    'badge_class' => 'badge-warning',
],
'pending_deposit' => [
    'text' => 'Chờ xác nhận cọc',
    'text_full' => 'Đơn đặt bàn đang chờ nhân viên xác nhận tiền cọc',
    'emoji' => '⏳',
    'color' => 'warning',
    'bg_class' => 'status-pending_deposit',
    'badge_class' => 'badge-warning',
],
        'confirmed' => [
            'text' => 'Đã xác nhận',
            'text_full' => 'Đơn hàng đã được xác nhận',
            'emoji' => '✅',
            'color' => 'info',
            'bg_class' => 'status-confirmed',
            'badge_class' => 'badge-info',
        ],
        'processing' => [
            'text' => 'Đang xử lý',
            'text_full' => 'Đơn hàng đang được xử lý',
            'emoji' => '⚙️',
            'color' => 'warning',
            'bg_class' => 'status-processing',
            'badge_class' => 'badge-warning',
        ],
        'ready' => [
            'text' => 'Chờ phục vụ',
            'text_full' => 'Đơn hàng đã sẵn sàng phục vụ',
            'emoji' => '🍽️',
            'color' => 'info',
            'bg_class' => 'status-ready',
            'badge_class' => 'badge-info',
        ],
        'served' => [
            'text' => 'Đã lên món',
            'text_full' => 'Đơn hàng đã được lên món',
            'emoji' => '😋',
            'color' => 'success',
            'bg_class' => 'status-served',
            'badge_class' => 'badge-success',
        ],
        'completed' => [
            'text' => 'Hoàn tất',
            'text_full' => 'Đơn hàng đã hoàn tất',
            'emoji' => '🏁',
            'color' => 'success',
            'bg_class' => 'status-completed',
            'badge_class' => 'badge-success',
        ],
        'cancelled' => [
            'text' => 'Đã hủy',
            'text_full' => 'Đơn hàng đã bị hủy',
            'emoji' => '❌',
            'color' => 'danger',
            'bg_class' => 'status-cancelled',
            'badge_class' => 'badge-danger',
        ],
        
        // Trạng thái thanh toán
        'paid_cash' => [
            'text' => 'Tiền mặt',
            'text_full' => 'Đã thanh toán bằng tiền mặt',
            'emoji' => '💵',
            'color' => 'success',
            'bg_class' => 'status-paid_cash',
            'badge_class' => 'badge-success',
        ],
        'paid_transfer' => [
            'text' => 'Chuyển khoản',
            'text_full' => 'Đã thanh toán bằng chuyển khoản',
            'emoji' => '📱',
            'color' => 'success',
            'bg_class' => 'status-paid_transfer',
            'badge_class' => 'badge-success',
        ],
        'paid' => [
            'text' => 'Đã thanh toán',
            'text_full' => 'Đơn hàng đã thanh toán',
            'emoji' => '💳',
            'color' => 'success',
            'bg_class' => 'status-paid',
            'badge_class' => 'badge-success',
        ],
        
        // Trạng thái Reservation
        'arrived' => [
            'text' => 'Khách đã đến',
            'text_full' => 'Khách hàng đã nhận bàn',
            'emoji' => '👤',
            'color' => 'info',
            'bg_class' => 'status-arrived',
            'badge_class' => 'badge-info',
        ],
        'preparing' => [
            'text' => 'Đang chuẩn bị',
            'text_full' => 'Nhà hàng đang chuẩn bị phục vụ',
            'emoji' => '🍳',
            'color' => 'warning',
            'bg_class' => 'status-preparing',
            'badge_class' => 'badge-warning',
        ],
        'serving' => [
            'text' => 'Đang phục vụ',
            'text_full' => 'Nhà hàng đang phục vụ khách',
            'emoji' => '👨‍🍳',
            'color' => 'warning',
            'bg_class' => 'status-serving',
            'badge_class' => 'badge-warning',
        ],
        'cleaning' => [
            'text' => 'Đang dọn dẹp',
            'text_full' => 'Bàn đang được dọn dẹp',
            'emoji' => '🧹',
            'color' => 'info',
            'bg_class' => 'status-cleaning',
            'badge_class' => 'badge-info',
        ],
        
        // Trạng thái item trong bếp
        'cooking' => [
            'text' => 'Đang nấu',
            'text_full' => 'Món ăn đang được nấu',
            'emoji' => '🔥',
            'color' => 'warning',
            'bg_class' => 'status-cooking',
            'badge_class' => 'badge-warning',
        ],
        'done' => [
            'text' => 'Đã xong',
            'text_full' => 'Món ăn đã hoàn thành',
            'emoji' => '✓',
            'color' => 'success',
            'bg_class' => 'status-done',
            'badge_class' => 'badge-success',
        ],
    ];

    /**
     * Lấy bản dịch của trạng thái
     * 
     * @param string $status
     * @param string $format 'text' (mặc định), 'text_full', 'emoji', 'color'
     * @return string|array
     */
    public static function translate($status, $format = 'text')
{
    $status = strtolower(trim($status ?? ''));
    
    if (!isset(self::STATUS_TRANSLATIONS[$status])) {
        if ($format === 'all') {
            return [
                'text' => ucfirst($status),
                'text_full' => ucfirst($status),
                'emoji' => '❓',
                'color' => 'secondary',
                'bg_class' => 'status-default',
                'badge_class' => 'badge-secondary',
            ];
        }
        return ucfirst($status);
    }

    if ($format === 'all') {
        return self::STATUS_TRANSLATIONS[$status];
    }

    return self::STATUS_TRANSLATIONS[$status][$format] ?? ucfirst($status);
}

    /**
     * Lấy text và emoji của trạng thái
     */
    public static function getTextWithEmoji($status)
    {
        $data = self::translate($status, 'all');
        return $data['emoji'] . ' ' . $data['text'];
    }

    /**
     * Lấy class CSS cho badge
     */
    public static function getBadgeClass($status)
    {
        $data = self::translate($status, 'all');
        return $data['badge_class'] ?? 'badge-secondary';
    }

    /**
     * Lấy class CSS cho background
     */
    public static function getBgClass($status)
    {
        $data = self::translate($status, 'all');
        return $data['bg_class'] ?? 'status-default';
    }

    /**
     * Kiểm tra xem trạng thái có phải là "chờ" không
     */
    public static function isPending($status)
    {
            return in_array(strtolower(trim($status)), ['pending', 'waiting', 'chờ', 'pending_deposit', 'pending_payment']);

    }

    /**
     * Kiểm tra xem trạng thái có phải là "hoàn tất" không
     */
    public static function isCompleted($status)
    {
        $status = strtolower(trim($status));
        return in_array($status, ['completed', 'done', 'paid', 'paid_cash', 'paid_transfer']);
    }

    /**
     * Kiểm tra xem trạng thái có phải là "đã hủy" không
     */
    public static function isCancelled($status)
    {
        return strtolower(trim($status)) === 'cancelled';
    }

    /**
     * Lấy danh sách tất cả trạng thái (cho dropdown, filter, v.v.)
     */
    public static function getAll()
    {
        return self::STATUS_TRANSLATIONS;
    }

    /**
     * Lấy danh sách trạng thái cho Order
     */
    public static function getOrderStatuses()
    {
        return [
            'pending' => self::translate('pending', 'all'),
            'confirmed' => self::translate('confirmed', 'all'),
            'processing' => self::translate('processing', 'all'),
            'ready' => self::translate('ready', 'all'),
            'served' => self::translate('served', 'all'),
            'completed' => self::translate('completed', 'all'),
            'cancelled' => self::translate('cancelled', 'all'),
        ];
    }

    /**
     * Lấy danh sách trạng thái cho Reservation
     */
    public static function getReservationStatuses()
    {
        return [
            'pending_deposit' => self::translate('pending_deposit', 'all'),            
            'arrived' => self::translate('arrived', 'all'),
            'preparing' => self::translate('preparing', 'all'),
            'serving' => self::translate('serving', 'all'),
            'completed' => self::translate('completed', 'all'),
            'cancelled' => self::translate('cancelled', 'all'),
        ];
    }

    /**
     * Lấy danh sách trạng thái cho item trong bếp
     */
    public static function getChefItemStatuses()
    {
        return [
            'pending' => self::translate('pending', 'all'),
            'cooking' => self::translate('cooking', 'all'),
            'done' => self::translate('done', 'all'),
        ];
    }

    /**
     * Lấy danh sách trạng thái thanh toán
     */
    public static function getPaymentStatuses()
    {
        return [
            'paid_cash' => self::translate('paid_cash', 'all'),
            'paid_transfer' => self::translate('paid_transfer', 'all'),
        ];
    }
}
