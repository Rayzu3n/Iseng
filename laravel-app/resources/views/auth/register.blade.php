@extends('layouts.app')
@section('title', 'Create an account')
@section('content')
<section class="auth-wrap">
    <div class="auth-aside">
        <div class="eyebrow">JOIN THE COMMUNITY</div>
        <h1>Make room for new perspectives.</h1>
        <p>Create a member account to publish drafts, share ideas, and contribute to community discussions.</p>
        <div class="auth-quote">One account. A lot of good conversations.</div>
    </div>
    <div class="form-card">
        <div class="eyebrow">GET STARTED</div>
        <h2>Create your account</h2>
        <p class="muted">A few details and you're in.</p>
        <form method="POST" action="{{ route('register.store') }}">
            @csrf
            <label class="field-label" for="name">Display name</label>
            <input class="input" id="name" name="name" value="{{ old('name') }}" autocomplete="name" required>
            <label class="field-label" for="email">Email address</label>
            <input class="input" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
            <label class="field-label" for="password">Password</label>
            <input class="input" id="password" name="password" type="password" autocomplete="new-password" required>
            <label class="field-label" for="password_confirmation">Confirm password</label>
            <input class="input" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
            <button class="button button-primary button-wide" type="submit">Create account ↗</button>
        </form>
        <p class="form-foot">Already registered? <a href="{{ route('login') }}">Sign in</a></p>
    </div>
</section>
@endsection
