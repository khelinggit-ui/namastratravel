@extends('admin.layouts.app')
@section('title', 'Destinasi')
@section('page', 'Destinasi')
@section('actions')<a href="{{ route('admin.destinations.create') }}" class="btn btn-primary">+ Tambah Destinasi</a>@endsection

@section('content')
  <div class="table-wrap">
    <table>
      <thead><tr><th>Gambar</th><th>Nama</th><th>Negara</th><th>Tipe</th><th>Tour Terkait</th><th>Aksi</th></tr></thead>
      <tbody>
        @foreach ($destinations as $d)
          <tr>
            <td>@if($d->image)<img class="thumb" src="{{ \App\Support\Media::url($d->image) }}">@else<span class="muted">-</span>@endif</td>
            <td><strong>{{ $d->name }}</strong></td>
            <td>{{ $d->country ?: '-' }}</td>
            <td><span class="badge {{ $d->type === 'Domestik' ? 'domestik' : 'mancanegara' }}">{{ $d->type }}</span></td>
            <td class="muted">{{ $d->tours->pluck('title')->join(', ') ?: '-' }}</td>
            <td style="white-space:nowrap">
              <a href="{{ route('admin.destinations.edit', $d) }}" class="btn btn-outline btn-sm">Edit</a>
              <form method="POST" action="{{ route('admin.destinations.destroy', $d) }}" style="display:inline" onsubmit="return confirm('Hapus destinasi ini?')">
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