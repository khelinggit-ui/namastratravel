<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Tour;
use App\Models\User;
use App\Notifications\NewBookingNotification;
use App\Notifications\BookingPaymentNotification;
use App\Services\CashupService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\ValidationException;
use RuntimeException;

class CashupPaymentController extends Controller
{
    public function __construct(private readonly CashupService $cashup)
    {
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:255',
            'tour_slug' => 'required|string|max:255',
            'schedule_start_date' => 'nullable|string|max:100',
            'whatsapp' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'destination' => 'required|string|max:2000',
            'pax' => 'nullable|string|max:50',
            'date' => 'nullable|string|max:100',
            'password' => 'required|string|min:8|same:password_confirmation',
            'password_confirmation' => 'required|string',
        ]);

        $tour = Tour::where('slug', $data['tour_slug'])
            ->where('status', 'published')
            ->first();

        if (!$tour) {
            return response()->json(['message' => 'Paket tour tidak ditemukan atau tidak tersedia.'], 404);
        }

        $pricePerParticipant = $this->resolveAmount($tour, $data['schedule_start_date'] ?? null);
        $participantCount = $this->participantCount($data['pax'] ?? null);
        $totalBookingAmount = $pricePerParticipant * $participantCount;
        $user = $this->customerUser($data);
        if ($totalBookingAmount < 1001) {
            return response()->json(['message' => 'Harga pembayaran harus lebih besar dari Rp1.000.'], 422);
        }

        $booking = Booking::create([
            'user_id' => $user->id,
            'customer_name' => $data['customer_name'],
            'tour_name' => $tour->title,
            'whatsapp' => $data['whatsapp'],
            'email' => $data['email'],
            'destination' => $data['destination'],
            'pax' => $data['pax'] ?? null,
            'planned_date' => $data['date'] ?? null,
            'status' => 'baru',
            'payment_status' => 'pending',
            'payment_provider' => 'cashup',
            'payment_amount' => $totalBookingAmount,
        ]);

        try {
            $response = $this->cashup->generateLink([
                'base_amount' => $totalBookingAmount,
                'customer_name' => $data['customer_name'],
                'phone_no' => $data['whatsapp'],
                'reference_id' => 'NAMASTRA-' . $booking->id,
            ]);

            $payment = $response['data'] ?? [];
            if (($response['status'] ?? null) !== 'SUCCESS' || empty($payment['generated_link']) || empty($payment['order_id'])) {
                throw new RuntimeException($response['message'] ?? 'CashUP gagal membuat payment link.');
            }

            $booking->update([
                'payment_order_id' => $payment['order_id'],
                'payment_invoice_number' => $this->invoiceNumber($response),
                'payment_url' => $payment['generated_link'],
            ]);

            try {
                Notification::route('mail', $booking->email)
                    ->notify(new BookingPaymentNotification($booking->fresh(), 'created'));
                $user->notify(new \App\Notifications\CustomerBookingNotification($booking->fresh()));
            } catch (\Throwable $mailException) {
                report($mailException);
            }

            try {
                User::where('is_admin', true)->get()->each->notify(new NewBookingNotification($booking->fresh()));
            } catch (\Throwable $mailException) {
                report($mailException);
            }
        } catch (\Throwable $exception) {
            $booking->update(['payment_status' => 'failed']);
            report($exception);

            return response()->json([
                'message' => 'Payment link belum dapat dibuat. Silakan coba lagi.',
            ], 502);
        }

        return response()->json([
            'message' => 'Payment link berhasil dibuat.',
            'booking_id' => $booking->id,
            'payment_url' => $booking->payment_url,
            'order_id' => $booking->payment_order_id,
            'amount' => $booking->payment_amount,
        ], 201);
    }

    public function status(Booking $booking)
    {
        if ($booking->payment_provider !== 'cashup' || !$booking->payment_order_id) {
            return response()->json(['message' => 'Booking belum memiliki transaksi CashUP.'], 404);
        }

        return response()->json($this->syncStatus($booking));
    }

    public function statusByOrder(string $orderId)
    {
        $booking = Booking::where('payment_provider', 'cashup')
            ->where('payment_order_id', $orderId)
            ->firstOrFail();

        return response()->json($this->syncStatus($booking));
    }

    public function callback(Request $request)
    {
        $orderId = $request->input('orderId') ?: $request->input('order_id');
        if (!$orderId) {
            return response()->json(['message' => 'order_id wajib diisi.'], 422);
        }

        $booking = Booking::where('payment_provider', 'cashup')
            ->where('payment_order_id', $orderId)
            ->firstOrFail();

        return response()->json($this->syncStatus($booking));
    }

    private function syncStatus(Booking $booking): array
    {
        $previousStatus = $booking->payment_status;
        $cashup = $this->cashup->checkStatus($booking->payment_order_id);
        $invoiceNumber = $booking->payment_invoice_number ?: $this->invoiceNumber($cashup);
        $cashupStatus = strtoupper((string) data_get($cashup, 'data.payment_link_status', ''));
        $paymentStatus = match ($cashupStatus) {
            'PAID', 'SUCCESS', 'COMPLETED' => 'paid',
            'FAILED', 'CANCELLED', 'EXPIRED' => strtolower($cashupStatus),
            default => 'pending',
        };

        if ($booking->payment_status === 'paid') {
            $paymentStatus = 'paid';
        }

        $booking->update([
            'payment_status' => $paymentStatus,
            'payment_invoice_number' => $invoiceNumber ?: $booking->payment_invoice_number,
            'paid_at' => $paymentStatus === 'paid' ? ($booking->paid_at ?: Carbon::now()) : $booking->paid_at,
        ]);

        if ($paymentStatus === 'paid' && $previousStatus !== 'paid') {
            try {
                Notification::route('mail', $booking->email)
                    ->notify(new BookingPaymentNotification($booking->fresh(), 'paid'));
            } catch (\Throwable $mailException) {
                report($mailException);
            }
        }

        return [
            'booking_id' => $booking->id,
            'order_id' => $booking->payment_order_id,
            'invoice_number' => $invoiceNumber,
            'payment_method' => data_get($cashup, 'data.trx_type') ?: data_get($cashup, 'data.rx_type'),
            'payment_issuer' => data_get($cashup, 'data.issuer'),
            'payment_status' => $booking->payment_status,
            'amount' => $booking->payment_amount,
            'customer_name' => $booking->customer_name,
            'email' => $booking->email,
            'whatsapp' => $booking->whatsapp,
            'tour_name' => $booking->tour_name,
            'destination' => $booking->destination,
            'pax' => $booking->pax,
            'planned_date' => $booking->planned_date,
            'paid_at' => $booking->paid_at?->toIso8601String(),
            'message' => $cashup['message'] ?? null,
            'cashup' => $cashup,
        ];
    }

    private function resolveAmount(Tour $tour, ?string $scheduleStartDate): int
    {
        if (!$scheduleStartDate || !is_array($tour->departure_schedules)) {
            return (int) $tour->price_start;
        }

        foreach ($tour->departure_schedules as $schedule) {
            $startDate = $schedule['startDate'] ?? $schedule['start_date'] ?? null;
            if ($startDate === $scheduleStartDate && ($schedule['status'] ?? null) !== 'full') {
                return (int) ($schedule['price'] ?? $tour->price_start);
            }
        }

        return (int) $tour->price_start;
    }

    private function participantCount(?string $pax): int
    {
        preg_match('/\d+/', (string) $pax, $matches);
        return max(1, (int) ($matches[0] ?? 1));
    }

    private function customerUser(array $data): User
    {
        $user = User::where('email', $data['email'])->first();
        if ($user?->is_admin) {
            throw ValidationException::withMessages(['email' => 'Email sudah terdaftar sebagai admin.']);
        }

        return $user ?: User::create([
            'name' => $data['customer_name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_admin' => false,
        ]);
    }

    private function invoiceNumber(array $response): ?string
    {
        foreach ([
            data_get($response, 'data.invoice_num'),
            data_get($response, 'data.invoice_number'),
            data_get($response, 'invoice_num'),
            data_get($response, 'invoice_number'),
        ] as $value) {
            if ($value !== null && $value !== '') {
                return (string) $value;
            }
        }

        return null;
    }
}
