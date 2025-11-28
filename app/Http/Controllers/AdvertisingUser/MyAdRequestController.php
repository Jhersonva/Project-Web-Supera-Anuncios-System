<?php

namespace App\Http\Controllers\AdvertisingUser;

use App\Http\Controllers\Controller;
use App\Models\AdCategory;
use App\Models\AdSubcategory;
use App\Models\FieldSubcategoryAd;
use App\Models\ValueFieldAd;
use App\Models\Advertisement;
use App\Models\AdvertisementImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class MyAdRequestController extends Controller
{
    /**
     * Mostrar formulario de creación de anuncio
     */
    public function create()
    {
        $categories = AdCategory::all();

        return view('advertising_user.my_ads.create-my-ads', compact('categories'));
    }

    /**
     * Cargar subcategorías por AJAX
     */
    public function loadSubcategories($categoryId)
    {
        $subcategories = AdSubcategory::where('ad_categories_id', $categoryId)->get();

        return response()->json($subcategories);
    }

    /**
     * Cargar campos dinámicos por AJAX
     */
    public function loadFields($subcategoryId)
    {
        $fields = FieldSubcategoryAd::where('ad_subcategories_id', $subcategoryId)->get();

        return response()->json($fields);
    }

    /**
     * Guardar solicitud de anuncio
     */
    public function store(Request $request)
    {
        // VALIDACIÓN
        $request->validate([
            'category_id' => 'required|exists:ad_categories,id',
            'subcategory_id' => 'required|exists:ad_subcategories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric',
            'days_active' => 'required|integer|min:1',
        ]);

        // CASTEAR SIEMPRE PARA EVITAR ERROR DE CARBON
        $days = (int)$request->input('days_active');

        // CALCULAR FECHA DE EXPIRACIÓN
        $expiresAt = now()->addDays($days);

        $user = auth()->user();

        // OBTENER SUBCATEGORÍA
        $subcategory = AdSubcategory::findOrFail($request->subcategory_id);
        $basePrice = (float)$subcategory->price;

        // COSTO FINAL
        $finalPrice = $basePrice * $days;

        // VERIFICAR SALDO
        if ($user->virtual_wallet < $finalPrice) {
            return back()->with('error', 'No tienes saldo suficiente para esta publicación.');
        }

        // DESCONTAR SALDO
        $user->virtual_wallet -= $finalPrice;
        $user->save();

        // CREAR ANUNCIO
        $ad = Advertisement::create([
            'ad_categories_id' => $request->category_id,
            'ad_subcategories_id' => $request->subcategory_id,
            'user_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'amount' => $request->amount,
            'days_active' => $days,
            'expires_at' => $expiresAt,
            'published' => 0,
            'urgent_publication' => 0,
            'status' => 'pendiente',
        ]);

        // CAMPOS DINÁMICOS
        if ($request->has('dynamic')) {
            foreach ($request->dynamic as $fieldId => $value) {
                ValueFieldAd::create([
                    'advertisementss_id' => $ad->id,
                    'fields_subcategory_ads_id' => $fieldId,
                    'value' => $value
                ]);
            }
        }

        // IMÁGENES
        if ($request->hasFile('images')) {

            $files = $request->file('images');
            $uploadPath = public_path('images/advertisementss');

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            foreach ($files as $index => $file) {

                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);

                AdvertisementImage::create([
                    'advertisementss_id' => $ad->id,
                    'image' => 'images/advertisementss/' . $filename,
                    'is_main' => $index === 0
                ]);
            }
        }

        return redirect()
            ->route('my-ads.index')
            ->with('success', 'Tu solicitud fue enviada. El administrador debe aprobarla.');
    }


}
