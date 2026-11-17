@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<section class="auth-wrap">
    <div class="auth-aside"><div class="eyebrow">SELAMAT DATANG KEMBALI</div>
        <h1>Ada bacaan menarik yang menunggumu.</h1>
        <p>Masuk untuk mengelola tulisan, ikut berdiskusi, dan menyimpan catatan komunitasmu di satu tempat.</p>
        <div class="auth-quote">“Ide yang baik berkembang saat dibagikan.”</div>
    </div>
    <div class="form-card"><div class="eyebrow">AKUN KAMU</div><h2>Masuk ke NusaBoard</h2>
        <p class="muted">Gunakan akun komunitas untuk melanjutkan.</p>
        <form method="POST" action="{{ route('login.store') }}">@csrf
            <label class="field-label" for="email">Alamat email</label>
            <input class="input" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            <label class="field-label" for="password">Kata sandi</label>
            <input class="input" id="password" name="password" type="password" autocomplete="current-password" required>
            <label class="check-row"><input type="checkbox" name="remember"> Ingat saya</label>
            <button class="button button-primary button-wide" type="submit">Masuk ↗</button>
        </form>
        <p class="form-foot">Belum punya akun? <a href="{{ route('register') }}">Buat akun</a></p>
        <details class="demo-accounts"><summary>Akun demo lokal</summary><p>Akun admin, editor, dan anggota menggunakan kata sandi password. Lihat PANDUAN.md untuk alamat emailnya.</p></details>
    </div>
</section>
@endsection
