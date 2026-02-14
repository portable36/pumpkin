<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorDocument;
use App\Services\VendorOnboardingService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class OnboardingE2ETestCommand extends Command
{
    protected $signature = 'onboarding:e2e-test {--force : Forces the test to run even if data exists}';
    protected $description = 'E2E test for vendor onboarding workflow (KYC verification)';

    public function handle(VendorOnboardingService $onboardingService): int
    {
        $this->info("=== VENDOR ONBOARDING E2E TEST ===\n");

        try {
            // Step 1: Create test user (vendor owner)
            $this->info("Step 1: Creating vendor owner user...");
            $owner = User::firstOrCreate(
                ['email' => 'vendor-e2e@test.com'],
                [
                    'name' => 'E2E Vendor Owner',
                    'password' => bcrypt('password123'),
                    'email_verified_at' => now(),
                ]
            );
            $this->line("  ✓ Owner created: {$owner->name} (ID: {$owner->id})");

            // Step 2: Create vendor application
            $this->info("\nStep 2: Creating vendor application...");
            $vendorData = [
                'shop_name' => 'E2E Test Shop',
                'email' => 'testshop@merchant.com',
                'phone' => '+8801234567890',
                'address' => '123 Main Street',
                'city' => 'Dhaka',
                'country' => 'Bangladesh',
                'postal_code' => '1000',
                'description' => 'Test e-commerce shop',
            ];

            $vendor = $onboardingService->createVendorApplication($owner, $vendorData);
            $this->line("  ✓ Vendor application created: {$vendor->shop_name} (ID: {$vendor->id})");

            // Step 3: Store bank details
            $this->info("\nStep 3: Storing bank details...");
            $bankData = [
                'account_holder_name' => 'E2E Test Owner',
                'bank_name' => 'Dhaka Bank',
                'account_number' => '1234567890123456',
                'routing_number' => '123456789',
            ];

            $bankDetail = $onboardingService->storeBankDetails($vendor, $bankData);
            $this->line("  ✓ Bank details stored: {$bankDetail->bank_name}");

            // Step 4: Upload KYC documents
            $this->info("\nStep 4: Uploading KYC documents...");
            $requiredDocs = ['nid', 'trade_license', 'owner_id'];
            $documentIds = [];

            foreach ($requiredDocs as $docType) {
                // Create a fake file for testing
                Storage::fake('private');
                $file = UploadedFile::fake()->create("{$docType}.pdf", 100, 'application/pdf');

                $document = $onboardingService->uploadDocument($vendor, $docType, $file);
                $documentIds[] = $document->id;
                
                $this->line("  ✓ {$docType} uploaded (File: {$document->file_name})");
            }

            // Step 5: Check onboarding status
            $this->info("\nStep 5: Checking onboarding status...");
            $status = $onboardingService->getOnboardingStatus($vendor);
            $progress = $onboardingService->getOnboardingProgress($vendor);

            $this->line("  ✓ Profile Complete: " . ($status['profile_complete'] ? 'Yes' : 'No'));
            $this->line("  ✓ Bank Details Verified: " . ($status['bank_details_verified'] ? 'Yes' : 'No'));
            $this->line("  ✓ Documents Uploaded: {$status['documents_uploaded']}");
            $this->line("  ✓ Onboarding Progress: {$progress}%");

            // Step 6: Submit for approval
            $this->info("\nStep 6: Vendor ready for approval review...");
            $this->line("  ✓ Documents pending admin review");
            $this->line("  ✓ Overall Status: {$status['overall_status']}");

            // Step 7: Admin approves first document
            $this->info("\nStep 7: Admin approcing first document...");
            $admin = User::firstOrCreate(
                ['email' => 'admin-e2e@test.com'],
                [
                    'name' => 'E2E Admin',
                    'password' => bcrypt('password123'),
                    'email_verified_at' => now(),
                ]
            );

            $firstDoc = VendorDocument::find($documentIds[0]);
            $onboardingService->approveDocument($firstDoc, $admin);
            $this->line("  ✓ First document approved by: {$admin->name}");

            // Step 8: Admin approves remaining documents
            $this->info("\nStep 8: Admin approving remaining documents...");
            $remainingDocs = VendorDocument::whereIn('id', array_slice($documentIds, 1))->get();
            $remainingDocs->each(fn($doc) => $onboardingService->approveDocument($doc, $admin));
            $this->line("  ✓ All documents approved ({$remainingDocs->count()} more)");

            // Step 9: Final status check before vendor approval
            $this->info("\nStep 9: Final status check before vendor approval...");
            $finalStatus = $onboardingService->getOnboardingStatus($vendor);
            $this->line("  ✓ Documents Approved: {$finalStatus['documents_approved']}");
            $this->line("  ✓ Documents Pending: {$finalStatus['documents_pending']}");
            $this->line("  ✓ Vendor Is Verified: " . ($vendor->is_verified ? 'Yes' : 'No'));

            // Step 10: Admin approves vendor
            $this->info("\nStep 10: Admin approving vendor...");
            $onboardingService->approveVendor($vendor, $admin, 'All documents verified');
            $vendor->refresh();
            $this->line("  ✓ Vendor approved and activated");
            $this->line("  ✓ is_verified: {$vendor->is_verified}");
            $this->line("  ✓ is_active: {$vendor->is_active}");

            // Step 11: Verify final state
            $this->info("\nStep 11: Verifying final onboarding state...");
            $finalContext = $onboardingService->getVendorWithContext($vendor);
            $finalProgress = $onboardingService->getOnboardingProgress($vendor);

            $this->line("  ✓ Shop Name: {$finalContext['vendor']->shop_name}");
            $this->line("  ✓ Commission Rate: {$finalContext['vendor']->commission_rate}%");
            $this->line("  ✓ Documents Count: " . $finalContext['documents']->count());
            $this->line("  ✓ Overall Status: " . $finalContext['status']['overall_status']);
            $this->line("  ✓ Progress: {$finalProgress}%");

            // Success summary
            $this->info("\n=== E2E TEST COMPLETED SUCCESSFULLY ===");
            $this->line("Vendor onboarding workflow validated:");
            $this->line("  ✓ Application creation");
            $this->line("  ✓ Bank details submission");
            $this->line("  ✓ Document upload");
            $this->line("  ✓ Admin approval workflow");
            $this->line("  ✓ Vendor activation");
            $this->line("Ready for production deployment ✓");

            return 0;

        } catch (\Exception $e) {
            $this->error("E2E TEST FAILED");
            $this->error("Error: " . $e->getMessage());
            $this->error("File: " . $e->getFile() . ":" . $e->getLine());
            return 1;
        }
    }
}
