<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Vendor;
use App\Models\VendorPayout;
use App\Models\VendorLedger;
use Illuminate\Support\Facades\Log;

/**
 * Process vendor payouts based on available balance
 * Scheduled to run monthly or on-demand
 */
class ProcessVendorPayoutsJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected int $minPayoutAmount;

    public function __construct(int $minPayoutAmount = 500)
    {
        $this->minPayoutAmount = $minPayoutAmount;
    }

    public function handle(): void
    {
        $vendors = Vendor::where('status', 'approved')
            ->where('kyc_status', 'approved')
            ->get();

        foreach ($vendors as $vendor) {
            $this->processVendorPayout($vendor);
        }

        Log::info('Vendor payouts processed', ['vendor_count' => $vendors->count()]);
    }

    protected function processVendorPayout(Vendor $vendor): void
    {
        // Calculate balance from ledger
        $ledgerEntries = VendorLedger::where('vendor_id', $vendor->id)->get();
        $balance = $ledgerEntries->sum('amount');

        if ($balance < $this->minPayoutAmount) {
            return;
        }

        // Create payout record
        $payout = VendorPayout::create([
            'vendor_id' => $vendor->id,
            'amount' => $balance,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        // Record ledger entry
        VendorLedger::recordPayout($vendor->id, $balance);

        Log::info("Payout created for vendor {$vendor->id}", [
            'payout_id' => $payout->id,
            'amount' => $balance,
        ]);

        // TODO: Integrate with actual payment processor (bank transfer, etc)
    }
}
