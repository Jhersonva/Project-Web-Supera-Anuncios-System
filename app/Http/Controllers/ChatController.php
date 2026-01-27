<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ChatController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $conversations = Conversation::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with([
                'sender',
                'receiver',
                'advertisement',
                'messages' => function ($q) use ($userId) {
                    $q->where('is_read', false)
                    ->where('user_id', '!=', $userId);
                }
            ])
            ->latest()
            ->get();

        return view('public.chat.index', compact('conversations'));
    }

    public function show($id)
    {
        $conversation = Conversation::with('messages.user')->findOrFail($id);

        if (!in_array(auth()->id(), [$conversation->sender_id, $conversation->receiver_id])) {
            abort(403);
        }

        // Marcar mensajes como leídos
        Message::where('conversation_id', $conversation->id)
            ->where('user_id', '!=', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('public.chat.show', compact('conversation'));
    }

    public function startConversation(Request $request, $adId)
    {
        $ad = Advertisement::findOrFail($adId);

        $senderId = auth()->id();
        $receiverId = $ad->user_id;

        if ($senderId === $receiverId) {
            return back()->with('error', 'No puedes enviarte mensajes a ti mismo.');
        }

        // Buscar si ya existe una conversación previa
        $conversation = Conversation::where('sender_id', $senderId)
            ->where('receiver_id', $receiverId)
            ->where('advertisement_id', $adId)
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'sender_id'        => $senderId,
                'receiver_id'      => $receiverId,
                'advertisement_id' => $adId
            ]);
        }

        // Crear primer mensaje
        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $senderId,
            'message' => $request->message
        ]);

        return redirect()->route('chat.show', $conversation->id)
            ->with('success', 'Conversación iniciada correctamente.');

    }

    public function sendMessage(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);

        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        return back()->with('success', 'Mensaje enviado.');

    }

    public function getMessages($id, Request $request)
    {
        $conversation = Conversation::findOrFail($id);

        if (!in_array(auth()->id(), [$conversation->sender_id, $conversation->receiver_id])) {
            abort(403);
        }

        $lastId = $request->last_id;

        $messages = Message::where('conversation_id', $id)
                        ->where('id', '>', $lastId)
                        ->get();

        return response()->json([
            'messages' => $messages
        ]);
    }

    // Obtener nuevas conversaciones
    public function checkNewConversations(Request $request)
    {
        $lastConversationId = $request->last_id;
        $userId = auth()->id();

        // Enviar SOLO conversaciones nuevas
        $newConversations = Conversation::where('id', '>', $lastConversationId)
            ->where(function($q) use ($userId) {
                $q->where('sender_id', $userId)
                ->orWhere('receiver_id', $userId);
            })
            ->with(['sender', 'receiver', 'advertisement'])
            ->get();

        return response()->json([
            'conversations' => $newConversations
        ]);
    }

    public function unreadCount()
    {
        $userId = auth()->id();

        $count = Message::where('is_read', false)
            ->whereHas('conversation', function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                ->orWhere('receiver_id', $userId);
            })
            ->where('user_id', '!=', $userId) 
            ->count();

        return response()->json([
            'count' => $count
        ]);
    }

}
