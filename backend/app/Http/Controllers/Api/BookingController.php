<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Notifications\NewBookingNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\ValidationException;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'tour' => 'nullable|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'destination' => 'required|string|max:2000',
            'pax' => 'nullable|string|max:50',
            'date' => 'nullable|string|max:50',
            'password' => 'required|string|min:8|same:password_confirmation',
            'password_confirmation' => 'required|string',
        ]);

        $user = $this->customerUser($data);

        $booking = Booking::create([
            'user_id' => $user->id,
            'customer_name' => $data['customer_name'] ?? null,
            'tour_name' => $data['tour'] ?? null,
            'whatsapp' => $data['whatsapp'],
            'email' => $data['email'],
            'destination' => $data['destination'],
            'pax' => $data['pax'] ?? null,
            'planned_date' => $data['date'] ?? null,
            'status' => 'baru',
        ]);

        // Notifikasi ke admin (email log + dashboard ter-update via query DB).
        try {
            User::where('is_admin', true)->get()->each->notify(new NewBookingNotification($booking));
            $user->notify(new \App\Notifications\CustomerBookingNotification($booking));
        } catch (\Throwable $e) {
            // jangan gagalkan request karena email gagal
            report($e);
        }

        return response()->json([
            'message' => 'Terima kasih! Tim kami akan menghubungi Anda segera.',
            'booking' => $booking,
        ], 201);
    }

    private function customerUser(array $data): User
    {
        $user = User::where('email', $data['email'])->first();
        if ($user?->is_admin) {
            throw ValidationException::withMessages(['email' => 'Email sudah terdaftar sebagai admin.']);
        }

        return $user ?: User::create([
            'name' => $data['customer_name'] ?? $data['email'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_admin' => false,
        ]);
    }

    // ---- Admin ----

    public function index(Request $request)
    {
        $query = Booking::query();
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('q')) {
            $q = strtolower($request->input('q'));
            $query->where(function ($w) use ($q) {
                $w->whereRaw('LOWER(whatsapp) like ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(email) like ?', ["%{$q}%"])
                    ->orWhereRaw('LOWER(destination) like ?', ["%{$q}%"]);
            });
        }
        return response()->json($query->orderByDesc('created_at')->paginate($request->input('per_page', 20)));
    }

    public function show(Booking $booking)
    {
        return response()->json($booking);
    }

    public function update(Request $request, Booking $booking)
    {
        $data = $request->validate(['status' => 'required|in:baru,dihubungi,selesai']);
        $booking->update($data);
        return response()->json($booking);
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return response()->json(['message' => 'Booking dihapus.'], 200);
    }

    public function export()
    {
        $bookings = Booking::orderByDesc('created_at')->get();

        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, ['ID', 'Tour', 'WhatsApp', 'Email', 'Detail Tujuan', 'Peserta', 'Tanggal', 'Status', 'Masuk']);
        foreach ($bookings as $b) {
            fputcsv($csv, [
                $b->id, $b->tour_name, $b->whatsapp, $b->email, $b->destination,
                $b->pax, $b->planned_date, $b->status, $b->created_at->format('Y-m-d H:i'),
            ]);
        }
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings-'.now()->format('Ymd-His').'.csv"',
        ]);
    }
}
