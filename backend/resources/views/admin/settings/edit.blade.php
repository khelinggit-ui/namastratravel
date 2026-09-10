@extends('admin.layouts.app')
@section('title', 'Pengaturan Situs')
@section('page', 'Pengaturan Situs')

@section('content')
  <div class="card">
    <form method="POST" action="{{ route('admin.settings') }}">
      @csrf @method('PUT')
      <div class="form-grid">
        <div class="field"><label>Nama Situs</label><input name="name" value="{{ $settings['name'] }}"></div>
        <div class="field"><label>Tagline</label><input name="tagline" value="{{ $settings['tagline'] }}"></div>
        <div class="field"><label>Alamat</label><input name="address" value="{{ $settings['address'] }}"></div>
        <div class="field"><label>Jam Operasional</label><input name="hours" value="{{ $settings['hours'] }}"></div>
        <div class="field"><label>Telepon</label><input name="phone" value="{{ $settings['phone'] }}"></div>
        <div class="field"><label>WhatsApp (format internasional, cth. 6281234567890)</label><input name="whatsapp" value="{{ $settings['whatsapp'] }}"></div>
        <div class="field"><label>Email</label><input name="email" value="{{ $settings['email'] }}"></div>
        <div class="field"><label>Instagram</label><input name="instagram" value="{{ $settings['instagram'] }}"></div>
        <div class="field"><label>Facebook</label><input name="facebook" value="{{ $settings['facebook'] }}"></div>
        <div class="field"><label>TikTok</label><input name="tiktok" value="{{ $settings['tiktok'] }}"></div>
        <div class="field full"><label>Embed URL Maps</label><textarea name="maps_embed_url" rows="2">{{ $settings['maps_embed_url'] }}</textarea></div>
      </div>
      <div class="form-actions"><button class="btn btn-primary">Simpan Pengaturan</button></div>
    </form>
  </div>
@endsection