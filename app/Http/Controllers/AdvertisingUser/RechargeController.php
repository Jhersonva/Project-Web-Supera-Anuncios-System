<?php

namespace App\Http\Controllers\AdvertisingUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recharge;
use Illuminate\Support\Facades\Auth;

class RechargeController extends Controller
{
    public function index()
    {
        $recharges = Recharge::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('advertising_user.recharges.index', compact('recharges'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'monto'        => 'required|numeric|min:1',
            'metodo_pago'  => 'required|string',
            'img_cap_pago' => 'nullable|image|max:4096',
        ]);

        // Subir imagen si existe
        $rutaImagen = null;
        if ($request->hasFile('img_cap_pago')) {
            $rutaImagen = $request->file('img_cap_pago')->store('comprobantes', 'public');
        }

        Recharge::create([
            'user_id'      => Auth::id(),
            'monto'        => $request->monto,
            'metodo_pago'  => $request->metodo_pago,
            'img_cap_pago' => $rutaImagen,
            'status'       => 'pendiente',
        ]);

        return redirect()->back()->with('success', 'Tu solicitud de recarga fue enviada. Espera la aprobación del administrador.');
    }
}
