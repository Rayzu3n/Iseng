@extends('layouts.app')
@section('title', 'Administration')
@section('content')
<section class="page-heading"><div><div class="eyebrow">CONTROL ROOM</div><h1>Administration</h1><p>Account directory and platform overview.</p></div><span class="tag tag-warning">ROLE CHECK LAB</span></section>
<div class="stats-grid">
    <div class="stat-card"><span>ACCOUNTS</span><strong>{{ $users->count() }}</strong><small>Registered users</small></div>
    <div class="stat-card"><span>ARTICLES</span><strong>{{ $articleCount }}</strong><small>{{ $publishedCount }} published · {{ $draftCount }} drafts</small></div>
    <div class="stat-card"><span>COMMENTS</span><strong>{{ $commentCount }}</strong><small>Community discussion</small></div>
    <div class="stat-card"><span>CATEGORIES</span><strong>{{ $categoryCount }}</strong><small>Content topics</small></div>
</div>
<section class="panel">
    <div class="section-heading"><div><div class="eyebrow">USER DIRECTORY</div><h2>Registered accounts</h2></div><span class="muted">Signed in as {{ auth()->user()->email }}</span></div>
    <div class="table-wrap"><table class="data-table"><thead><tr><th>NAME</th><th>EMAIL</th><th>ROLE</th><th>ARTICLES</th><th>COMMENTS</th></tr></thead><tbody>
    @foreach($users as $user)
        <tr><td><strong>{{ $user->name }}</strong></td><td>{{ $user->email }}</td><td><span class="role-pill role-{{ $user->role }}">{{ $user->role }}</span></td><td>{{ $user->articles_count }}</td><td>{{ $user->comments_count }}</td></tr>
    @endforeach
    </tbody></table></div>
</section>
<details class="lab-note"><summary>Security exercise · Role-based access control</summary><p>This page is linked only for admin users in the navigation, but the route middleware verifies only authentication. A regular member can request <code>/admin</code> directly. Learn the difference between hiding a link and enforcing a server-side role or policy check.</p></details>
@endsection
