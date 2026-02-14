<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

class NotificationController extends Controller
{
    /**
     * Get user's notifications
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->update(['read_at' => now()]);

        return response()->json(['message' => 'Notification marked as read']);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        auth()->user()->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    /**
     * Delete notification
     */
    public function delete(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->delete();

        return response()->json(['message' => 'Notification deleted']);
    }

    /**
     * Send order notification
     */
    public static function sendOrderNotification(Order $order, $type = 'created')
    {
        $user = $order->user;
        $title = '';
        $message = '';
        $action_url = route('orders.show', $order->id);

        switch ($type) {
            case 'created':
                $title = 'Order Confirmed';
                $message = "Your order #{$order->id} has been confirmed. Total: \${$order->total_amount}";
                break;
            case 'paid':
                $title = 'Payment Received';
                $message = "Payment for order #{$order->id} has been received.";
                break;
            case 'shipped':
                $title = 'Order Shipped';
                $message = "Your order #{$order->id} has been shipped. Tracking: {$order->tracking_number}";
                break;
            case 'delivered':
                $title = 'Order Delivered';
                $message = "Your order #{$order->id} has been delivered.";
                break;
            case 'cancelled':
                $title = 'Order Cancelled';
                $message = "Your order #{$order->id} has been cancelled.";
                break;
        }

        // Create database notification
        Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => 'order_' . $type,
            'action_url' => $action_url,
            'data' => json_encode(['order_id' => $order->id, 'type' => $type]),
        ]);

        // Queue email notification
        if (settings('email_notifications_enabled', true)) {
            Queue::dispatch(new \App\Jobs\SendNotificationEmail($user, $title, $message));
        }

        // Queue SMS notification
        if (settings('sms_notifications_enabled', false) && $user->phone) {
            Queue::dispatch(new \App\Jobs\SendSMSNotification($user, $message));
        }

        // Queue PWA push notification
        if (settings('push_notifications_enabled', true)) {
            self::sendPushNotification($user, $title, $message);
        }
    }

    /**
     * Send push notification (PWA)
     */
    public static function sendPushNotification($user, $title, $message, $icon = null)
    {
        // Implement PWA push notification
        // Store in queue for background processing
        if ($user->push_subscription) {
            Queue::dispatch(new \App\Jobs\SendPushNotification($user, $title, $message, $icon));
        }
    }

    /**
     * Send stock notification
     */
    public static function sendLowStockNotification($product)
    {
        // Notify vendor about low stock
        $vendor = $product->vendor;
        $title = 'Low Stock Alert';
        $message = "Product '{$product->name}' is running low on stock ({$product->stock_quantity} remaining).";

        Notification::create([
            'user_id' => $vendor->owner_id,
            'title' => $title,
            'message' => $message,
            'type' => 'inventory_alert',
            'action_url' => route('products.edit', $product->id),
            'data' => json_encode(['product_id' => $product->id]),
        ]);
    }

    /**
     * Send price drop notification
     */
    public static function sendPriceDropNotification($product, $oldPrice, $newPrice)
    {
        // Notify users who added product to wishlist about price drop
        $wishlistUsers = $product->wishlistUsers()->get();

        foreach ($wishlistUsers as $user) {
            $title = 'Price Drop!';
            $message = "{$product->name} price dropped from \${$oldPrice} to \${$newPrice}.";

            Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
                'type' => 'price_alert',
                'action_url' => route('products.show', $product->id),
                'data' => json_encode(['product_id' => $product->id, 'old_price' => $oldPrice, 'new_price' => $newPrice]),
            ]);

            self::sendPushNotification($user, $title, $message);
        }
    }

    /**
     * Subscribe to push notifications
     */
    public function subscribeToPush(Request $request)
    {
        $request->validate([
            'subscription' => 'required|json',
        ]);

        auth()->user()->update([
            'push_subscription' => $request->get('subscription'),
        ]);

        return response()->json(['message' => 'Subscribed to push notifications']);
    }

    /**
     * Get unread notification count
     */
    public function unreadCount()
    {
        $count = auth()->user()->notifications()
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Preferences
     */
    public function preferences()
    {
        $user = auth()->user();
        return view('settings.notifications', compact('user'));
    }

    /**
     * Update preferences
     */
    public function updatePreferences(Request $request)
    {
        $user = auth()->user();
        $user->update([
            'email_notifications' => $request->boolean('email_notifications'),
            'sms_notifications' => $request->boolean('sms_notifications'),
            'push_notifications' => $request->boolean('push_notifications'),
            'newsletter_subscription' => $request->boolean('newsletter_subscription'),
        ]);

        return redirect()->back()->with('success', 'Notification preferences updated');
    }
}
