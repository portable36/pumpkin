<?php

namespace App\Livewire\Notifications;

use Livewire\Attributes\On;
use Livewire\Component;

class Toast extends Component
{
    public $show = false;
    public $message = '';
    public $type = 'success';

    #[On('notify')]
    public function showNotification($message, $type = 'success')
    {
        $this->message = is_array($message) ? $message['message'] : $message;
        $this->type = is_array($message) ? ($message['type'] ?? 'success') : $type;
        $this->show = true;

        $this->dispatch('toast-shown');
    }

    public function dismiss()
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.notifications.toast');
    }
}
