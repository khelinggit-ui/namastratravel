<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\CashupService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::query();
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }
        if ($request->filled('q')) {
            $q = '%'.$request->input('q').'%';
            $query->where(fn ($w) => $w->where('customer_name', 'like', $q)
                ->orWhere('whatsapp', 'like', $q)
                ->orWhere('email', 'like', $q)
                ->orWhere('payment_order_id', 'like', $q)
                ->orWhere('destination', 'like', $q));
        }
        return view('admin.bookings.index', [
            'bookings' => $query->latest()->paginate(15)->withQueryString(),
            'metrics' => [
                'total' => Booking::count(),
                'pending' => Booking::where('payment_status', 'pending')->count(),
                'paid' => Booking::where('payment_status', 'paid')->count(),
                'paid_amount' => (int) Booking::where('payment_status', 'paid')->sum('payment_amount'),
            ],
        ]);
    }

    public function show(Booking $booking)
    {
        return view('admin.bookings.show', ['booking' => $booking]);
    }

    public function update(Request $request, Booking $booking)
    {
        $data = $request->validate(['status' => 'required|in:baru,dihubungi,selesai']);
        $booking->update($data);

        if ($request->has('from') && $request->input('from') === 'index') {
            return redirect()->route('admin.bookings.index')->with('success', 'Status booking diperbarui.');
        }
        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Status booking diperbarui.');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Booking dihapus.');
    }

    public function syncPayment(Booking $booking, CashupService $cashup)
    {
        if ($booking->payment_provider !== 'cashup' || !$booking->payment_order_id) {
            return redirect()->route('admin.bookings.show', $booking)
                ->with('error', 'Booking ini belum memiliki transaksi CashUP.');
        }

        try {
            $response = $cashup->checkStatus($booking->payment_order_id);
            $cashupStatus = strtoupper((string) data_get($response, 'data.payment_link_status', ''));
            $invoiceNumber = $booking->payment_invoice_number
                ?: data_get($response, 'data.invoice_num')
                ?: data_get($response, 'data.invoice_number')
                ?: data_get($response, 'invoice_num')
                ?: data_get($response, 'invoice_number');
            $paymentStatus = match ($cashupStatus) {
                'PAID', 'SUCCESS', 'COMPLETED' => 'paid',
                'FAILED' => 'failed',
                'EXPIRED' => 'expired',
                'CANCELLED' => 'cancelled',
                default => 'pending',
            };

            $booking->update([
                'payment_status' => $paymentStatus,
                'payment_invoice_number' => $invoiceNumber,
                'paid_at' => $paymentStatus === 'paid' ? ($booking->paid_at ?: Carbon::now()) : $booking->paid_at,
            ]);

            return redirect()->route('admin.bookings.show', $booking)
                ->with('success', 'Status pembayaran disinkronkan dari CashUP: '.strtoupper($paymentStatus).'.');
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->route('admin.bookings.show', $booking)
                ->with('error', 'Status CashUP belum dapat disinkronkan. Periksa konfigurasi dan koneksi CashUP.');
        }
    }

    public function export()
    {
        $bookings = Booking::latest()->get();
        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, ['ID', 'Nama', 'Tour', 'WhatsApp', 'Email', 'Detail Tujuan', 'Peserta', 'Tanggal', 'Status Booking', 'Status Payment', 'Nominal', 'Order CashUP', 'Masuk']);
        foreach ($bookings as $b) {
            fputcsv($csv, [$b->id, $b->customer_name, $b->tour_name, $b->whatsapp, $b->email, $b->destination, $b->pax, $b->planned_date, $b->status, $b->payment_status ?: '-', $b->payment_amount ?: 0, $b->payment_order_id ?: '-', $b->created_at->format('Y-m-d H:i')]);
        }
        rewind($csv);

        return response(stream_get_contents($csv), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings-'.now()->format('Ymd-His').'.csv"',
        ]);
    }
}
