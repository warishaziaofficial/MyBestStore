<?php

namespace App\Mail;

use App\Models\Order;
use Cms\Models\Order as CmsOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order|CmsOrder $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your MyBestStore Order Invoice - '.$this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-invoice',
        );
    }
}
