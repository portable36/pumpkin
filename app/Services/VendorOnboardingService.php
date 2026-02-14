<?php

namespace App\Services;

use App\Models\Vendor;
use App\Models\VendorBankDetail;
use App\Models\VendorDocument;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

/**
 * Vendor Onboarding Service
 * Manages the complete KYC & onboarding workflow for vendors
 */
class VendorOnboardingService
{
    /**
     * Create vendor from application
     */
    public function createVendorApplication(User $user, array $data): Vendor
    {
        $vendor = Vendor::create([
            'owner_id' => $user->id,
            'shop_name' => $data['shop_name'],
            'slug' => $this->generateSlug($data['shop_name']),
            'email' => $data['email'] ?? $user->email,
            'phone' => $data['phone'],
            'description' => $data['description'] ?? '',
            'address' => $data['address'],
            'city' => $data['city'],
            'country' => $data['country'] ?? 'Bangladesh',
            'postal_code' => $data['postal_code'] ?? '',
            'is_verified' => false,
            'is_active' => false,
            'commission_rate' => Setting::get('commission.default_rate', 10),
        ]);

        Log::info("Vendor application created", [
            'vendor_id' => $vendor->id,
            'owner_id' => $user->id,
            'shop_name' => $vendor->shop_name,
        ]);

        return $vendor;
    }

    /**
     * Store bank details for vendor
     */
    public function storeBankDetails(Vendor $vendor, array $data): VendorBankDetail
    {
        $bankDetail = VendorBankDetail::updateOrCreate(
            ['vendor_id' => $vendor->id],
            [
                'account_holder_name' => $data['account_holder_name'],
                'bank_name' => $data['bank_name'],
                'account_number' => $this->encryptSensitive($data['account_number']),
                'routing_number' => $data['routing_number'] ?? null,
                'swift_code' => $data['swift_code'] ?? null,
                'iban' => $data['iban'] ?? null,
                'is_verified' => false,
            ]
        );

        Log::info("Bank details stored for vendor", [
            'vendor_id' => $vendor->id,
            'bank_name' => $bankDetail->bank_name,
        ]);

        return $bankDetail;
    }

    /**
     * Upload KYC document
     */
    public function uploadDocument(Vendor $vendor, string $documentType, object $file): VendorDocument
    {
        if (!isset(VendorDocument::DOCUMENT_TYPES[$documentType])) {
            throw new \Exception("Invalid document type: {$documentType}");
        }

        // Store file
        $storagePath = "vendors/{$vendor->id}/documents/{$documentType}";
        $filePath = $file->store($storagePath, 'private');

        // Create document record
        $document = VendorDocument::create([
            'vendor_id' => $vendor->id,
            'document_type' => $documentType,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'status' => 'pending',
        ]);

        Log::info("Document uploaded for vendor", [
            'vendor_id' => $vendor->id,
            'document_type' => $documentType,
            'file_path' => $filePath,
        ]);

        return $document;
    }

    /**
     * Get onboarding status
     */
    public function getOnboardingStatus(Vendor $vendor): array
    {
        $bankDetail = $vendor->bankDetails ?? null;
        $documents = VendorDocument::where('vendor_id', $vendor->id)->get();
        $requiredDocuments = Setting::get('onboarding.required_documents', [
            'nid',
            'trade_license',
            'owner_id',
        ]);

        $uploadedDocuments = $documents->keyBy('document_type');
        $approvedDocuments = $documents->where('status', 'approved')->count();
        $rejectedDocuments = $documents->where('status', 'rejected');

        $status = [
            'vendor_id' => $vendor->id,
            'overall_status' => $this->calculateOverallStatus($vendor, $documents),
            'profile_complete' => $this->isProfileComplete($vendor),
            'bank_details_verified' => $bankDetail && $bankDetail->is_verified,
            'documents_uploaded' => $documents->count(),
            'documents_approved' => $approvedDocuments,
            'documents_pending' => $documents->where('status', 'pending')->count(),
            'documents_rejected' => $rejectedDocuments->count(),
            'missing_documents' => array_diff(
                $requiredDocuments,
                $uploadedDocuments->keys()->toArray()
            ),
            'rejected_documents' => $rejectedDocuments->map(function ($doc) {
                return [
                    'type' => $doc->document_type,
                    'reason' => $doc->rejection_reason,
                ];
            })->toArray(),
            'is_approved' => $vendor->is_verified,
            'is_active' => $vendor->is_active,
        ];

        return $status;
    }

