@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('page', 'Dashboard')

@section('content')
  <div class="stats">
    <div class="stat"><strong>{{ $bookingsBaru }}</strong><span>Booking Baru</span></div>
    <div class="stat"><strong>{{ $bookingsTotal }}</strong><span>Total Booking</span></div>
    <div class="stat"><strong>{{ $toursPublished }}</strong><span>Tour Terbit</span></div>
    <div class="stat"><strong>{{ $tours }}</strong><span>Total Tour</span></div>
    <div class="stat"><strong>{{ $destinations }}</strong><span>Destinasi</span></div>
    <div class="stat"><strong>{{ $posts }}</strong><span>Artikel</span></div>
    <div class="stat"><strong>{{ $contactsBaru }}</strong><span>Kontak Baru</span></div>
  </div>

  <div class="card">
    <div class="card-title">Booking Terbaru</div>
    @if ($recentBookings->count())
      <div class="table-wrap" style="border:0">
        <table>
          <thead><tr><th>Tour</th><th>WhatsApp</th><th>Email</th><th>Detail</th><th>Status</th><th>Masuk</th></tr></thead>
          <tbody>
            @foreach ($recentBookings as $b)
              <tr>
                <td>{{ $b->tour_name ?: '-' }}</td>
                <td>{{ $b->whatsapp }}</td>
                <td>{{ $b->email }}</td>
                <td class="muted" style="max-width:240px">{{ \Illuminate\Support\Str::limit($b->destination, 60) }}</td>
                <td><span class="badge {{ $b->status }}">{{ $b->status }}</span></td>
                <td class="muted">{{ $b->created_at->diffForHumans() }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <p class="muted">Belum ada booking masuk.</p>
    @endif
  </div>
@endsection