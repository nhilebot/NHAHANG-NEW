<?php

namespace App\Console\Commands;

use App\Services\PendingReservationExpiry;
use Illuminate\Console\Command;

class ExpirePendingReservations extends Command
{
    protected $signature = 'reservations:expire-pending-deposits';

    protected $description = 'Hủy các yêu cầu đặt bàn quá hạn thanh toán cọc';

    public function handle(PendingReservationExpiry $expiry): int
    {
        $expired = $expiry->expire();
        $this->info("Đã hết hạn {$expired} yêu cầu đặt bàn.");

        return self::SUCCESS;
    }
}
