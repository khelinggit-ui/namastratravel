<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\Destination;
use App\Models\Post;
use App\Models\Tour;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'bookings_total' => Booking::count(),
            'bookings_baru' => Booking::where('status', 'baru')->count(),
            'tours_count' => Tour::count(),
            'tours_published' => Tour::where('status', 'published')->count(),
            'destinations_count' => Destination::count(),
            'posts_count' => Post::where('status', 'published')->count(),
            'contacts_baru' => Contact::where('status', 'baru')->count(),
            'recent_bookings' => Booking::orderByDesc('created_at')->limit(8)->get(),
        ]);
    }
}