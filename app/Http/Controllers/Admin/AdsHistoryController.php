<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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

    public function verify(Advertisement $ad)
    {
        $ad->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verification_requested' => false, 
        ]);

        return back()->with('success', 'Anuncio marcado como verificado.');
    }

    public function notifyUser($id, $status)
    {
        $ad = Advertisement::with('user')->findOrFail($id);

        $settings = SystemSetting::first();

        if (!$settings || !$settings->whatsapp_number) {
            return response()->json([
                'error' => 'El número de WhatsApp del sistema no está configurado.'
            ], 400);
        }

        $phone = preg_replace('/\D/', '', $settings->whatsapp_number); // limpia caracteres

        $receiptLink = $ad->receipt_file 
            ? asset($ad->receipt_file) 
            : null;

        $messages = [
            'pendiente' =>
                "Hola {$ad->user->full_name},\n\n".
                "Tu anuncio *{$ad->title}* está en revisión.",

            'publicado' =>
                "*¡Anuncio aprobado!*\n\n".
                "Hola {$ad->user->full_name}, tu anuncio *{$ad->title}* ya está publicado.\n\n".
                ($receiptLink ? "*Comprobante de pago:*\n{$receiptLink}\n\n" : "").
                "Gracias por confiar en nosotros.",

            'rechazado' =>
                "Hola {$ad->user->full_name},\n\n".
                "Tu anuncio *{$ad->title}* ha sido rechazado.",

            'expirado' =>
                "Hola {$ad->user->full_name},\n\n".
                "Tu anuncio *{$ad->title}* ha expirado."
        ];

        return response()->json([
            'phone' => $phone,
            'text'  => $messages[$status] ?? ''
        ]);
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
        DB::transaction(function () use ($id) {

            $ad = Advertisement::with(['user', 'subcategory'])->lockForUpdate()->findOrFail($id);

            // Evitar doble devolución
            if ($ad->refunded) {
                return;
            }

            // Total pagado REAL
            $totalPaid =
                ($ad->subcategory->price * $ad->days_active)
                + $ad->urgent_price
                + $ad->featured_price
                + $ad->premiere_price
                + $ad->semi_new_price
                + $ad->new_price
                + $ad->available_price
                + $ad->top_price;

            // Devolver saldo al usuario
            $user = $ad->user;
            $user->virtual_wallet += $totalPaid;
            $user->save();

            // Actualizar anuncio
            $ad->update([
                'status'    => 'rechazado',
                'published' => 0,
                'refunded'  => true,
            ]);

        });

        return back()->with('success', 'Anuncio rechazado y saldo devuelto al usuario.');
    }

    public function pendingCount()
    {
        $count = Advertisement::where('status', 'pendiente')->count();

        return response()->json([
            'count' => $count
        ]);
    }

}