@extends('layouts.app')
@section('title', 'Buat akun')
@section('content')
<section class="auth-wrap">
    <div class="auth-aside"><div class="eyebrow">GABUNG DENGAN KOMUNITAS</div>
        <h1>Buka ruang untuk sudut pandang baru.</h1>
        <p>Buat akun anggota untuk menyimpan draf, berbagi ide, dan ikut berdiskusi dengan komunitas.</p>
        <div class="auth-quote">Satu akun. Banyak percakapan menarik.</div>
    </div>
    <div class="form-card"><div class="eyebrow">MULAI DI SINI</div><h2>Buat akun kamu</h2>
        <p class="muted">Isi beberapa detail berikut untuk bergabung.</p>
        <form method="POST" action="{{ route('register.store') }}">@csrf
            <label class="field-label" for="name">Nama tampilan</label>
            <input class="input" id="name" name="name" value="{{ old('name') }}" autocomplete="name" required>
            <label class="field-label" for="email">Alamat email</label>
            <input class="input" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
            <label class="field-label" for="password">Kata sandi</label>
            <input class="input" id="password" name="password" type="password" autocomplete="new-password" required>
            <label class="field-label" for="password_confirmation">Konfirmasi kata sandi</label>
            <input class="input" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
            <button class="button button-primary button-wide" type="submit">Buat akun ↗</button>
        </form>
        <p class="form-foot">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
    </div>
</section>
@endsection
