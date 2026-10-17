<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public Reservation $booking;

    public function __construct(Reservation $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->subject('Xác nhận đặt bàn - Aurora Garden')
            ->view('emails.booking_confirmation')
            ->with(['booking' => $this->booking]);
    }
}
