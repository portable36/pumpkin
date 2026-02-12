<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendNotificationEmail implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public string $subject,
        public string $message,
        public ?array $data = null
    ) {}

    public function handle(): void
    {
        // Send email notification
        // This would integrate with Mail facade
        
        $user->notifications()->create([
            'type' => 'email',
            'title' => $this->subject,
            'message' => $this->message,
            'data' => $this->data,
        ]);
    }
}
