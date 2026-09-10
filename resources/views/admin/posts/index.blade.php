@extends('admin.layouts.app')
@section('title', 'Blog / Artikel')
@section('page', 'Blog / Artikel')
@section('actions')<a href="{{ route('admin.posts.create') }}" class="btn btn-primary">+ Tulis Artikel</a>@endsection

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
      <thead><tr><th>Cover</th><th>Judul</th><th>Kategori</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
        @foreach ($posts as $p)
          <tr>
            <td>@if($p->cover)<img class="thumb" src="{{ \App\Support\Media::url($p->cover) }}">@else<span class="muted">-</span>@endif</td>
            <td><strong>{{ $p->title }}</strong></td>
            <td>{{ $p->category ?: '-' }}</td>
            <td>{{ $p->published_at?->format('d M Y') }}</td>
            <td><span class="badge {{ $p->status }}">{{ $p->status }}</span></td>
            <td style="white-space:nowrap">
              <a href="{{ route('admin.posts.edit', $p) }}" class="btn btn-outline btn-sm">Edit</a>
              <form method="POST" action="{{ route('admin.posts.destroy', $p) }}" style="display:inline" onsubmit="return confirm('Hapus artikel ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">Hapus</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="muted" style="margin-top:12px">{!! $posts->links() !!}</div>
@endsection