@extends('layouts.app')
@section('title', 'Write an article')
@section('content')
<section class="page-heading"><div><div class="eyebrow">CONTENT STUDIO / NEW</div><h1>Write a story.</h1><p>Shape an idea into something the community can use.</p></div></section>
<section class="panel"><form method="POST" action="{{ route('articles.store') }}">@include('articles._form', ['submitLabel' => 'Create article'])</form></section>
<details class="lab-note"><summary>Security exercise · Mass assignment</summary><p>The UI shows publication fields to every signed-in member. Compare the controller's validation list with the array passed to <code>Article::create()</code> and inspect which extra fields the model accepts.</p></details>
@endsection
