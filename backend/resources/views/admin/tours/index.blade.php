@extends('admin.layouts.app')
@section('title', 'Tour')
@section('page', 'Tour')

@section('actions')
  <a href="{{ route('admin.tours.create') }}" class="btn btn-primary">+ Tambah Tour</a>
@endsection

@section('content')
  <div class="card">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:4px">
      <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul..." style="max-width:280px">
      <select name="status" style="max-width:180px">
        <option value="">Semua status</option>
        <option value="published" @selected(request('status')==='published')>Published</option>
        <option value="draft" @selected(request('status')==='draft')>Draft</option>
      </select>
      <button class="btn btn-outline">Filter</button>
    </form>
  </div>

  <div class="table-wrap">
    <table>
      <thead><tr><th>Gambar</th><th>Judul</th><th>Kategori</th><th>Harga</th><th>Durasi</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
        @foreach ($tours as $tour)
          <tr>
            <td>
              @if ($tour->image)<img class="thumb" src="{{ \App\Support\Media::url($tour->image) }}">@else<span class="muted">-</span>@endif
            </td>
            <td><strong>{{ $tour->title }}</strong></td>
            <td><span class="badge {{ $tour->category }}">{{ ucfirst($tour->category) }}</span></td>
            <td>Rp {{ number_format($tour->price_start, 0, ',', '.') }}</td>
            <td>{{ $tour->duration ?: '-' }}</td>
            <td><span class="badge {{ $tour->status }}">{{ $tour->status }}</span></td>
            <td style="white-space:nowrap">
              <a href="{{ route('admin.tours.edit', $tour) }}" class="btn btn-outline btn-sm">Edit</a>
              <form method="POST" action="{{ route('admin.tours.destroy', $tour) }}" style="display:inline" onsubmit="return confirm('Hapus tour ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">Hapus</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="muted" style="margin-top:12px">{!! $tours->links() !!}</div>
@endsection