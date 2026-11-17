@extends('layouts.app')
@section('title', 'Edit article')
@section('content')
<section class="page-heading"><div><div class="eyebrow">CONTENT STUDIO / EDIT</div><h1>Refine your story.</h1><p>Last updated {{ $article->updated_at->format('M d, Y · H:i') }} · Author: {{ $article->author->name }}</p></div></section>
<section class="panel"><form method="POST" action="{{ route('articles.update', $article) }}">@method('PUT')@include('articles._form', ['submitLabel' => 'Save changes'])</form></section>
<details class="lab-note"><summary>Security exercise · IDOR / broken object-level authorization</summary><p>This route loads a record by its numeric ID and checks only whether a user is signed in. It does not check whether the current user owns the article or has an editor permission. The same flaw exists in update and delete actions.</p></details>
@endsection
