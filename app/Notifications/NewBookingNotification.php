<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBookingNotification extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Baru: '.($this->booking->tour_name ?? 'Inquiry'))
            ->greeting('Ada booking baru masuk')
            ->line('Tour: '.($this->booking->tour_name ?: '-'))
            ->line('WhatsApp: '.$this->booking->whatsapp)
            ->line('Email: '.$this->booking->email)
            ->line('Detail Tujuan: '.$this->booking->destination)
            ->line('Jumlah Peserta: '.($this->booking->pax ?: '-'))
            ->line('Tanggal Rencana: '.($this->booking->planned_date ?: '-'))
            ->line('Status Payment: '.($this->booking->payment_status ?: 'Inquiry'))
            ->line('Nominal: Rp '.number_format($this->booking->payment_amount ?: 0, 0, ',', '.'))
            ->line('Order CashUP: '.($this->booking->payment_order_id ?: '-'))
            ->action('Buka Admin CMS', url('/admin'))
            ->line('Segera tindak lanjuti booking ini.');
    }
}
