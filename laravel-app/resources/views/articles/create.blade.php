@extends('layouts.app')
@section('title', 'Tulis artikel')
@section('content')
<section class="page-heading"><div><div class="eyebrow">PENGELOLAAN KONTEN / BARU</div><h1>Tulis sebuah cerita.</h1><p>Ubah ide menjadi tulisan yang bermanfaat bagi komunitas.</p></div></section>
<section class="panel"><form method="POST" action="{{ route('articles.store') }}">@include('articles._form', ['submitLabel' => 'Buat artikel'])</form></section>
<details class="lab-note"><summary>Latihan keamanan · Mass assignment</summary><p>Antarmuka menampilkan pilihan publikasi kepada setiap anggota yang sudah masuk. Bandingkan daftar validasi di controller dengan data yang dikirim ke Article::create(), lalu periksa atribut tambahan yang diizinkan oleh model.</p></details>
@endsection
