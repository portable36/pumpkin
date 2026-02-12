@extends('layouts.app')

@section('title', 'Register - Pumpkin')

@section('content')
<div class="auth-container">
    <h2>Create Account</h2>
    <form action="/register" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required value="{{ old('name') }}">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required value="{{ old('email') }}">
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>
        <button type="submit" class="btn" style="width: 100%; padding: 1rem;">Register</button>
    </form>
    <p style="text-align: center; margin-top: 1rem;">Already have an account? <a href="/login">Login here</a></p>
</div>
@endsection
