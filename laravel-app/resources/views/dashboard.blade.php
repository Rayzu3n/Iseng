@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<section class="page-heading">
    <div><div class="eyebrow">YOUR WORKSPACE</div><h1>Good to see you, {{ auth()->user()->name }}.</h1><p>Here's what's happening with your NusaBoard account.</p></div>
    <a class="button button-primary" href="{{ route('articles.create') }}">Create article +</a>
</section>

<div class="stats-grid">
    <div class="stat-card"><span>MY ARTICLES</span><strong>{{ $articleCount }}</strong><small>Stories and drafts</small></div>
    <div class="stat-card"><span>PUBLISHED</span><strong>{{ $publishedCount }}</strong><small>Visible to readers</small></div>
    <div class="stat-card"><span>MY COMMENTS</span><strong>{{ $commentCount }}</strong><small>Contributions</small></div>
    <div class="stat-card"><span>TOPICS</span><strong>{{ $categoryCount }}</strong><small>Community categories</small></div>
</div>

<div class="dashboard-grid">
    <section class="panel">
        <div class="section-heading"><div><div class="eyebrow">YOUR CONTENT</div><h2>Recent articles</h2></div><a class="text-link" href="{{ route('articles.index') }}">View all ↗</a></div>
        @forelse($myArticles as $article)
            <div class="manage-row">
                <div><strong>{{ $article->title }}</strong><small>{{ ucfirst($article->status) }} · {{ $article->updated_at->format('M d, Y') }}</small></div>
                <a class="button button-small button-quiet" href="{{ route('articles.edit', $article) }}">Edit</a>
            </div>
        @empty
            <div class="empty-state">You haven't written an article yet. Start with a short note.</div>
        @endforelse
    </section>
    <aside class="panel">
        <div class="eyebrow">ACCOUNT OVERVIEW</div>
        <h2>Your access</h2>
        <div class="role-box"><span class="avatar avatar-large">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><div><strong>{{ auth()->user()->name }}</strong><small>{{ strtoupper(auth()->user()->role) }}</small></div></div>
        <p class="muted">Your account role determines which management actions should be available.</p>
        <details class="lab-note"><summary>Security exercise · Admin route</summary><p>The navigation hides the admin link from non-admins, but inspect the route middleware. A hidden link is not authorization.</p><a class="text-link" href="{{ url('/admin') }}">Open /admin directly ↗</a></details>
    </aside>
</div>

<section class="panel panel-wide">
    <div class="section-heading"><div><div class="eyebrow">COMMUNITY PULSE</div><h2>Recently published</h2></div><a class="text-link" href="{{ route('home') }}">Explore stories ↗</a></div>
    <div class="compact-list">
        @foreach($recentArticles as $article)
            <div class="compact-row"><span class="compact-category">{{ $article->category?->name ?? 'Community' }}</span><strong>{{ $article->title }}</strong><small>by {{ $article->author->name }}</small></div>
        @endforeach
    </div>
</section>
@endsection
