@extends('admin.layouts.app')
@section('title', $user->exists ? 'Edit Akun Admin' : 'Tambah Akun Admin')
@section('page', 'Master / Management User')

@section('actions')
  <a href="{{ route('admin.master.users.index') }}" class="btn btn-outline">Kembali</a>
@endsection

@section('content')
  <div class="card master-form-card">
    <form method="POST" action="{{ $user->exists ? route('admin.master.users.update', $user) : route('admin.master.users.store') }}">
      @csrf
      @if ($user->exists) @method('PUT') @endif
      <div class="form-grid">
        <div class="field full">
          <label for="admin-name">Nama *</label>
          <input id="admin-name" name="name" value="{{ old('name', $user->name) }}" required maxlength="255">
        </div>
        <div class="field full">
          <label for="admin-email">Email *</label>
          <input id="admin-email" name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="255">
        </div>
        <div class="field">
          <label for="admin-password">Kata sandi {{ $user->exists ? '(kosongkan jika tidak diganti)' : '*' }}</label>
          <input id="admin-password" name="password" type="password" minlength="8" autocomplete="new-password" {{ $user->exists ? '' : 'required' }}>
        </div>
        <div class="field">
          <label for="admin-password-confirmation">Konfirmasi kata sandi {{ $user->exists ? '' : '*' }}</label>
          <input id="admin-password-confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" {{ $user->exists ? '' : 'required' }}>
        </div>
      </div>
      <div class="form-actions">
        <button class="btn btn-primary">{{ $user->exists ? 'Simpan Perubahan' : 'Tambah Admin' }}</button>
        <a href="{{ route('admin.master.users.index') }}" class="btn btn-outline">Batal</a>
      </div>
    </form>
  </div>
@endsection
