@extends('layouts.app')

@section('title', 'Chat')

@section('content')

<style>
    .chat-container {
        height: calc(100vh - 160px);
        display: flex;
        flex-direction: column;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #ddd;
    }

    .chat-messages {
        flex-grow: 1;
        overflow-y: auto;
        padding: 15px;
        background: #f7f7f7;
    }

    .msg {
        max-width: 75%;
        padding: 10px 14px;
        border-radius: 15px;
        margin-bottom: 10px;
        font-size: 15px;
        line-height: 1.4;
        display: inline-block;
    }

    .msg-left {
        background: #ffffff;
        border: 1px solid #ddd;
        align-self: flex-start;
    }

    .msg-right {
        background: #0d6efd;
        color: white;
        align-self: flex-end;
    }

    .chat-input {
        padding: 15px;
        background: white;
        border-top: 1px solid #ddd;
    }

    .chat-header {
        padding: 15px;
        background: white;
        border-bottom: 1px solid #ddd;
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>

<div class="container mt-3 mb-5">

    <div class="chat-container shadow-sm">

        <!-- HEADER -->
        <div class="chat-header">
            <a href="{{ route('chat.index') }}" class="me-2">
                <i class="fa-solid fa-arrow-left fs-4"></i>
            </a>

            <i class="fa-solid fa-user-circle fa-2x text-secondary"></i>

            <div>
                @php
                    $other = auth()->id() === $conversation->sender_id
                        ? $conversation->receiver
                        : $conversation->sender;
                @endphp

                <div class="fw-bold">{{ $other->full_name }}</div>
                <small class="text-muted">{{ $conversation->advertisement->title }}</small>
            </div>
        </div>

        <!-- MENSAJES -->
        <div class="chat-messages" id="chatBox">

            @foreach($conversation->messages as $msg)
                <div class="d-flex {{ $msg->user_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                    <div class="msg {{ $msg->user_id == auth()->id() ? 'msg-right' : 'msg-left' }}">
                        {{ $msg->message }}
                        <br>
                        <small class="text-muted" style="font-size: 11px;">
                            {{ $msg->created_at->format('H:i') }}
                        </small>
                    </div>
                </div>
            @endforeach

        </div>

        <!-- INPUT -->
        <form id="chatForm">
            @csrf
            <div class="input-group">
                <input type="text" id="msgInput" name="message" class="form-control" required>
                <button class="btn btn-primary">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
        </form>


    </div>

</div>

<script>
    // Auto-scroll inicial
    const chatBox = document.getElementById('chatBox');
    chatBox.scrollTop = chatBox.scrollHeight;

    document.getElementById('chatForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const btn = this.querySelector('button'); 
        const message = document.getElementById('msgInput').value;

        if (!message.trim()) return; 

        // Deshabilitar el botón mientras se envía
        btn.disabled = true;

        fetch(`/chat/{{ $conversation->id }}/send`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message }),
        })
        .then(() => {
            document.getElementById('msgInput').value = "";
            loadMessages(); 
        })
        .finally(() => {
            btn.disabled = false; 
        });
    });


    const conversationId = {{ $conversation->id }};
    let lastMessageId = {{ $conversation->messages->last()->id ?? 0 }};

    function loadMessages() {
        fetch(`/chat/${conversationId}/messages?last_id=` + lastMessageId)
            .then(res => res.json())
            .then(data => {

                if (data.messages.length > 0) {

                    data.messages.forEach(msg => {
                        lastMessageId = msg.id;

                        const side = msg.user_id == {{ auth()->id() }} 
                            ? 'justify-content-end' 
                            : 'justify-content-start';

                        const msgType = msg.user_id == {{ auth()->id() }}
                            ? 'msg-right'
                            : 'msg-left';

                        chatBox.innerHTML += `
                            <div class="d-flex ${side}">
                                <div class="msg ${msgType}">
                                    ${msg.message}
                                    <br>
                                    <small class="text-muted" style="font-size: 11px;">
                                        ${(new Date(msg.created_at)).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}
                                    </small>
                                </div>
                            </div>
                        `;
                    });

                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            });
    }

    // Refrescar cada 2 segundos
    setInterval(loadMessages, 2000);
</script>


@endsection
