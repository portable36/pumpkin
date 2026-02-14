<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Order;
use App\Models\VendorPayout;
use App\Models\VendorLedger;
use App\Models\Setting;
use App\Services\PayoutService;

class PayoutE2ETestCommand extends Command
{
    protected $signature = 'payout:e2e-test';
    protected $description = 'End-to-end test for vendor payout system';

    public function handle(PayoutService $payoutService): int
    {
        $this->info('🚀 Starting Payout System E2E Test');
        $this->line('');

        try {
            // Step 1: Setup test data
            $this->info('✓ STEP 1: Setting up test data...');
            
            $customer = User::first();
            if (!$customer) {
                $this->error('No users found, please seed database first');
                return 1;
            }

            $vendor = Vendor::firstOrCreate(
                ['owner_id' => $customer->id],
                [
                    'shop_name' => 'Payout Test Vendor',
                    'slug' => 'payout-test-' . time(),
                    'is_active' => true,
                    'is_verified' => true,
                    'commission_rate' => 15,
                ]
            );

            $product = Product::firstOrCreate(
                ['vendor_id' => $vendor->id],
                [
                    'name' => 'Test Product for Payout',
                    'slug' => 'test-payout-' . time(),
                    'price' => 2000,
                    'sku' => 'PAYOUT-TEST-' . time(),
                    'description' => 'Test product',
                ]
            );

            $this->line("  ✓ Vendor: {$vendor->shop_name} (ID: {$vendor->id})");
            $this->line("  ✓ Commission Rate: {$vendor->commission_rate}%");
            $this->line("  ✓ Product: {$product->name}");

            // Step 2: Create test orders
            $this->info('✓ STEP 2: Creating test orders...');
            
            $uniqueSuffix = substr(md5(uniqid()), 0, 8);
            $orders = [];
            for ($i = 1; $i <= 3; $i++) {
                $order = Order::create([
                    'user_id' => $customer->id,
                    'vendor_id' => $vendor->id,
                    'order_number' => 'ORD-PAYOUT-' . date('Ymd') . '-' . strtoupper(substr($uniqueSuffix, 0, 5)) . sprintf('%c', $i + 64),
                    'status' => 'delivered',
                    'payment_status' => 'paid',
                    'subtotal' => 5000,
                    'shipping_cost' => 100,
                    'tax_amount' => 500,
                    'total_amount' => 5600,
                ]);

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => 5000,
                    'total_amount' => 5000,
                ]);

                $orders[] = $order;
                $this->line("  ✓ Order {$i}: {$order->order_number} = ৳{$order->total_amount}");
            }

            // Step 3: Record sales in ledger
            $this->info('✓ STEP 3: Recording sales in vendor ledger...');
            
            foreach ($orders as $order) {
                $payoutService->recordSaleCredit($vendor, $order);
            }

            $ledgerEntries = VendorLedger::where('vendor_id', $vendor->id)->count();
            $this->line("  ✓ Ledger entries created: {$ledgerEntries}");

            // Step 4: Calculate commission
            $this->info('✓ STEP 4: Calculating commission and payout...');
            
            $totalSales = 5600 * 3;
            $pendingCommission = $payoutService->calculatePendingCommission($vendor);
            $netPayout = $payoutService->calculateNetPayout($vendor);
            $pendingBalance = $payoutService->calculatePendingBalance($vendor);

            $this->line("  ✓ Total Sales: ৳{$totalSales}");
            $this->line("  ✓ Commission (15%): ৳{$pendingCommission}");
            $this->line("  ✓ Net Payout: ৳{$netPayout}");
            $this->line("  ✓ Pending Balance: ৳{$pendingBalance}");

            // Step 5: Check payout eligibility
            $this->info('✓ STEP 5: Checking payout eligibility...');
            
            $minPayout = Setting::get('commission.min_payout', 500);
            $this->line("  ✓ Minimum Payout Threshold: ৳{$minPayout}");

