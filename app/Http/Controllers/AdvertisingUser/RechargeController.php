<?php

namespace App\Http\Controllers\AdvertisingUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recharge;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;

class RechargeController extends Controller
{
    public function index()
    {
        $recharges = Recharge::where('user_id', Auth::id())
            ->with('paymentMethod')
            ->orderBy('created_at', 'desc')
            ->get();

        $paymentMethods = PaymentMethod::where('activo', true)->get();

        return view('advertising_user.recharges.index', compact('recharges', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'monto'             => 'required|numeric|min:1',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'img_cap_pago'      => 'nullable|image|max:4096',
        ]);

        $rutaImagen = null;

        if ($request->hasFile('img_cap_pago')) {
            $uploadPath = public_path('images/recharges');

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $request->img_cap_pago->getClientOriginalExtension();
            $request->img_cap_pago->move($uploadPath, $filename);
            $rutaImagen = 'images/recharges/' . $filename;
        }

        Recharge::create([
            'user_id'           => Auth::id(),
            'monto'             => $request->monto,
            'payment_method_id' => $request->payment_method_id,
            'img_cap_pago'      => $rutaImagen,
            'status'            => 'pendiente',
        ]);

        return redirect()->back()
            ->with('success', 'Tu solicitud de recarga fue enviada correctamente.');
    }
}
    