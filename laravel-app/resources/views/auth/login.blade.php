@extends('layouts.app')
@section('title', 'Sign in')
@section('content')
<section class="auth-wrap">
    <div class="auth-aside">
        <div class="eyebrow">WELCOME BACK</div>
        <h1>Your next good read is waiting.</h1>
        <p>Sign in to manage your stories, join conversations, and keep your community notes in one place.</p>
        <div class="auth-quote">“Good ideas grow when they are shared.”</div>
    </div>
    <div class="form-card">
        <div class="eyebrow">YOUR ACCOUNT</div>
        <h2>Sign in to NusaBoard</h2>
        <p class="muted">Use your community account to continue.</p>
        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <label class="field-label" for="email">Email address</label>
            <input class="input" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            <label class="field-label" for="password">Password</label>
            <input class="input" id="password" name="password" type="password" autocomplete="current-password" required>
            <label class="check-row"><input type="checkbox" name="remember"> Remember me</label>
            <button class="button button-primary button-wide" type="submit">Sign in ↗</button>
        </form>
        <p class="form-foot">New here? <a href="{{ route('register') }}">Create an account</a></p>
        <details class="demo-accounts"><summary>Local demo accounts</summary><p>Admin, editor, and member accounts use the password <code>password</code>. See the README for email addresses.</p></details>
    </div>
</section>
@endsection
