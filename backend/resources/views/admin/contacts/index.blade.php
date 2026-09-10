@extends('admin.layouts.app')
@section('title', 'Kontak Masuk')
@section('page', 'Kontak Masuk')

@section('content')
  <div class="card">
    <form method="GET" style="display:flex;gap:10px;align-items:center">
      <select name="status" style="max-width:180px">
        <option value="">Semua</option>
        <option value="baru" @selected(request('status')==='baru')>Baru</option>
        <option value="dibaca" @selected(request('status')==='dibaca')>Dibaca</option>
      </select>
      <button class="btn btn-outline">Filter</button>
    </form>
  </div>

  <div class="table-wrap">
    <table>
      <thead><tr><th>Nama</th><th>Email</th><th>Pesan</th><th>Status</th><th>Masuk</th><th>Aksi</th></tr></thead>
      <tbody>
        @foreach ($contacts as $c)
          <tr>
            <td><strong>{{ $c->name }}</strong></td>
            <td><a href="mailto:{{ $c->email }}">{{ $c->email }}</a></td>
            <td class="muted" style="max-width:320px">{{ \Illuminate\Support\Str::limit($c->message, 90) }}</td>
            <td>
              <form method="POST" action="{{ route('admin.contacts.update', $c) }}">
                @csrf @method('PUT')
                <select name="status" onchange="this.form.submit()" style="width:auto;padding:4px 8px">
                  <option value="baru" @selected($c->status==='baru')>Baru</option>
                  <option value="dibaca" @selected($c->status==='dibaca')>Dibaca</option>
                </select>
              </form>
            </td>
            <td class="muted">{{ $c->created_at->format('d M Y H:i') }}</td>
            <td>
              <form method="POST" action="{{ route('admin.contacts.destroy', $c) }}" onsubmit="return confirm('Hapus pesan ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">Hapus</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="muted" style="margin-top:12px">{!! $contacts->links() !!}</div>
@endsection