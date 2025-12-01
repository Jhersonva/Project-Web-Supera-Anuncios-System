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

        $rutaImagen = null;

        // SUBIR IMAGEN MANUALMENTE COMO ANUNCIOS
        if ($request->hasFile('img_cap_pago')) {

            // Carpeta donde se guardarán las recargas
            $uploadPath = public_path('images/recharges');

            // Crear carpeta si no existe
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Nombre único
            $filename = time() . '_' . uniqid() . '.' . $request->img_cap_pago->getClientOriginalExtension();

            // Mover archivo
            $request->img_cap_pago->move($uploadPath, $filename);

            // Guardar ruta relativa a la base del public
            $rutaImagen = 'images/recharges/' . $filename;
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
