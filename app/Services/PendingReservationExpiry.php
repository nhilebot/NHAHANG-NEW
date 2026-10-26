<?php

namespace App\Services;

use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class PendingReservationExpiry
{
    public const PAYMENT_WINDOW_MINUTES = 30;

    public function expire(): int
    {
        $cutoff = now()->subMinutes(self::PAYMENT_WINDOW_MINUTES);
        $ids = Reservation::query()
            ->where('status', 'pending_deposit')
            ->where('payment_status', 'unpaid')
            ->where('created_at', '<=', $cutoff)
            ->orderBy('id')
            ->pluck('id');

        $expired = 0;
        foreach ($ids as $id) {
            $didExpire = DB::transaction(function () use ($id, $cutoff) {
                $reservation = Reservation::whereKey($id)->lockForUpdate()->first();
                if (
                    !$reservation
                    || $reservation->status !== 'pending_deposit'
                    || $reservation->payment_status !== 'unpaid'
                    || !$reservation->created_at
                    || $reservation->created_at->gt($cutoff)
                ) {
                    return false;
                }

                $reservation->status = 'cancelled';
                $reservation->cancellation_status = 'expired';
                $reservation->cancellation_reason = 'Hết thời hạn thanh toán tiền cọc.';
                $reservation->cancellation_reviewed_at = now();
                $reservation->save();

                return true;
            });

            $expired += (int) $didExpire;
        }

        return $expired;
    }
}
