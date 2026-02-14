<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Find or create admin user
$admin = User::firstOrCreate(
    ['email' => 'admin@example.com'],
    [
        'name' => 'Administrator',
        'password' => Hash::make('password'),
        'email_verified_at' => now(),
    ]
);

echo "\n✅ Admin User Ready!\n";
echo "Email: admin@example.com\n";
echo "Password: password\n\n";
echo "Access: http://localhost:8000/admin\n";
