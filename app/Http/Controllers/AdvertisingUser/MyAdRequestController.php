<?php

namespace App\Http\Controllers\AdvertisingUser;

use App\Http\Controllers\Controller;
use App\Models\AdCategory;
use App\Models\AdSubcategory;
use App\Models\FieldSubcategoryAd;
use App\Models\ValueFieldAd;
use App\Models\Advertisement;
use App\Models\AdvertisementImage;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class MyAdRequestController extends Controller
{
    /**
     * Mostrar formulario de creación de anuncio
     */
    public function create()
    {
        $categories = AdCategory::all();

        $urgentPrice = Setting::get('urgent_publication_price', 5.00);
        $featuredPrice  = Setting::get('featured_publication_price', 3.00);
        $premierePrice  = Setting::get('premiere_publication_price', 3.00);

        return view('advertising_user.my_ads.create-my-ads', compact('categories', 'urgentPrice', 'featuredPrice', 'premierePrice'));
    }

    public function show($id)
    {
        $ad = Advertisement::with(['images', 'mainImage', 'fields_values'])
            ->findOrFail($id);

        return view('advertising_user.my_ads.show-my-ad', compact('ad'));
    }

    public function stats($id)
    {
        $ad = Advertisement::findOrFail($id);

        // Simulación de estadísticas (se puede reemplazar luego)
        $stats = [
            'views' => rand(20, 400),
            'contacts' => rand(1, 50),
            'favorites' => rand(1, 30),
        ];

        return view('advertising_user.my_ads.stats-my-ad', compact('ad', 'stats'));
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

    public function categoryInfo($id)
    {
        return AdCategory::findOrFail($id);
    }

    /**
     * Guardar y publicar el anuncio
     */
    public function store(Request $request)
    {
        Log::info("REQUEST RAW", $request->all());

        $request->validate([
            'category_id' => 'required|exists:ad_categories,id',
            'subcategory_id' => 'required|exists:ad_subcategories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'amount_visible' => 'required|in:0,1',
            'amount' => 'required_if:amount_visible,1|nullable|numeric|min:0',
            'days_active' => 'required|integer|min:1',
        ]);

        $user = auth()->user();

        $days = (int) $request->days_active;
        $expiresAt = now()->addDays($days);

        // Subcategoría para calcular precio
        $subcategory = AdSubcategory::findOrFail($request->subcategory_id);
        $basePrice = (float) $subcategory->price;

        // Precio urgente
        $urgentPrice = $request->urgent_publication == 1
            ? (float) Setting::get('urgent_publication_price', 5.00)
            : 0;

        // Precio destacado
        $featuredPrice = $request->featured_publication == 1
            ? (float) Setting::get('featured_publication_price', 10.00)
            : 0;
        
        // Precio estreno
        $premierePrice = $request->premiere_publication == 1
            ? (float) Setting::get('premiere_publication_price', 0)
            : 0;

        // Total final
       $finalPrice = ($basePrice * $days) + $urgentPrice + $featuredPrice + $premierePrice;


        // Validación de saldo
        if ($user->virtual_wallet < $finalPrice) {
            return back()->with('error', 'No tienes saldo suficiente para publicar este anuncio.');
        }

        // Descontar saldo
        $user->virtual_wallet -= $finalPrice;
        $user->save();

        $amount = $request->amount_visible == 1 ? $request->amount : 0;

        Log::info("ANTES DE CREAR", [
            'premiere_publication' => $request->premiere_publication,
            'premiere_publication_bool' => $request->premiere_publication == 1 ? 1 : 0,
            'premiere_price' => $premierePrice,
        ]);


        // Crear ANUNCIO *PUBLICADO AUTOMÁTICAMENTE*
        $ad = Advertisement::create([
            'ad_categories_id'      => $request->category_id,
            'ad_subcategories_id'   => $request->subcategory_id,
            'user_id'               => $user->id,
            'title'                 => $request->title,
            'description'           => $request->description,
            'contact_location'      => $request->contact_location,
            'amount'                => $amount,
            'amount_visible'        => $request->amount_visible,
            'days_active'           => $days,
            'expires_at'            => $expiresAt,
            'published'             => false,
            'status'                => 'pendiente',

            'urgent_publication'    => $request->has('urgent_publication'),
            'urgent_price'          => $urgentPrice,

            'featured_publication'  => $request->has('featured_publication'),
            'featured_price' => $featuredPrice,

            'premiere_publication' => $request->premiere_publication == 1,
            'premiere_price'       => $premierePrice,

            'receipt_type'          => $request->receipt_type,
            'dni'                   => $request->dni,
            'full_name'             => $request->full_name,
            'ruc'                   => $request->ruc,
            'company_name'          => $request->company_name,
            'address'               => $request->address,
        ]);

        //  GENERAR COMPROBANTE Y GUARDARLO EN public/proof_payment/

        $folder = public_path('proof_payment');
        if (!file_exists($folder)) mkdir($folder, 0777, true);

        $receiptFile = "receipt_{$ad->id}.pdf";
        $receiptPath = $folder . '/' . $receiptFile;

        // Renderizar PDF usando la vista
        $pdf = Pdf::loadView('public.pdf.receipt', [
            'ad' => $ad,
            'user' => $user,
            'finalPrice' => $finalPrice
        ]);

        // Guardar PDF en el servidor
        $pdf->save($receiptPath);

        // Guardar ruta en la BD
        $ad->update([
            'receipt_file' => "proof_payment/{$receiptFile}"
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

            $path = public_path('images/advertisementss');
            if (!file_exists($path)) mkdir($path, 0777, true);

            foreach ($request->file('images') as $index => $file) {

                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($path, $filename);

                AdvertisementImage::create([
                    'advertisementss_id' => $ad->id,
                    'image' => 'images/advertisementss/' . $filename,
                    'is_main' => $index == 0
                ]);
            }
        }

        return redirect()
            ->route('my-ads.index')
            ->with('success', 'Tu anuncio ha sido enviado para revisión. Será publicado una vez aprobado.');
    }

    public function edit($id)
    {
        $ad = Advertisement::with(['mainImage', 'images', 'fields_values'])->findOrFail($id);

        $categories = AdCategory::all();

        $subcategories = AdSubcategory::where('ad_categories_id', $ad->ad_categories_id)->get();

        $fields = FieldSubcategoryAd::where('ad_subcategories_id', $ad->ad_subcategories_id)->get();

        $urgentPrice = Setting::get('urgent_publication_price', 5.00);

        return view('advertising_user.my_ads.edit-my-ads', compact(
            'ad', 'categories', 'subcategories', 'fields', 'urgentPrice'
        ));
    }

    public function update(Request $request, $id)
    {
        $ad = Advertisement::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:ad_categories,id',
            'subcategory_id' => 'required|exists:ad_subcategories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'amount_visible' => 'required|in:0,1',
            'amount' => 'required_if:amount_visible,1|nullable|numeric|min:0',
            'contact_location' => 'nullable|string',
        ]);

        $amount = $request->amount_visible == 1 ? $request->amount : 0;

        $ad->update([
            'ad_categories_id' => $request->category_id,
            'ad_subcategories_id' => $request->subcategory_id,
            'title' => $request->title,
            'description' => $request->description,
            'contact_location' => $request->contact_location,
            'amount_visible' => $request->amount_visible,
            'amount' => $amount,
            'urgent_publication' => $request->has('urgent_publication'),
        ]);

        // ACTUALIZAR CAMPOS DINÁMICOS
        if ($request->has('dynamic')) {
            foreach ($request->dynamic as $fieldId => $value) {

                ValueFieldAd::updateOrCreate(
                    [
                        'advertisementss_id' => $ad->id,
                        'fields_subcategory_ads_id' => $fieldId
                    ],
                    [
                        'value' => $value
                    ]
                );
            }
        }

        // IMÁGENES NUEVAS
        if ($request->hasFile('images')) {

            $path = public_path('images/advertisementss');
            if (!file_exists($path)) mkdir($path, 0777, true);

            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($path, $filename);

                AdvertisementImage::create([
                    'advertisementss_id' => $ad->id,
                    'image' => 'images/advertisementss/' . $filename,
                    'is_main' => 0
                ]);
            }
        }

        // ELIMINAR IMÁGENES
        if ($request->remove_images) {
            $ids = json_decode($request->remove_images, true);

            foreach ($ids as $imgId) {
                $img = AdvertisementImage::where('advertisementss_id', $ad->id)
                                        ->where('id', $imgId)
                                        ->first();

                if ($img) {
                    // borrar archivo físico
                    $path = public_path($img->image);
                    if (file_exists($path)) {
                        unlink($path);
                    }

                    // borrar de BD
                    $img->delete();
                }
            }
        }

        // ACTUALIZAR IMAGEN PRINCIPAL SELECCIONADA POR EL USUARIO
        if ($request->main_image) {

            // Quitar la principal actual
            AdvertisementImage::where('advertisementss_id', $ad->id)
                ->update(['is_main' => 0]);

            // Asignar la nueva
            AdvertisementImage::where('advertisementss_id', $ad->id)
                ->where('id', $request->main_image)
                ->update(['is_main' => 1]);
        }



        return redirect($request->return_to)->with('success', 'Anuncio actualizado correctamente');
    }

    public function destroy(Request $request, $id)
    {
        $ad = Advertisement::findOrFail($id);

        // ELIMINAR IMÁGENES
        foreach ($ad->images as $img) {
            if (file_exists(public_path($img->image))) {
                @unlink(public_path($img->image));
            }
            $img->delete();
        }

        ValueFieldAd::where('advertisementss_id', $ad->id)->delete();

        $ad->delete();

        return redirect()->to($request->return_to)
    ->with('success', 'Anuncio eliminado correctamente.');

    }
}