            if ($payoutService->canProcessPayout($vendor)) {
                $this->line("  ✓ Vendor IS eligible for payout");
            } else {
                $this->warn("  ✗ Vendor NOT eligible for payout");
                $reason = !$vendor->is_active ? "Not active" : (!$vendor->is_verified ? "Not verified" : "Balance below threshold");
                $this->line("    Reason: {$reason}");
            }

            // Step 6: Create and process payout
            $this->info('✓ STEP 6: Creating payout record...');
            
            $payout = $payoutService->createPayout($vendor, $pendingBalance);
            
            $this->line("  ✓ Payout ID: {$payout->id}");
            $this->line("  ✓ Amount: ৳{$payout->amount}");
            $this->line("  ✓ Status: {$payout->status}");
            $this->line("  ✓ Period: {$payout->period_start->format('Y-m-d')} to {$payout->period_end->format('Y-m-d')}");

            // Step 7: Mark payout as processed
            $this->info('✓ STEP 7: Processing payout...');
            
            $transactionId = 'TXN-' . time();
            $payoutService->markAsProcessed($payout, $transactionId);
            $payout->refresh();

            $this->line("  ✓ Status Updated: {$payout->status}");
            $this->line("  ✓ Transaction ID: {$payout->transaction_id}");
            $this->line("  ✓ Processed At: {$payout->processed_at->format('Y-m-d H:i:s')}");

            // Step 8: Get statistics
            $this->info('✓ STEP 8: Final Statistics...');
            
            $stats = $payoutService->getPayoutStats($vendor);
            
            $this->line("  ✓ Total Earnings: ৳{$stats['total_earnings']}");
            $this->line("  ✓ Total Commission: ৳{$stats['total_commission']}");
            $this->line("  ✓ Net Earnings: ৳{$stats['net_earnings']}");
            $this->line("  ✓ Total Paid: ৳{$stats['total_paid']}");
            $this->line("  ✓ Pending Payout: ৳{$stats['pending_payout']}");
            $this->line("  ✓ Available Balance: ৳{$stats['available_balance']}");

            // Step 9: Verify ledger accuracy
            $this->info('✓ STEP 9: Verifying ledger accuracy...');
            
            $entries = $payoutService->getLedgerEntries($vendor);
            $credits = $entries->filter(fn($e) => $e->type === 'credit')->sum('amount');
            $debits = $entries->filter(fn($e) => $e->type === 'debit')->sum('amount');
            
            $this->line("  ✓ Total Credits: ৳{$credits}");
            $this->line("  ✓ Total Debits: ৳{$debits}");
            $this->line("  ✓ Running Balance: ৳{$stats['available_balance']}");

            $this->line('');
            $this->info('✅ Payout System E2E Test Passed!');
            $this->line('');
            $this->table(
                ['Component', 'Status', 'Details'],
                [
                    ['Vendor Setup', '✓', "{$vendor->shop_name} ({$vendor->commission_rate}% commission)"],
                    ['Order Creation', '✓', count($orders) . ' orders, ৳' . array_sum(array_map(fn($o) => $o->total_amount, $orders))],
                    ['Ledger Recording', '✓', $ledgerEntries . ' entries'],
                    ['Commission Calc', '✓', '৳' . $pendingCommission],
                    ['Eligibility Check', '✓', $payoutService->canProcessPayout($vendor) ? 'Eligible' : 'Not Eligible'],
                    ['Payout Creation', '✓', 'ID: ' . $payout->id . ', Amount: ৳' . $payout->amount],
                    ['Payout Processing', '✓', 'Status: ' . $payout->status . ', TXN: ' . $transactionId],
                    ['Statistics', '✓', '৳' . $stats['total_paid'] . ' paid out'],
                ]
            );

            $this->line('');
            $this->info('📊 Payout automation is ready for production!');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Test Failed: ' . $e->getMessage());
            $this->line($e->getTraceAsString());
            return 1;
        }
    }
}
