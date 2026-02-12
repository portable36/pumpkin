@extends('layouts.app')

@section('title', 'Contact - Pumpkin')

@section('content')
<div class="container">
    <div style="max-width: 600px; margin: 4rem auto;">
        <h1 style="text-align: center; margin-bottom: 2rem;">Contact Us</h1>
        
        <form action="/contact" method="POST" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" required>
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="6" required></textarea>
            </div>
            <button type="submit" class="btn" style="width: 100%; padding: 1rem;">Send Message</button>
        </form>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; margin-top: 4rem;">
            <div style="text-align: center;">
                <h3>📧 Email</h3>
                <p><a href="mailto:support@pumpkin.com">support@pumpkin.com</a></p>
            </div>
            <div style="text-align: center;">
                <h3>📱 Phone</h3>
                <p><a href="tel:+1234567890">+1 (234) 567-890</a></p>
            </div>
            <div style="text-align: center;">
                <h3>📍 Address</h3>
                <p>123 Main St, City, Country</p>
            </div>
        </div>
    </div>
</div>
@endsection
