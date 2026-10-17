<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    // Khai báo biến public để file giao diện (view) bên ngoài có thể đọc được dữ liệu
    public $mailData;

    /**
     * Khởi tạo và truyền dữ liệu hóa đơn vào lớp Mail
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Cấu hình tiêu đề Mail xuất hiện trong hộp thư Gmail
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thông tin đơn hàng - Aurora Garden',
        );
    }

    /**
     * Trỏ đường dẫn đến file giao diện HTML của Email
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order_confirmed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
