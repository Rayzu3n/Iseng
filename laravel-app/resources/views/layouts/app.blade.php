<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#10151d">
    <title>@yield('title', 'NusaBoard') · NusaBoard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="{{ route('home') }}"><span class="brand-mark">N</span><span>Nusa<span class="brand-accent">Board</span></span></a>
        <nav class="main-nav">
            <a href="{{ route('home') }}">Explore</a>
            @auth
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('articles.index') }}">My articles</a>
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.index') }}">Administration</a>
                @endif
            @endauth
        </nav>
        <div class="header-actions">
            @auth
                <span class="user-chip"><span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="button button-quiet" type="submit">Sign out</button></form>
            @else
                <a class="button button-quiet" href="{{ route('login') }}">Sign in</a>
                <a class="button button-primary" href="{{ route('register') }}">Join community</a>
            @endauth
        </div>
    </div>
</header>

<main class="page-shell">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error">
            <strong>Something needs attention.</strong>
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    @yield('content')
</main>

<footer class="site-footer">
    <div><strong>NusaBoard</strong><span> · A community publishing playground</span></div>
    <span>LOCAL SECURITY TRAINING ENVIRONMENT</span>
</footer>
</body>
</html>
