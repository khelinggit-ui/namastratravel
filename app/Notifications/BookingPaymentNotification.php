<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingPaymentNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Booking $booking,
        public string $event = 'created',
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->event === 'paid') {
            return (new MailMessage)
                ->subject('Pembayaran berhasil - '.$this->booking->tour_name)
                ->greeting('Pembayaran berhasil dikonfirmasi')
                ->line('Halo '.($this->booking->customer_name ?: 'Customer').', pembayaran Anda telah dikonfirmasi.')
                ->line('Tour: '.($this->booking->tour_name ?: '-'))
                ->line('Nominal: Rp '.number_format($this->booking->payment_amount ?: 0, 0, ',', '.'))
                ->line('Order ID: '.$this->booking->payment_order_id)
                ->line('Tim Namastra Travel akan menghubungi Anda untuk detail perjalanan.')
                ->attachData(
                    $this->invoiceHtml(),
                    'invoice-'.($this->booking->payment_invoice_number ?: $this->booking->payment_order_id ?: $this->booking->id).'.html',
                    ['mime' => 'text/html']
                );
        }

        return (new MailMessage)
            ->subject('Payment link Namastra Travel - '.$this->booking->tour_name)
            ->greeting('Booking Anda berhasil dibuat')
            ->line('Halo '.($this->booking->customer_name ?: 'Customer').', silakan lanjutkan pembayaran melalui link berikut.')
            ->line('Tour: '.($this->booking->tour_name ?: '-'))
            ->line('Nominal: Rp '.number_format($this->booking->payment_amount ?: 0, 0, ',', '.'))
            ->action('Lanjutkan Pembayaran', $this->booking->payment_url)
            ->line('Order ID: '.$this->booking->payment_order_id)
            ->line('Link pembayaran diproses melalui CashUP.');
    }

    private function invoiceHtml(): string
    {
        $booking = $this->booking;
        $invoiceNumber = $booking->payment_invoice_number ?: $booking->payment_order_id ?: 'BOOKING-'.$booking->id;
        $value = static fn ($value): string => htmlspecialchars((string) ($value ?: '-'), ENT_QUOTES, 'UTF-8');
        $amount = 'Rp '.number_format($booking->payment_amount ?: 0, 0, ',', '.');

        return '<!doctype html><html lang="id"><head><meta charset="utf-8"><title>Invoice '.$value($invoiceNumber).'</title>'
            .'<style>body{font-family:Arial,sans-serif;color:#17221b;max-width:760px;margin:40px auto;padding:0 24px}header{border-bottom:2px solid #1fae64;padding-bottom:20px}h1{margin:8px 0;font-size:28px}p{color:#536158}.meta{display:grid;grid-template-columns:1fr 1fr;gap:16px;background:#f4f7f4;padding:20px;margin:24px 0}.label{display:block;color:#68746d;font-size:12px;margin-bottom:5px}.value{font-weight:600}.total{display:flex;justify-content:space-between;border-top:1px solid #17221b;padding-top:18px;font-size:20px;font-weight:700}</style></head><body>'
            .'<header><strong>NAMASTRA TRAVEL</strong><h1>Invoice Pembayaran</h1><p>Pembayaran berhasil dikonfirmasi.</p></header>'
            .'<div class="meta">'
            .'<div><span class="label">Nomor invoice</span><span class="value">'.$value($invoiceNumber).'</span></div>'
            .'<div><span class="label">Order ID</span><span class="value">'.$value($booking->payment_order_id).'</span></div>'
            .'<div><span class="label">Status pembayaran</span><span class="value">Lunas</span></div>'
            .'<div><span class="label">Nama customer</span><span class="value">'.$value($booking->customer_name).'</span></div>'
            .'<div><span class="label">Tour</span><span class="value">'.$value($booking->tour_name).'</span></div>'
            .'<div><span class="label">Jumlah peserta</span><span class="value">'.$value($booking->pax).'</span></div>'
            .'<div><span class="label">Tanggal rencana</span><span class="value">'.$value($booking->planned_date).'</span></div>'
            .'<div><span class="label">Tanggal pembayaran</span><span class="value">'.$value($booking->paid_at?->format('d M Y H:i')).'</span></div>'
            .'</div><div class="total"><span>Total pembayaran</span><span>'.$value($amount).'</span></div></body></html>';
    }
}
