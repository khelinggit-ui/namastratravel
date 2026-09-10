<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerBookingNotification extends Notification
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
        $loginUrl = rtrim(config('app.frontend_url'), '/').'/login';

        return (new MailMessage)
            ->subject('Booking Namastra Travel berhasil dibuat')
            ->greeting('Booking Anda berhasil dibuat')
            ->line('Halo '.($this->booking->customer_name ?: 'Customer').', data booking Anda telah kami terima.')
            ->line('Tour: '.($this->booking->tour_name ?: '-'))
            ->line('Jumlah Peserta: '.($this->booking->pax ?: '-'))
            ->line('Tanggal Rencana: '.($this->booking->planned_date ?: '-'))
            ->action('Login untuk melihat booking', $loginUrl)
            ->line('Gunakan email dan password yang Anda buat saat booking untuk masuk ke halaman riwayat booking.');
    }
}
