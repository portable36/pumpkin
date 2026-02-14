<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;
use Illuminate\Support\Str;

class VendorOnboardingController extends Controller
{
    public function showRegistrationForm()
    {
        return view('vendor.onboarding.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'kyc_document' => 'required|file|mimes:pdf,jpg,png|max:5120',
            'bank_account' => 'required|string|max:50',
            'bank_name' => 'required|string|max:100',
        ]);

        $user = auth()->user();

        // Check if vendor already exists
        if ($user->vendors ()->exists()) {
            return redirect()->back()->withErrors(['vendor' => 'You already have a vendor account.']);
        }

        // Upload KYC document
        $kycPath = $request->file('kyc_document')->store('kyc_documents', 'public');

        // Create vendor
        $vendor = Vendor::create([
            'user_id' => $user->id,
            'owner_id' => $user->id,
            'store_name' => $validated['store_name'],
            'shop_name' => $validated['store_name'],
            'slug' => Str::slug($validated['store_name']) . '-' . substr(uniqid(), -6),
            'description' => $validated['description'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'kyc_document' => $kycPath,
            'status' => 'pending',
            'kyc_status' => 'pending',
        ]);

        // Store bank details if available
        if (isset($validated['bank_account'])) {
            $vendor->bankDetails()->create([
                'account_number' => $validated['bank_account'],
                'bank_name' => $validated['bank_name'],
            ]);
        }

        // Assign vendor role
        $user->assignRole('vendor');

        return redirect()->route('vendor.dashboard')->with('success', 'Vendor registration submitted. Awaiting approval.');
    }

    public function showDashboard()
    {
        $vendor = auth()->user()->vendors()->first();

        if (!$vendor) {
            return redirect()->route('vendor.register.form')->with('info', 'Please register as vendor first.');
        }

        return view('vendor.dashboard', [
            'vendor' => $vendor,
            'status' => $vendor->status,
            'kyc_status' => $vendor->kyc_status,
            'products_count' => $vendor->products()->count(),
            'orders_count' => $vendor->orders()->count(),
        ]);
    }
}
