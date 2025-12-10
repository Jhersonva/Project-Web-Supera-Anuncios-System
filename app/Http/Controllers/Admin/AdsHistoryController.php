<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;

class AdsHistoryController extends Controller
{
    /**
     * Mostrar historial de anuncios
     */
    public function index(Request $request)
    {
        $query = Advertisement::with(['user', 'category', 'subcategory', 'images']);

        // FILTRO POR ESTADO
        if ($request->status) {
            if ($request->status === "publicado") {
                $query->where('published', 1);
            } elseif ($request->status === "expirado") {
                $query->where('expires_at', '<', now());
            }
        }

        // BUSCADOR
        if ($request->search) {
            $query->where('title', 'LIKE', "%{$request->search}%")
                ->orWhereHas('user', function ($q) use ($request) {
                    $q->where('full_name', 'LIKE', "%{$request->search}%"); 
                })
                ->orWhereHas('category', function ($q) use ($request) {
                    $q->where('name', 'LIKE', "%{$request->search}%");
                });
        }

        $ads = $query->latest()->paginate(10);

        return view('admin.ads-history.index', compact('ads'));
    }

    public function notifyUser($id, $status)
    {
        $ad = Advertisement::with('user')->findOrFail($id);

        if (!$ad->user->phone) {
            return back()->with('error', 'El usuario no tiene número de WhatsApp registrado.');
        }

        $phone = $ad->user->phone; // asegúrate que tu tabla tenga este campo

        // Mensajes personalizados
        $messages = [
            'pendiente' => "Hola {$ad->user->full_name}, tu anuncio '{$ad->title}' está en revisión y se encuentra pendiente de aprobación.",
            'publicado' => "¡Hola {$ad->user->full_name}! Tu anuncio '{$ad->title}' ha sido aprobado y ya está publicado 🎉.",
            'rechazado' => "Hola {$ad->user->full_name}, lamentamos informarte que tu anuncio '{$ad->title}' ha sido rechazado.",
            'expirado' => "Hola {$ad->user->full_name}, tu anuncio '{$ad->title}' ha expirado. Puedes renovarlo cuando desees."
        ];

        if (!isset($messages[$status])) {
            return back()->with('error', 'Estado no válido.');
        }

        $text = urlencode($messages[$status]);

        $whatsappUrl = "https://wa.me/51{$phone}?text={$text}";

        return redirect($whatsappUrl);
    }

    public function approve($id)
    {
        $ad = Advertisement::findOrFail($id);
        $ad->status = 'publicado';
        $ad->published = 1; 
        $ad->save();

        return back()->with('success', 'Anuncio aprobado correctamente.');
    }

    public function reject($id)
    {
        $ad = Advertisement::findOrFail($id);
        $ad->status = 'rechazado';
        $ad->published = 0; 
        $ad->save();

        return back()->with('success', 'Anuncio rechazado correctamente.');
    }

}