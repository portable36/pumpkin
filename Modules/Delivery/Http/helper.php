<?php

use App\Models\OrderStatus;
use Illuminate\Support\Facades\DB;
use Modules\Delivery\Entities\DeliveryMan;

if (! function_exists('getOrderStatusId')) {
    /**
     * Returns the order status id by slug
     *
     * @param  string  $slug
     * @return id
     */
    function getOrderStatusId($slug)
    {
        return OrderStatus::getAll()->where('slug', $slug)->pluck('id')->first();
    }
}

if (! function_exists('getOrderStatusIds')) {
    /**
     * Returns the order status id by slug
     *
     * @param  array  $slug
     * @return id
     */
    function getOrderStatusIds($slug): array
    {
        return OrderStatus::getAll()->whereIn('slug', $slug)->pluck('id')->all();
    }
}

if (! function_exists('getDeliveryManId')) {
    /**
     * Returns the order status id by slug
     *
     * @param  string  $slug
     * @return id
     */
    function getDeliveryManId($userId)
    {
        return DeliveryMan::getAll()->where('user_id', $userId)->pluck('id')->first();
    }
}

if (! function_exists('getOrderStatuses')) {
    /**
     * Returns the order statuses
     *
     * @return mixed
     */
    function getOrderStatuses()
    {
        return OrderStatus::getAll()->sortBy('order_by');
    }
}

if (! function_exists('getDeliveryMan')) {
    /**
     * Returns delivery man
     *
     * @return mixed
     */
    function getDeliveryMan($userId)
    {
        return DeliveryMan::getAll()->where('user_id', $userId)->first();
    }
}

if (! function_exists('isCarrierCommissionable')) {
    /**
     * Check carrier commission
     */
    function isCarrierCommissionable(string|int $deliveredStatusId, string|int $orderStatusId): bool
    {
        $isCommissionType = preference('payment_type_delivery_man') == 'commission';
        $isFlatType = preference('payment_type_delivery_man') == 'flat';

        return ($isCommissionType || $isFlatType) && $deliveredStatusId == $orderStatusId;
    }
}

if (! function_exists('getTemplate')) {

    /**
     * Retrieves the email template for the given language and slug.
     * If the language is not available, it will fallback to the default language (English).
     *
     * @param  string|null  $langName  The language short name
     * @param  string|null  $slug  The email template slug
     * @return object|null The email template object
     */
    function getTemplate(?string $langName = null, ?string $slug = null): ?object
    {
        if (empty($slug)) {
            return null;
        }

        $languageId = DB::table('languages')->where('short_name', $langName)->value('id');

        if (is_null($languageId)) {
            return null;
        }

        // Retrieve the email template
        $template = DB::table('email_templates')
            ->where('status', 'Active')
            ->where('slug', $slug)
            ->whereIn('language_id', [$languageId, 1])
            ->orderByDesc('language_id')
            ->first();

        return $template ?? null;
    }
}

add_filter('order_view_sections', function ($sections) {
    $sections['delivery'] = [
        'is_main' => false,
        'position' => '95',
        'visibility' => auth()->user()->role()->type == 'vendor' && preference('vendor_can_assign_delivery_man') == 0 ? false : true,
        'content' => 'delivery::admin.orders.delivery',
        'vendor_content' => 'delivery::vendor.orders.delivery',
    ];

    return $sections;
});
