@extends('layouts.app')

@section('title', 'Login - Pumpkin')

@section('content')
<div class="auth-container">
    <h2>Login</h2>
    <form action="/login" method="POST">
        @csrf
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required value="{{ old('email') }}">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn" style="width: 100%; padding: 1rem;">Login</button>
    </form>
    <p style="text-align: center; margin-top: 1rem;">Don't have an account? <a href="/register">Register here</a></p>
    <p style="text-align: center; margin-top: 0.5rem;"><a href="/forgot-password">Forgot password?</a></p>
</div>
@endsection
