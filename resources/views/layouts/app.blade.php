<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pumpkin - Multivendor Marketplace')</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ff6b35">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; background: #f5f5f5; }
        .navbar { background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 100; }
        .navbar-container { max-width: 1200px; margin: 0 auto; padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 24px; font-weight: bold; color: #ff6b35; }
        .nav-links { display: flex; gap: 2rem; list-style: none; }
        .nav-links a { text-decoration: none; color: #333; font-weight: 500; transition: color 0.3s; }
        .nav-links a:hover { color: #ff6b35; }
        .nav-links button:hover { color: #ff6b35; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem 1rem; }
        .hero { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 4rem 1rem; text-align: center; }
        .hero h1 { font-size: 3rem; margin-bottom: 1rem; }
        .hero p { font-size: 1.2rem; margin-bottom: 2rem; }
        .btn { display: inline-block; padding: 0.75rem 1.5rem; background: #ff6b35; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; font-weight: 600; transition: background 0.3s; }
        .btn:hover { background: #e55a25; }
        .btn-outline { background: transparent; border: 2px solid white; }
        .btn-outline:hover { background: white; color: #667eea; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 2rem; margin: 2rem 0; }
        .card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; }
        .card:hover { transform: translateY(-4px); box-shadow: 0 8px 12px rgba(0,0,0,0.15); }
        .card-image { width: 100%; height: 200px; background: #e0e0e0; display: flex; align-items: center; justify-content: center; }
        .card-body { padding: 1.5rem; }
        .card-title { font-size: 1.2rem; font-weight: bold; margin-bottom: 0.5rem; }
        .card-price { font-size: 1.5rem; color: #ff6b35; font-weight: bold; margin: 1rem 0; }
        .rating { color: #ffc107; }
        .footer { background: #333; color: white; padding: 2rem; text-align: center; margin-top: 4rem; }
        .alert { padding: 1rem; margin: 1rem 0; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; }
        .form-group textarea { resize: vertical; }
        .auth-container { max-width: 400px; margin: 4rem auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .auth-container h2 { margin-bottom: 2rem; text-align: center; color: #333; }
        .dashboard { display: grid; grid-template-columns: 250px 1fr; gap: 2rem; min-height: calc(100vh - 200px); }
        .sidebar { background: white; padding: 2rem; border-radius: 8px; height: fit-content; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .sidebar-menu { list-style: none; }
        .sidebar-menu li { margin: 0.5rem 0; }
        .sidebar-menu a { text-decoration: none; color: #333; display: block; padding: 0.75rem 1rem; border-radius: 4px; transition: background 0.3s; }
        .sidebar-menu a:hover { background: #f0f0f0; }
        .sidebar-menu a.active { background: #ff6b35; color: white; }
        .main-content { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid #ddd; }
        .table th { background: #f5f5f5; font-weight: 600; }
        .table tr:hover { background: #f9f9f9; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center; }
        .stat-card .number { font-size: 2rem; font-weight: bold; color: #ff6b35; }
        .stat-card .label { color: #666; margin-top: 0.5rem; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: white; padding: 2rem; border-radius: 8px; max-width: 500px; width: 90%; }
        .chat-container { display: flex; flex-direction: column; height: 500px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; }
        .chat-messages { flex: 1; overflow-y: auto; padding: 1rem; background: #f9f9f9; }
        .message { margin: 1rem 0; padding: 0.75rem 1rem; border-radius: 4px; max-width: 80%; }
        .message.sent { background: #667eea; color: white; margin-left: auto; }
        .message.received { background: white; border: 1px solid #ddd; }
        .chat-input { padding: 1rem; border-top: 1px solid #ddd; display: flex; gap: 0.5rem; }
        .chat-input input { flex: 1; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; }
        .badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.85rem; font-weight: 600; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        @media (max-width: 768px) {
            .nav-links { gap: 1rem; }
            .hero h1 { font-size: 2rem; }
            .grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); }
            .dashboard { grid-template-columns: 1fr; }
            .table { font-size: 0.9rem; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">🎃 Pumpkin</div>
            <ul class="nav-links">
                <li><a href="/">Home</a></li>
                <li><a href="/shop">Shop</a></li>
                <li><a href="/about">About</a></li>
                <li><a href="/contact">Contact</a></li>
                <li><a href="/cart">Cart ({{ session('cart_count', 0) }})</a></li>
                @auth
                    <li><a href="/dashboard">Dashboard</a></li>
                    <li>
                        <form action="/logout" method="POST" style="display: inline;" onsubmit="return confirm('Logout?')">
                            @csrf
                            <button type="submit" style="background: none; border: none; color: #333; font-weight: 500; cursor: pointer; font-family: inherit; font-size: inherit; padding: 0; transition: color 0.3s;">Logout</button>
                        </form>
                    </li>
                @else
                    <li><a href="/login" class="btn">Login</a></li>
                @endauth
            </ul>
        </div>
    </nav>

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="alert alert-error">{{ $error }}</div>
        @endforeach
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @yield('content')

    <footer class="footer">
        <p>&copy; 2026 Pumpkin Marketplace. All rights reserved.</p>
        <div style="margin-top: 1rem; display: flex; justify-content: center; gap: 2rem;">
            <a href="/terms" style="color: white; text-decoration: none;">Terms</a>
            <a href="/privacy" style="color: white; text-decoration: none;">Privacy</a>
            <a href="/contact" style="color: white; text-decoration: none;">Support</a>
        </div>
    </footer>
</body>
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js').then(function(reg) {
                console.log('Service worker registered.', reg);
            }).catch(function(err) {
                console.warn('Service worker registration failed:', err);
            });
        });
    }
</script>
</html>
