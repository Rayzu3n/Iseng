@extends('layouts.app')
@section('title', 'Administrasi')
@section('content')
<section class="page-heading"><div><div class="eyebrow">PUSAT KONTROL</div><h1>Administrasi</h1><p>Direktori akun dan ringkasan platform.</p></div><span class="tag tag-warning">LATIHAN PEMERIKSAAN PERAN</span></section>
<div class="stats-grid">
    <div class="stat-card"><span>AKUN</span><strong>{{ $users->count() }}</strong><small>Pengguna terdaftar</small></div>
    <div class="stat-card"><span>ARTIKEL</span><strong>{{ $articleCount }}</strong><small>{{ $publishedCount }} terbit · {{ $draftCount }} draf</small></div>
    <div class="stat-card"><span>KOMENTAR</span><strong>{{ $commentCount }}</strong><small>Diskusi komunitas</small></div>
    <div class="stat-card"><span>KATEGORI</span><strong>{{ $categoryCount }}</strong><small>Topik konten</small></div>
</div>
<section class="panel">
    <div class="section-heading"><div><div class="eyebrow">DIREKTORI PENGGUNA</div><h2>Akun terdaftar</h2></div><span class="muted">Masuk sebagai {{ auth()->user()->email }}</span></div>
    <div class="table-wrap"><table class="data-table"><thead><tr><th>NAMA</th><th>EMAIL</th><th>PERAN</th><th>ARTIKEL</th><th>KOMENTAR</th></tr></thead><tbody>
    @foreach($users as $user)
        <tr><td><strong>{{ $user->name }}</strong></td><td>{{ $user->email }}</td><td><span class="role-pill role-{{ $user->role }}">{{ $user->role === 'admin' ? 'Admin' : ($user->role === 'editor' ? 'Editor' : 'Anggota') }}</span></td><td>{{ $user->articles_count }}</td><td>{{ $user->comments_count }}</td></tr>
    @endforeach
    </tbody></table></div>
</section>
<details class="lab-note"><summary>Latihan keamanan · Role-based access control</summary><p>Halaman ini hanya ditautkan untuk admin di navigasi, tetapi middleware rute hanya memeriksa autentikasi. Anggota biasa tetap dapat meminta alamat /admin secara langsung. Pelajari perbedaan antara menyembunyikan tautan dan menerapkan pemeriksaan peran di sisi server.</p></details>
@endsection
