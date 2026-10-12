@extends('admin.layouts.app')
@section('title', 'Management User')
@section('page', 'Master / Management User')

@section('actions')
  <a href="{{ route('admin.master.users.create') }}" class="btn btn-primary">+ Tambah Admin</a>
@endsection

@section('content')
  <div class="card"><p class="muted">Halaman ini hanya mengelola akun admin backend. Akun pelanggan tidak ditampilkan atau diubah di sini.</p></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Nama</th><th>Email</th><th>Dibuat</th><th>Aksi</th></tr></thead>
      <tbody>
        @forelse ($users as $user)
          <tr>
            <td><strong>{{ $user->name }}</strong>@if($user->is(auth()->user())) <span class="badge">Anda</span>@endif</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->created_at?->format('d M Y') ?: '-' }}</td>
            <td class="master-actions">
              <a href="{{ route('admin.master.users.edit', $user) }}" class="btn btn-outline btn-sm">Edit</a>
              @unless ($user->is(auth()->user()))
                <form method="POST" action="{{ route('admin.master.users.destroy', $user) }}" onsubmit="return confirm('Hapus akun admin ini?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger btn-sm">Hapus</button>
                </form>
              @endunless
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="muted">Belum ada akun admin.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="muted" style="margin-top:12px">{!! $users->links() !!}</div>
@endsection
