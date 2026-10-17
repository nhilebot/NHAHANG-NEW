<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingPendingDeposit extends Mailable
{
    use Queueable, SerializesModels;

    public Reservation $booking;

    public function __construct(Reservation $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->subject('Đã tiếp nhận yêu cầu đặt bàn - Aurora Garden')
            ->view('emails.booking_pending_deposit')
            ->with(['booking' => $this->booking]);
    }
}
