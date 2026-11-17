@extends('layouts.app')
@section('title', 'Edit artikel')
@section('content')
<section class="page-heading"><div><div class="eyebrow">PENGELOLAAN KONTEN / EDIT</div><h1>Sempurnakan tulisanmu.</h1><p>Pembaruan terakhir {{ $article->updated_at->translatedFormat('d M Y · H:i') }} · Penulis: {{ $article->author->name }}</p></div></section>
<section class="panel"><form method="POST" action="{{ route('articles.update', $article) }}">@method('PUT')@include('articles._form', ['submitLabel' => 'Simpan perubahan'])</form></section>
<details class="lab-note"><summary>Latihan keamanan · IDOR / otorisasi objek yang lemah</summary><p>Rute ini mengambil data berdasarkan ID numerik dan hanya memeriksa apakah pengguna sudah masuk. Rute ini belum memeriksa apakah pengguna memiliki artikel tersebut atau memiliki izin editor. Kekurangan yang sama juga ada pada tindakan perbarui dan hapus.</p></details>
@endsection
