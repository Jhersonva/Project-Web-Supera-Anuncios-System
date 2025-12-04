<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::orderBy('id', 'desc')->get();
        return view('admin.config.payment_methods.index', compact('methods'));
    }

    public function create()
    {
        return view('admin.config.payment_methods.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_method'        => 'required|string|max:255',
            'type'               => 'nullable|string|max:255',
            'holder_name'        => 'nullable|string|max:255',
            'cell_phone_number'  => 'nullable|string|max:255',
            'account_number'     => 'nullable|string|max:255',
            'cci'                => 'nullable|string|max:255',
            'qr'                 => 'nullable|image|max:4096',
            'logo'               => 'nullable|image|max:4096',
        ]);

        $rutaQr = null;
        $rutaLogo = null;

        $uploadPath = public_path('images/payment_methods');
        if (!file_exists($uploadPath)) mkdir($uploadPath, 0777, true);

        // QR
        if ($request->hasFile('qr')) {
            $filename = time().'_qr_'.uniqid().'.'.$request->qr->getClientOriginalExtension();
            $request->qr->move($uploadPath, $filename);
            $rutaQr = 'images/payment_methods/'.$filename;
        }

        // LOGO
        if ($request->hasFile('logo')) {
            $filename = time().'_logo_'.uniqid().'.'.$request->logo->getClientOriginalExtension();
            $request->logo->move($uploadPath, $filename);
            $rutaLogo = 'images/payment_methods/'.$filename;
        }

        PaymentMethod::create([
            'name_method'        => $request->name_method,
            'type'               => $request->type,
            'holder_name'        => $request->holder_name,
            'cell_phone_number'  => $request->cell_phone_number,
            'account_number'     => $request->account_number,
            'cci'                => $request->cci,
            'qr'                 => $rutaQr,
            'logo'               => $rutaLogo,
            'active'             => $request->has('active'),
        ]);

        return redirect()->route('admin.config.payment_methods.index')
            ->with('success', 'Método de pago creado correctamente.');
    }

    public function edit($id)
    {
        $method = PaymentMethod::findOrFail($id);
        return view('admin.config.payment_methods.edit', compact('method'));
    }

    public function update(Request $request, $id)
    {
        $method = PaymentMethod::findOrFail($id);

        $request->validate([
            'name_method'        => 'required|string|max:255',
            'type'               => 'nullable|string|max:255',
            'holder_name'        => 'nullable|string|max:255',
            'cell_phone_number'  => 'nullable|string|max:255',
            'account_number'     => 'nullable|string|max:255',
            'cci'                => 'nullable|string|max:255',
            'qr'                 => 'nullable|image|max:4096',
            'logo'               => 'nullable|image|max:4096',
        ]);

        $rutaQr = $method->qr;
        $rutaLogo = $method->logo;

        $uploadPath = public_path('images/payment_methods');
        if (!file_exists($uploadPath)) mkdir($uploadPath, 0777, true);

        // QR
        if ($request->hasFile('qr')) {

            if ($method->qr && file_exists(public_path($method->qr))) {
                unlink(public_path($method->qr));
            }

            $filename = time().'_qr_'.uniqid().'.'.$request->qr->getClientOriginalExtension();
            $request->qr->move($uploadPath, $filename);
            $rutaQr = 'images/payment_methods/'.$filename;
        }

        // LOGO
        if ($request->hasFile('logo')) {

            if ($method->logo && file_exists(public_path($method->logo))) {
                unlink(public_path($method->logo));
            }

            $filename = time().'_logo_'.uniqid().'.'.$request->logo->getClientOriginalExtension();
            $request->logo->move($uploadPath, $filename);
            $rutaLogo = 'images/payment_methods/'.$filename;
        }

        $method->update([
            'name_method'        => $request->name_method,
            'type'               => $request->type,
            'holder_name'        => $request->holder_name,
            'cell_phone_number'  => $request->cell_phone_number,
            'account_number'     => $request->account_number,
            'cci'                => $request->cci,
            'qr'                 => $rutaQr,
            'logo'               => $rutaLogo,
            'active'             => $request->has('active'),
        ]);

        return redirect()->route('admin.config.payment_methods.index')
            ->with('success', 'Método de pago actualizado correctamente.');
    }

    public function destroy($id)
    {
        $method = PaymentMethod::findOrFail($id);

        if ($method->qr && file_exists(public_path($method->qr))) {
            unlink(public_path($method->qr));
        }

        // borrar registro
        $method->delete();

        return redirect()->route('admin.config.payment_methods.index')
            ->with('success', 'Método de pago eliminado correctamente.');
    }
}
