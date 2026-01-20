<?php

namespace App\Http\Controllers\AdvertisingUser;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\AdCategory;
use App\Models\Advertisement;
use App\Models\ValueFieldAd;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();

        $ads = Advertisement::with(['category', 'subcategory', 'mainImage'])
            ->where('user_id', $user->id)

            // Buscar por título
            ->when($request->search, function ($q) use ($request) {
                $q->where('title', 'LIKE', '%' . $request->search . '%');
            })

            // Filtrar por estado
            ->when($request->status, function ($q) use ($request) {

                if ($request->status === 'expirado') {
                    $q->where('expires_at', '<', now());

                } elseif ($request->status === 'draft') {
                    $q->where('status', 'draft');

                } else {
                    $q->where('status', $request->status)
                    ->where(function ($q2) {
                        $q2->whereNull('expires_at')
                            ->orWhere('expires_at', '>=', now());
                    });
                }
            })

            ->orderByDesc('created_at')

            // PAGINACIÓN (10)
            ->paginate(10)

            // Mantener filtros
            ->appends($request->query());

        return view('advertising_user.my_ads.index', compact('ads'));
    }

    public function requestVerification(Advertisement $ad)
    {
        if ($ad->user_id !== auth()->id()) {
            abort(403);
        }

        $ad->update([
            'verification_requested' => true
        ]);

        return back()->with('success', 'Solicitud de verificación enviada.');
    }

    public function show($slug, $id)
    {
        $ad = Advertisement::with([
            'category',
            'subcategory',
            'images',
            'fields_values.field',
            'user'
        ])->findOrFail($id);

        return view('public.advertisement-detail', compact('ad'));
    }
}
