<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Helpers\Settings;

class AdminSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Show settings dashboard
     */
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('admin.settings', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_tagline' => 'required|string|max:255',
            'admin_email' => 'required|email',
            'support_email' => 'required|email',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'standard_shipping' => 'required|numeric|min:0',
            'express_shipping' => 'required|numeric|min:0',
            'overnight_shipping' => 'required|numeric|min:0',
            'vendor_approval_required' => 'required|in:yes,no',
            'product_approval_required' => 'required|in:yes,no',
            'user_registration_enabled' => 'required|in:yes,no',
            'vendor_registration_enabled' => 'required|in:yes,no',
            'max_products_per_vendor' => 'required|integer|min:1',
            'min_order_amount' => 'required|numeric|min:0',
            'payment_methods' => 'required|string',
            'currency_symbol' => 'required|string|max:5',
            'timezone' => 'required|string',
        ]);

        // Update all settings
        foreach ($request->all() as $key => $value) {
            if ($key !== '_token') {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        // Clear cached settings
        Settings::clearCache();

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }

    /**
     * Get setting by key
     */
    public static function get($key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Show statistics
     */
    public function stats()
    {
        $stats = [
            'total_users' => \App\Models\User::where('is_vendor', false)->count(),
            'total_vendors' => \App\Models\User::where('is_vendor', true)->count(),
            'pending_vendors' => \App\Models\Vendor::where('status', 'pending')->count(),
            'total_products' => \App\Models\Product::count(),
            'low_stock_products' => \App\Models\Product::where('stock', '<', 10)->count(),
            'total_orders' => \App\Models\Order::count(),
            'pending_orders' => \App\Models\Order::where('status', 'pending')->count(),
            'total_revenue' => \App\Models\Order::sum('total_amount'),
            'today_revenue' => \App\Models\Order::whereDate('created_at', today())->sum('total_amount'),
            'this_month_revenue' => \App\Models\Order::whereMonth('created_at', now()->month)->sum('total_amount'),
        ];

        return view('admin.stats', compact('stats'));
    }

    /**
     * Show activity log
     */
    public function activityLog()
    {
        // You can implement activity logging later
        $activities = [];
        return view('admin.activity-log', compact('activities'));
    }
}
