@extends('admin.layouts.app')
@section('title', 'Konten Tentang Kami')
@section('page', 'Konten Tentang Kami')

@section('content')
  <div class="card">
    <form method="POST" action="{{ route('admin.about') }}">
      @csrf @method('PUT')
      <div class="form-grid">
        <div class="field full"><label>Visi</label><textarea name="vision" rows="2">{{ $about->vision }}</textarea></div>
        <div class="field full"><label>Cerita (paragraf dipisah baris kosong)</label>
          <textarea name="story" rows="6">{{ $about->story ? implode("\n\n", $about->story) : '' }}</textarea></div>
        <div class="field full"><label>Misi (1 per baris)</label>
          <textarea name="missions" rows="5">{{ $about->missions ? implode("\n", $about->missions) : '' }}</textarea></div>
        <div class="field full"><label>Statistik (format: <code>value|label</code> per baris)</label>
          <textarea name="stats" rows="4">{{ collect($about->stats)->map(fn ($s) => ($s['value'] ?? '').'|'.($s['label'] ?? ''))->join("\n") }}</textarea></div>
        <div class="field full"><label>Keunggulan / Nilai (format: <code>Judul|Deskripsi</code> per baris)</label>
          <textarea name="values" rows="5">{{ collect($about->values)->map(fn ($v) => ($v['title'] ?? '').'|'.($v['desc'] ?? ''))->join("\n") }}</textarea></div>
      </div>
      <div class="form-actions"><button class="btn btn-primary">Simpan Konten</button></div>
    </form>
  </div>
@endsection