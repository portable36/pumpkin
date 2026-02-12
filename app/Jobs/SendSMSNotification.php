<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendSMSNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public string $message,
        public ?array $data = null
    ) {}

    public function handle(): void
    {
        // Send SMS notification using Twilio or similar
        
        $user->notifications()->create([
            'type' => 'sms',
            'title' => 'SMS Notification',
            'message' => $this->message,
            'data' => $this->data,
        ]);
    }
}
