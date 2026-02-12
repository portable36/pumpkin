@extends('layouts.app')

@section('title', 'Messages - Pumpkin')

@section('content')
<div style="display: flex; height: calc(100vh - 80px); gap: 0;">
    <!-- Conversations List -->
    <div style="width: 300px; background: white; border-right: 1px solid #ddd; display: flex; flex-direction: column; overflow: hidden;">
        <div style="padding: 1.5rem; border-bottom: 1px solid #ddd;">
            <h2 style="margin: 0;">Messages</h2>
            <input type="text" placeholder="Search conversations..." style="width: 100%; margin-top: 1rem; padding: 0.5rem;">
        </div>

        <div style="flex: 1; overflow-y: auto;">
            @forelse($conversations as $conversation)
                <div style="padding: 1rem; border-bottom: 1px solid #eee; cursor: pointer; background: {{ $conversation->id === $activeConversation?->id ? '#f0f0f0' : 'white' }}; transition: background 0.2s;" 
                     onclick="selectConversation({{ $conversation->id }})">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <strong>{{ $conversation->otherUser($conversation)->name }}</strong>
                        <span style="font-size: 0.8rem; color: #999;">{{ $conversation->updated_at->format('M d') }}</span>
                    </div>
                    <p style="color: #666; margin: 0.5rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ $conversation->messages()->latest()->first()?->message ?? 'No messages yet' }}
                    </p>
                </div>
            @empty
                <div style="padding: 2rem; text-align: center; color: #999;">
                    No conversations yet
                </div>
            @endforelse
        </div>
    </div>

    <!-- Chat Area -->
    <div style="flex: 1; display: flex; flex-direction: column; background: white;">
        @if($activeConversation)
            <!-- Chat Header -->
            <div style="padding: 1.5rem; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="margin: 0;">{{ $activeConversation->otherUser($activeConversation)->name }}</h3>
                    <p style="color: #666; margin: 0.5rem 0; font-size: 0.9rem;">🟢 Online</p>
                </div>
                <div>
                    <button class="btn btn-small btn-outline">📞 Call</button>
                    <button class="btn btn-small btn-outline">ℹ️ Info</button>
                </div>
            </div>

            <!-- Messages -->
            <div style="flex: 1; overflow-y: auto; padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                @foreach($activeConversation->messages()->oldest()->get() as $message)
                    <div style="display: flex; {{ $message->user_id === auth()->id() ? 'justify-content: flex-end;' : 'justify-content: flex-start;' }}">
                        <div style="max-width: 60%; padding: 0.75rem 1.5rem; border-radius: 8px; {{ $message->user_id === auth()->id() ? 'background: #667eea; color: white;' : 'background: #f0f0f0; color: #333;' }}">
                            <p style="margin: 0;">{{ $message->message }}</p>
                            <p style="font-size: 0.75rem; {{ $message->user_id === auth()->id() ? 'color: rgba(255,255,255,0.7);' : 'color: #999;' }} margin: 0.25rem 0 0 0;">
                                {{ $message->created_at->format('H:i') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Message Input -->
            <div style="padding: 1.5rem; border-top: 1px solid #ddd; display: flex; gap: 1rem;">
                <input type="text" id="message-input" placeholder="Type a message..." style="flex: 1; padding: 0.75rem;border: 1px solid #ddd; border-radius: 4px;">
                <button class="btn" onclick="sendMessage()">Send</button>
            </div>
        @else
            <div style="flex: 1; display: flex; align-items: center; justify-content: center; color: #999;">
                <div style="text-align: center;">
                    <p style="font-size: 2rem;">💬</p>
                    <p>Select a conversation to start messaging</p>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function selectConversation(id) {
    window.location.href = `/messages?conversation_id=${id}`;
}

function sendMessage() {
    const message = document.getElementById('message-input').value;
    if (message.trim() === '') return;
    
    const conversationId = {{ $activeConversation->id ?? 'null' }};
    if (!conversationId) return;

    fetch('/messages/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({
            conversation_id: conversationId,
            message: message,
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('message-input').value = '';
            // Reload messages
            location.reload();
        }
    });
}

// Auto-refresh messages every 3 seconds
setInterval(() => {
    location.reload();
}, 3000);
</script>

<style>
.btn-small {
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
    margin-left: 0.5rem;
}
</style>
@endsection
