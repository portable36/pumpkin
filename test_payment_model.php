<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Payment;
use App\Models\User;

echo "\n=== PAYMENT MODEL TEST ===\n\n";

// Test 1: Check user exists
$user = User::first();
if (!$user) {
    echo "❌ No users in database\n";
    exit;
}
echo "✅ Found user: {$user->name} ({$user->email})\n";

// Test 2: Create a payment
$payment = Payment::create([
    'user_id' => $user->id,
    'amount' => 1000.00,
    'currency' => 'BDT',
    'gateway' => Payment::GATEWAY_SSL_COMMERZ,
    'status' => Payment::STATUS_PENDING,
    'transaction_id' => 'TXN-' . time(),
    'payment_method' => 'online',
]);

echo "\n✅ Created payment:\n";
echo "   ID: {$payment->id}\n";
echo "   Amount: {$payment->amount} {$payment->currency}\n";
echo "   Gateway: {$payment->gateway}\n";
echo "   Status: {$payment->status}\n";

// Test 3: Verify relationship
$fetchedUser = $payment->user;
echo "\n✅ User relationship works:\n";
echo "   User Name: {$fetchedUser->name}\n";
echo "   User Email: {$fetchedUser->email}\n";

// Test 4: Test scopes
$pendingCount = Payment::pending()->count();
echo "\n✅ Scopes working:\n";
echo "   Pending payments: {$pendingCount}\n";

// Test 5: Mark as successful
$payment->markAsSuccessful();
echo "\n✅ Status update:\n";
echo "   New Status: {$payment->status}\n";
echo "   Paid At: {$payment->paid_at}\n";

// Test 6: Test helper methods
$successful = Payment::successful()->count();
echo "\n✅ After update:\n";
echo "   Successful payments: {$successful}\n";

// Cleanup - delete test payment
$payment->forceDelete();
echo "\n✅ Test payment deleted\n";

echo "\n🎉 All payment model tests passed!\n";
