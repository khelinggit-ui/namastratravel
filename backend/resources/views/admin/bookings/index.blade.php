@extends('admin.layouts.app')
@section('title', 'Booking & Payment')
@section('page', 'Booking & Payment')
@section('actions')
  <a href="{{ route('admin.bookings.export') }}" class="btn btn-outline">Export CSV</a>
@endsection

@section('content')
  <section class="booking-header">
    <div>
      <span class="eyebrow">CashUP Direct Channel</span>
      <h1>Booking &amp; Payment</h1>
      <p>Kelola pesanan customer dan pantau status transaksi CashUP dari satu tempat.</p>
    </div>
    <div class="booking-header-note"><span class="status-dot"></span> Sinkronisasi manual tersedia</div>
  </section>

  <section class="stats booking-stats">
    <div class="stat booking-stat"><span>Total Booking</span><strong>{{ number_format($metrics['total']) }}</strong><small>Semua pesanan masuk</small></div>
    <div class="stat booking-stat"><span>Menunggu Pembayaran</span><strong>{{ number_format($metrics['pending']) }}</strong><small>Perlu dipantau</small></div>
    <div class="stat booking-stat"><span>Berhasil Dibayar</span><strong>{{ number_format($metrics['paid']) }}</strong><small>Transaksi terkonfirmasi</small></div>
    <div class="stat booking-stat booking-stat-money"><span>Total Dana Masuk</span><strong>Rp {{ number_format($metrics['paid_amount'], 0, ',', '.') }}</strong><small>Dari transaksi paid</small></div>
  </section>

  <div class="booking-toolbar card">
    <div>
      <h2>Daftar Transaksi</h2>
      <p class="muted">Gunakan filter untuk menemukan booking berdasarkan status dan payment.</p>
    </div>
    <form method="GET" class="booking-filters">
      <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari customer, email, order ID...">
      <select name="payment_status">
        <option value="">Semua payment</option>
        <option value="pending" @selected(request('payment_status')==='pending')>Pending</option>
        <option value="paid" @selected(request('payment_status')==='paid')>Paid</option>
        <option value="failed" @selected(request('payment_status')==='failed')>Failed</option>
        <option value="expired" @selected(request('payment_status')==='expired')>Expired</option>
        <option value="cancelled" @selected(request('payment_status')==='cancelled')>Cancelled</option>
      </select>
      <select name="status">
        <option value="">Semua booking</option>
        <option value="baru" @selected(request('status')==='baru')>Baru</option>
        <option value="dihubungi" @selected(request('status')==='dihubungi')>Dihubungi</option>
        <option value="selesai" @selected(request('status')==='selesai')>Selesai</option>
      </select>
      <button class="btn btn-primary">Terapkan</button>
    </form>
  </div>

  <div class="table-wrap booking-table-wrap">
    <table class="booking-table">
      <thead><tr><th>Customer</th><th>Paket Tour</th><th>Nominal</th><th>Payment</th><th>Booking</th><th>Masuk</th><th></th></tr></thead>
      <tbody>
        @forelse ($bookings as $b)
          <tr>
            <td>
              <strong>{{ $b->customer_name ?: 'Customer' }}</strong>
              <a class="booking-email" href="mailto:{{ $b->email }}">{{ $b->email }}</a>
              <a class="booking-phone" href="https://wa.me/{{ preg_replace('/\D/', '', $b->whatsapp) }}" target="_blank" rel="noreferrer">{{ $b->whatsapp }}</a>
            </td>
            <td><span class="booking-tour">{{ $b->tour_name ?: '-' }}</span><span class="booking-destination">{{ \Illuminate\Support\Str::limit($b->destination, 42) }}</span></td>
            <td class="booking-amount">{{ $b->payment_amount ? 'Rp '.number_format($b->payment_amount, 0, ',', '.') : '-' }}</td>
            <td>
              @if ($b->payment_status)
                <span class="badge payment-{{ $b->payment_status }}">{{ ucfirst($b->payment_status) }}</span>
                @if ($b->payment_order_id)<span class="booking-order">{{ \Illuminate\Support\Str::limit($b->payment_order_id, 18) }}</span>@endif
              @else
                <span class="muted">Tidak ada payment</span>
              @endif
            </td>
            <td><span class="badge {{ $b->status }}">{{ ucfirst($b->status) }}</span></td>
            <td class="muted">{{ $b->created_at->format('d M Y H:i') }}</td>
            <td class="booking-actions"><a href="{{ route('admin.bookings.show', $b) }}" class="btn btn-outline btn-sm">Lihat</a></td>
          </tr>
        @empty
          <tr><td colspan="7" class="booking-empty"><strong>Belum ada booking</strong><span>Transaksi baru akan muncul di sini.</span></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="booking-pagination">{!! $bookings->links('vendor.pagination.admin') !!}</div>
@endsection
