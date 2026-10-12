@extends('admin.layouts.app')
@section('title', 'Banner Hero')
@section('page', 'Master / Banner Hero')

@section('actions')
  <a href="{{ route('admin.master.banners.create') }}" class="btn btn-primary">+ Tambah Banner</a>
@endsection

@section('content')
  <div class="table-wrap">
    <table>
      <thead><tr><th>Gambar</th><th>Judul</th><th>Teks Alternatif</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
        @forelse ($banners as $banner)
          <tr>
            <td><img class="thumb master-banner-thumb" src="{{ \App\Support\Media::url($banner->image) }}" alt="{{ $banner->alt_text ?: $banner->title }}"></td>
            <td><strong>{{ $banner->title }}</strong></td>
            <td>{{ $banner->alt_text ?: '-' }}</td>
            <td>{{ $banner->sort }}</td>
            <td><span class="badge {{ $banner->is_active ? 'published' : 'draft' }}">{{ $banner->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
            <td class="master-actions">
              <a href="{{ route('admin.master.banners.edit', $banner) }}" class="btn btn-outline btn-sm">Edit</a>
              <form method="POST" action="{{ route('admin.master.banners.destroy', $banner) }}" onsubmit="return confirm('Hapus banner ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="muted">Belum ada banner. Tambahkan banner untuk ditampilkan pada hero halaman utama.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="muted" style="margin-top:12px">{!! $banners->links() !!}</div>
@endsection
