<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    /**
     * LISTAR reclamos (admin)
     */
    public function indexView(Request $request)
    {
        $search = $request->get('search');

        $complaints = Complaint::when($search, function ($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('subject', 'like', "%$search%");
            })
            ->latest()
            ->paginate(15);

        return view('admin.config.complaints.index', compact('complaints', 'search'));
    }

    /**
     * CREAR reclamo (advertising_user)
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'document_type' => 'nullable|string|max:20',
            'document_number' => 'nullable|string|max:20',
            'complaint_type' => 'required|in:reclamo,queja',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'request' => 'nullable|string',
        ]);

        $complaint = Complaint::create([
            'user_id' => Auth::id(),
            ...$request->all(),
        ]);

        return back()->with('success', 'Tu reclamo fue enviado correctamente.');
    }

    /**
     * VER reclamo específico (admin)
     */
    public function show(Complaint $complaint)
    {
        return view('admin.config.complaints.show', compact('complaint'));
    }

    /**
     * ACTUALIZAR reclamo (admin)
     */
    public function update(Request $request, Complaint $complaint)
    {
        $request->validate([
            'status' => 'required|in:pendiente,atendido,cerrado',
            'response' => 'nullable|string',
        ]);

        $complaint->update([
            'status' => $request->status,
            'response' => $request->response,
            'responded_at' => now(),
        ]);

        return redirect()
            ->route('admin.config.complaints.show', $complaint)
            ->with('success', 'Reclamo actualizado correctamente.');
    }

    /**
     * ELIMINAR reclamo (admin)
     */
    public function destroy(Complaint $complaint)
    {
        $complaint->delete();

        return redirect()
            ->route('admin.config.complaints.index')
            ->with('success', 'Reclamo eliminado correctamente');
    }

}
