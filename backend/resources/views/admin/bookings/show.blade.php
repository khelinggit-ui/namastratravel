@extends('admin.layouts.app')
@section('title', 'Detail Booking')
@section('page', 'Detail Booking #'.$booking->id)

@section('actions')
  <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline">← Kembali</a>
@endsection

@section('content')
  <section class="detail-hero-card">
    <div>
      <span class="eyebrow">Order #{{ $booking->id }}</span>
      <h1>{{ $booking->tour_name ?: 'Booking Travel' }}</h1>
      <p class="detail-subtitle">Dibuat {{ $booking->created_at->format('d M Y, H:i') }} · {{ $booking->customer_name ?: 'Customer' }}</p>
    </div>
    <div class="detail-hero-status">
      <span class="detail-status-label">Status booking</span>
      <span class="badge {{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
    </div>
  </section>

  <div class="detail-layout">
    <main class="detail-main-column">
      <section class="card detail-card">
        <div class="detail-card-heading"><div><span class="detail-kicker">Customer</span><h2>Informasi customer</h2></div><span class="detail-number">01</span></div>
        <div class="detail-info-grid">
          <div class="detail-field"><span>Nama lengkap</span><strong>{{ $booking->customer_name ?: '-' }}</strong></div>
          <div class="detail-field"><span>Email</span><a href="mailto:{{ $booking->email }}">{{ $booking->email }}</a></div>
          <div class="detail-field"><span>WhatsApp</span><a href="https://wa.me/{{ preg_replace('/\D/', '', $booking->whatsapp) }}" target="_blank" rel="noreferrer">{{ $booking->whatsapp }}</a></div>
          <div class="detail-field"><span>Jumlah peserta</span><strong>{{ $booking->pax ?: '-' }}</strong></div>
        </div>
      </section>

      <section class="card detail-card">
        <div class="detail-card-heading"><div><span class="detail-kicker">Perjalanan</span><h2>Detail perjalanan</h2></div><span class="detail-number">02</span></div>
        <div class="detail-info-grid">
          <div class="detail-field detail-field-wide"><span>Paket tour</span><strong>{{ $booking->tour_name ?: '-' }}</strong></div>
          <div class="detail-field"><span>Tanggal rencana</span><strong>{{ $booking->planned_date ?: '-' }}</strong></div>
          <div class="detail-field detail-field-wide"><span>Permintaan customer</span><p>{{ $booking->destination ?: '-' }}</p></div>
        </div>
      </section>

      <section class="card detail-card">
        <div class="detail-card-heading"><div><span class="detail-kicker">Operasional</span><h2>Status booking</h2></div><span class="detail-number">03</span></div>
        <form method="POST" action="{{ route('admin.bookings.update', $booking) }}" class="detail-status-form">
          @csrf @method('PUT')
          <div><span class="detail-field-label">Tahap tindak lanjut</span><p class="muted">Perbarui status komunikasi tim dengan customer.</p></div>
          <select name="status">
            <option value="baru" @selected($booking->status==='baru')>Baru</option>
            <option value="dihubungi" @selected($booking->status==='dihubungi')>Dihubungi</option>
            <option value="selesai" @selected($booking->status==='selesai')>Selesai</option>
          </select>
          <button class="btn btn-primary">Simpan status</button>
        </form>
      </section>
    </main>

    <aside class="detail-side-column">
      <section class="card payment-card">
        <div class="payment-card-top"><div><span class="detail-kicker">CashUP Direct Channel</span><h2>Pembayaran</h2></div><span class="payment-mark">↗</span></div>
        @if ($booking->payment_provider === 'cashup')
          <div class="payment-amount"><span>Total transaksi</span><strong>Rp {{ number_format($booking->payment_amount ?: 0, 0, ',', '.') }}</strong></div>
          <div class="payment-status-row"><span>Status</span><span class="badge payment-{{ $booking->payment_status ?: 'pending' }}">{{ ucfirst($booking->payment_status ?: 'pending') }}</span></div>
          <dl class="payment-details">
            <div><dt>Order ID</dt><dd><code>{{ $booking->payment_order_id ?: '-' }}</code></dd></div>
            <div><dt>Invoice</dt><dd>{{ $booking->payment_invoice_number ?: '-' }}</dd></div>
            <div><dt>Dibayar</dt><dd>{{ $booking->paid_at?->format('d M Y, H:i') ?: 'Belum dibayar' }}</dd></div>
          </dl>
          <div class="payment-actions">
            @if ($booking->payment_url && $booking->payment_status !== 'paid')
              <a href="{{ $booking->payment_url }}" class="btn btn-outline" target="_blank" rel="noreferrer">Buka payment link</a>
            @endif
            <form method="POST" action="{{ route('admin.bookings.sync-payment', $booking) }}">
              @csrf
              <button class="btn btn-primary">Sinkronkan status</button>
            </form>
          </div>
        @else
          <p class="muted detail-empty-payment">Booking ini merupakan inquiry tanpa transaksi CashUP.</p>
        @endif
      </section>
      <section class="detail-note"><span class="status-dot"></span><div><strong>Verifikasi server-side</strong><p>Status payment disinkronkan langsung dari CashUP, bukan dari URL redirect customer.</p></div></section>
    </aside>
  </div>
@endsection
