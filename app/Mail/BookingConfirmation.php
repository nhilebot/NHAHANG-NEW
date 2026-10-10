<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $booking; // Khai báo biến để dùng trong giao diện email

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

   public function build()
{
    return $this->subject('Xác nhận đặt bàn - Aurora Garden')
                ->view('emails.booking_confirmation')
                ->with([
                    'booking' => $this->booking,
                ]);
}
}