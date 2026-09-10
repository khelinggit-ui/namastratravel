@extends('admin.layouts.app')
@section('title', 'Testimoni')
@section('page', 'Testimoni')
@section('actions')<a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">+ Tambah Testimoni</a>@endsection

@section('content')
  <div class="table-wrap">
    <table>
      <thead><tr><th>Nama</th><th>Lokasi</th><th>Testimoni</th><th>Rating</th><th>Aksi</th></tr></thead>
      <tbody>
        @foreach ($testimonials as $t)
          <tr>
            <td><strong>{{ $t->name }}</strong></td>
            <td>{{ $t->location ?: '-' }}</td>
            <td class="muted" style="max-width:360px">{{ \Illuminate\Support\Str::limit($t->text, 100) }}</td>
            <td>{{ str_repeat('★', $t->rating) }}</td>
            <td style="white-space:nowrap">
              <a href="{{ route('admin.testimonials.edit', $t) }}" class="btn btn-outline btn-sm">Edit</a>
              <form method="POST" action="{{ route('admin.testimonials.destroy', $t) }}" style="display:inline" onsubmit="return confirm('Hapus testimoni ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">Hapus</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endsection