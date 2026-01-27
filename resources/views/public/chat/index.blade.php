@extends('layouts.app')

@section('title', 'Mis Chats')

@section('content')

<div class="container mt-4 mb-5">

    <h4 class="fw-bold mb-3">Chats</h4>

    @if($conversations->isEmpty())
        <p class="text-muted text-center">No tienes conversaciones aún.</p>
    @else
        <div class="list-group shadow-sm">

            @foreach($conversations as $conv)
                @php
                    $otherUser = auth()->id() === $conv->sender_id
                        ? $conv->receiver
                        : $conv->sender;
                @endphp

                <a href="{{ route('chat.show', $conv->id) }}" 
                    class="list-group-item list-group-item-action d-flex align-items-center">

                    <div class="me-3">
                        <img
                        src="{{ $otherUser->profile_image
                            ? asset($otherUser->profile_image)
                            : asset('assets/img/profile-image/default-user.png') }}"
                        class="rounded-circle border border-2 border-danger"
                        style="width:42px; height:42px; object-fit:cover;"
                        alt="Perfil">

                    </div>

                    <div class="flex-grow-1">
                        <div class="fw-bold">{{ $otherUser->full_name }}</div>
                        <small class="text-muted">
                            {{ $conv->advertisement->title }}
                        </small>
                    </div>

                    @if($conv->messages->count() > 0)
                        <span class="badge bg-danger rounded-pill ms-2">
                            {{ $conv->messages->count() }}
                        </span>
                    @endif

                    <i class="fa-solid fa-chevron-right ms-2"></i>

                </a>
            @endforeach

        </div>
    @endif

</div>

<script>
let lastConversationId = {{ $conversations->max('id') ?? 0 }};


function checkNewConversations() {
    fetch(`/chat/check-new?last_id=` + lastConversationId)
        .then(res => res.json())
        .then(data => {

            if (data.conversations.length > 0) {

                const listGroup = document.querySelector(".list-group");

                data.conversations.forEach(conv => {

                    if (conv.id > lastConversationId) {
                        lastConversationId = conv.id;
                    }

                    const otherUser = conv.sender_id == {{ auth()->id() }}
                        ? conv.receiver
                        : conv.sender;

                    let html = `
                        <a href="/chat/${conv.id}" 
                            class="list-group-item list-group-item-action d-flex align-items-center">

                            <div class="me-3">
                                <i class="fa-solid fa-user-circle fa-2x text-secondary"></i>
                            </div>

                            <div class="flex-grow-1">
                                <div class="fw-bold">${otherUser.full_name}</div>
                                <small class="text-muted">
                                    ${conv.advertisement.title}
                                </small>
                            </div>

                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    `;

                    listGroup.innerHTML = html + listGroup.innerHTML; 
                });
            }
        });
}

// Verificar cada 3 segundos
setInterval(checkNewConversations, 3000);
</script>


@endsection
