<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\User;
use Illuminate\Http\Request;

class AdsRequestController extends Controller
{
    /**
     * Lista de anuncios pendientes
     */
    public function index()
    {
        $ads = Advertisement::where('status', 'pendiente')
            ->with(['user', 'category', 'subcategory'])
            ->latest()
            ->get();

        return view('admin.ads-requests.index', compact('ads'));
    }

    /**
     * Aprobar anuncio
     */
    public function approve($id)
    {
        $ad = Advertisement::findOrFail($id);

        $ad->published = 1;
        $ad->status = 'aceptado';
        $ad->save();

        return back()->with('success', 'El anuncio fue aprobado y publicado.');
    }

    /**
     * Rechazar anuncio y devolver dinero
     */
    public function reject($id)
    {
        $ad = Advertisement::findOrFail($id);
        $user = $ad->user;

        // devolver dinero
        $user->virtual_wallet += $ad->price;
        $user->save();

        $ad->status = 'rechazado';
        $ad->published = 0;
        $ad->save();

        return back()->with('error', 'El anuncio fue rechazado y se devolvió el saldo al usuario.');
    }
}