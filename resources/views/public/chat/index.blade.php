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
                        <i class="fa-solid fa-user-circle fa-2x text-secondary"></i>
                    </div>

                    <div class="flex-grow-1">
                        <div class="fw-bold">{{ $otherUser->full_name }}</div>
                        <small class="text-muted">
                            {{ $conv->advertisement->title }}
                        </small>
                    </div>

                    <i class="fa-solid fa-chevron-right"></i>

                </a>
            @endforeach

        </div>
    @endif

</div>

@endsection