    /**
     * Calculate overall onboarding status
     */
    protected function calculateOverallStatus(Vendor $vendor, $documents): string
    {
        if ($vendor->is_verified) {
            return 'approved';
        }

        if (!$this->isProfileComplete($vendor)) {
            return 'profile_incomplete';
        }

        if ($documents->count() === 0) {
            return 'documents_pending';
        }

        if ($documents->where('status', 'rejected')->count() > 0) {
            return 'documents_rejected';
        }

        if ($documents->where('status', 'pending')->count() > 0) {
            return 'under_review';
        }

        return 'pending_approval';
    }

    /**
     * Check if vendor profile is complete
     */
    protected function isProfileComplete(Vendor $vendor): bool
    {
        return !empty($vendor->shop_name) &&
               !empty($vendor->email) &&
               !empty($vendor->phone) &&
               !empty($vendor->address) &&
               !empty($vendor->city) &&
               $vendor->bankDetails && 
               !empty($vendor->bankDetails->account_holder_name);
    }

    /**
     * Approve vendor (admin)
     */
    public function approveVendor(Vendor $vendor, User $approver, string $notes = null): void
    {
        $vendor->update([
            'is_verified' => true,
            'is_active' => true,
        ]);

        Log::info("Vendor approved", [
            'vendor_id' => $vendor->id,
            'approved_by' => $approver->id,
            'notes' => $notes,
        ]);

        // Approve all pending documents
        VendorDocument::where('vendor_id', $vendor->id)
            ->where('status', 'pending')
            ->get()
            ->each(fn($doc) => $doc->approve($approver->id));
    }

    /**
     * Reject vendor (admin)
     */
    public function rejectVendor(Vendor $vendor, User $rejector, string $reason): void
    {
        $vendor->update([
            'is_verified' => false,
            'is_active' => false,
        ]);

        Log::warning("Vendor rejected", [
            'vendor_id' => $vendor->id,
            'rejected_by' => $rejector->id,
            'reason' => $reason,
        ]);
    }

    /**
     * Approve document (admin)
     */
    public function approveDocument(VendorDocument $document, User $approver): void
    {
        $document->approve($approver->id);

        Log::info("Document approved", [
            'vendor_id' => $document->vendor_id,
            'document_type' => $document->document_type,
            'approved_by' => $approver->id,
        ]);
    }

    /**
     * Reject document (admin)
     */
    public function rejectDocument(VendorDocument $document, User $rejector, string $reason): void
    {
        $document->reject($reason, $rejector->id);

        Log::info("Document rejected", [
            'vendor_id' => $document->vendor_id,
            'document_type' => $document->document_type,
            'reason' => $reason,
            'rejected_by' => $rejector->id,
        ]);
    }

    /**
     * Get onboarding progress percentage
     */
    public function getOnboardingProgress(Vendor $vendor): int
    {
        $status = $this->getOnboardingStatus($vendor);
        
        $profileComplete = $status['profile_complete'] ? 30 : 0;
        $bankDetailsVerified = $status['bank_details_verified'] ? 20 : 0;
        $documentsApproved = min(($status['documents_approved'] / 3) * 40, 40);
        $approved = $vendor->is_verified ? 10 : 0;

        return (int) min($profileComplete + $bankDetailsVerified + $documentsApproved + $approved, 100);
    }

    /**
     * Get all pending vendors for admin review
     */
    public function getPendingVendors(int $limit = 50)
    {
        return Vendor::where('is_verified', false)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->with('owner', 'bankDetails', 'documents')
            ->get();
    }

    /**
     * Generate URL-friendly slug
     */
    protected function generateSlug(string $name): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        $count = Vendor::where('slug', 'like', "{$slug}%")->count();
        return $count > 0 ? "{$slug}-{$count}" : $slug;
    }

    /**
     * Encrypt sensitive data
     */
    protected function encryptSensitive(string $data): string
    {
        return encrypt($data);
    }

    /**
     * Get vendor with full onboarding context
     */
    public function getVendorWithContext(Vendor $vendor): array
    {
        return [
            'vendor' => $vendor,
            'owner' => $vendor->owner,
            'bank_details' => $vendor->bankDetails,
            'documents' => VendorDocument::where('vendor_id', $vendor->id)->get(),
            'status' => $this->getOnboardingStatus($vendor),
            'progress' => $this->getOnboardingProgress($vendor),
        ];
    }
}
