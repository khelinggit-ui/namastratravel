<?php

namespace App\Http\Controllers\Admin;

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
        return view('admin.dashboard', [
            'bookingsBaru' => Booking::where('status', 'baru')->count(),
            'bookingsTotal' => Booking::count(),
            'tours' => Tour::count(),
            'toursPublished' => Tour::where('status', 'published')->count(),
            'destinations' => Destination::count(),
            'posts' => Post::where('status', 'published')->count(),
            'contactsBaru' => Contact::where('status', 'baru')->count(),
            'recentBookings' => Booking::latest()->limit(8)->get(),
        ]);
    }
}