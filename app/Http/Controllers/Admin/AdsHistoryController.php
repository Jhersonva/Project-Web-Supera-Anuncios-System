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

        $ads = $query->latest()->paginate(2);

        return view('admin.ads-history.index', compact('ads'));
    }
}