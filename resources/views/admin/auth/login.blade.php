<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login — Admin Namastra</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <style>{!! file_get_contents(public_path('css/admin.css')) !!}</style>
</head>
<body>
  <div class="login-wrap">
    <div class="login-card">
      <div class="brand"><img src="{{ asset('namastratravel.png') }}" alt="Namastra Travel" class="brand-img"></div>
      @if ($errors->any())
        <div class="alert error"><ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif
      <form method="POST" action="{{ route('admin.login') }}">
        @csrf
        <div class="field">
          <label>Email</label>
          <input type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="field">
          <label>Password</label>
          <input type="password" name="password" required>
        </div>
        <button class="btn btn-primary" style="width:100%;justify-content:center">Masuk</button>
      </form>
    </div>
  </div>
</body>
</html>