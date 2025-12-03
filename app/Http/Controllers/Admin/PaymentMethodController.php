<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /* -----------------------------------------------------
       LISTA GENERAL — Para panel de administración
    ----------------------------------------------------- */
    public function index()
    {
        $methods = PaymentMethod::orderBy('id', 'desc')->get();
        return view('admin.config.payment_methods.index', compact('methods'));
    }

    /* -----------------------------------------------------
       FORMULARIO CREAR
    ----------------------------------------------------- */
    public function create()
    {
        return view('admin.config.payment_methods.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo'   => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:255',
            'cuenta' => 'nullable|string|max:255',
            'cci'    => 'nullable|string|max:255',
            'qr'     => 'nullable|image|max:4096',
        ]);

        $rutaQr = null;

        if ($request->hasFile('qr')) {
            $uploadPath = public_path('images/payment_methods');

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $request->qr->getClientOriginalExtension();
            $request->qr->move($uploadPath, $filename);
            $rutaQr = 'images/payment_methods/' . $filename;
        }

        PaymentMethod::create([
            'nombre' => $request->nombre,
            'tipo'   => $request->tipo,
            'numero' => $request->numero,
            'cuenta' => $request->cuenta,
            'cci'    => $request->cci,
            'qr'     => $rutaQr,
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('admin.config.payment_methods.index')
            ->with('success', 'Método de pago creado correctamente.');
    }

    /* -----------------------------------------------------
       EDITAR
    ----------------------------------------------------- */
    public function edit($id)
    {
        $method = PaymentMethod::findOrFail($id);
        return view('admin.config.payment_methods.edit', compact('method'));
    }

    public function update(Request $request, $id)
    {
        $method = PaymentMethod::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo'   => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:255',
            'cuenta' => 'nullable|string|max:255',
            'cci'    => 'nullable|string|max:255',
            'qr'     => 'nullable|image|max:4096',
        ]);

        $rutaQr = $method->qr;

        if ($request->hasFile('qr')) {
            $uploadPath = public_path('images/payment_methods');

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $request->qr->getClientOriginalExtension();
            $request->qr->move($uploadPath, $filename);
            $rutaQr = 'images/payment_methods/' . $filename;
        }

        $method->update([
            'nombre' => $request->nombre,
            'tipo'   => $request->tipo,
            'numero' => $request->numero,
            'cuenta' => $request->cuenta,
            'cci'    => $request->cci,
            'qr'     => $rutaQr,
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('admin.config.payment_methods.index')
            ->with('success', 'Método de pago actualizado.');
    }

    /* -----------------------------------------------------
       ELIMINAR
    ----------------------------------------------------- */
    public function destroy($id)
    {
        $method = PaymentMethod::findOrFail($id);
        $method->delete();

        return redirect()->route('admin.config.payment_methods.index')
            ->with('success', 'Método de pago eliminado.');
    }

    /* -----------------------------------------------------
       API: lista de métodos activos para recargas
    ----------------------------------------------------- */
    public function apiActive()
    {
        return PaymentMethod::where('activo', true)
            ->orderBy('nombre')
            ->get();
    }
}
