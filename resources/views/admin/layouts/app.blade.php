<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Admin') — Namastra Travel</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
  <style>{!! file_get_contents(public_path('css/admin.css')) !!}</style>
</head>
<body>
  <aside class="sidebar">
    <a href="{{ route('admin') }}" class="brand">
      <img src="{{ asset('namastratravel.png') }}" alt="Namastra Travel" class="brand-img">
    </a>
    <button class="mobile-menu-toggle" type="button" aria-expanded="false" aria-controls="admin-menu" aria-label="Buka menu navigasi">
      <span></span><span></span><span></span>
    </button>
    <nav class="menu" id="admin-menu">
      <a href="{{ route('admin') }}" class="{{ request()->routeIs('admin') ? 'active' : '' }}">Dashboard</a>
      <a href="{{ route('admin.tours.index') }}" class="{{ request()->routeIs('admin.tours.*') ? 'active' : '' }}">Tour</a>
      <a href="{{ route('admin.destinations.index') }}" class="{{ request()->routeIs('admin.destinations.*') ? 'active' : '' }}">Destinasi</a>
      <a href="{{ route('admin.posts.index') }}" class="{{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">Blog / Artikel</a>
      <a href="{{ route('admin.testimonials.index') }}" class="{{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">Testimoni</a>
      <a href="{{ route('admin.bookings.index') }}" class="{{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">Booking <span class="pill">{{ \App\Models\Booking::where('status','baru')->count() }}</span></a>
      <a href="{{ route('admin.contacts.index') }}" class="{{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">Kontak Masuk</a>
      <a href="{{ route('admin.about') }}" class="{{ request()->routeIs('admin.about') ? 'active' : '' }}">Tentang Kami</a>
      <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}">Pengaturan</a>
      <span class="menu-section">Master</span>
      <a href="{{ route('admin.master.banners.index') }}" class="{{ request()->routeIs('admin.master.banners.*') ? 'active' : '' }}">Banner Hero</a>
      <a href="{{ route('admin.master.users.index') }}" class="{{ request()->routeIs('admin.master.users.*') ? 'active' : '' }}">Management User</a>
    </nav>
    <div class="sidebar-foot">
      <span>{{ auth()->user()->email }}</span>
      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button class="btn-ghost">Logout</button>
      </form>
    </div>
  </aside>
  <button class="sidebar-overlay" type="button" aria-label="Tutup menu navigasi"></button>
  <div class="main">
    <header class="topbar">
      <div class="page-title">@yield('page', 'Admin')</div>
      <div class="top-actions">@yield('actions', '')</div>
    </header>
    @if (session('success'))
      <div class="alert success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="alert error">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
      <div class="alert error">
        <ul>
          @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
      </div>
    @endif
    <main class="content">
      @yield('content')
    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
  <script>
    (() => {
      const sidebar = document.querySelector('.sidebar')
      const toggle = document.querySelector('.mobile-menu-toggle')
      const overlay = document.querySelector('.sidebar-overlay')
      const menuLinks = document.querySelectorAll('.menu a')

      if (!sidebar || !toggle || !overlay) return

      const setMenuState = (isOpen) => {
        sidebar.classList.toggle('is-open', isOpen)
        overlay.classList.toggle('is-visible', isOpen)
        toggle.setAttribute('aria-expanded', String(isOpen))
        toggle.setAttribute('aria-label', isOpen ? 'Tutup menu navigasi' : 'Buka menu navigasi')
      }

      toggle.addEventListener('click', () => setMenuState(!sidebar.classList.contains('is-open')))
      overlay.addEventListener('click', () => setMenuState(false))
      menuLinks.forEach((link) => link.addEventListener('click', () => setMenuState(false)))
    })()
  </script>
  @stack('scripts')
</body>
</html>
