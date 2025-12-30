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
use App\Models\Alert;
use App\Models\AdSubcategoryImage;
use App\Models\AdultContentPublishTerm;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Support\AdPrices;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class MyAdRequestController extends Controller
{
    /**
     * Mostrar formulario de creación de anuncio
     */
    public function create()
    {
        $categories = AdCategory::all();
        $user = auth()->user();

        $alerts = Alert::where('is_active', true)
            ->orderByDesc('created_at')
            ->get();

        // términos
        $terms = AdultContentPublishTerm::orderBy('id')->get();

        // preparamos alerts para JS
        $alertsPrepared = [];

        foreach ($alerts as $alert) {
            $alertsPrepared[] = [
                'title' => $alert->title,
                'description' => $alert->description,
                'logo' => $alert->logo,
                'terms' => $terms, 
            ];
        }

        $urgentPrice = Setting::get('urgent_publication_price', 7.00);
        $featuredPrice  = Setting::get('featured_publication_price', 6.00);
        $premierePrice  = Setting::get('premiere_publication_price', 5.00);
        $semiNewPrice  = Setting::get('semi_new_publication_price', 4.00);
        $newPrice  = Setting::get('new_publication_price', 3.00);
        $availablePrice  = Setting::get('available_publication_price', 2.00);
        $topPrice  = Setting::get('top_publication_price', 1.00);

        return view(
            'advertising_user.my_ads.create-my-ads',
            compact(
                'categories',
                'user',
                'urgentPrice',
                'featuredPrice',
                'premierePrice',
                'semiNewPrice',
                'newPrice',
                'availablePrice',
                'topPrice',
                'alertsPrepared'
            )
        );
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

    public function deactivate($id)
    {
        $ad = Advertisement::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!$ad->published) {
            return back()->with('error', 'El anuncio ya está desactivado.');
        }

        $ad->update([
            'published' => false,
            'status' => 'pendiente', 
        ]);

        return back()->with('success', 'El anuncio fue dado de baja correctamente.');
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

    private function generateNotaVentaCode()
    {
        return 'NV-' . now()->format('Y') . '-' . strtoupper(Str::random(6));
    }

    /**
     * Guardar y publicar el anuncio
     */
    public function store(Request $request)
    { 

        $request->validate([
            'category_id' => 'required|exists:ad_categories,id',
            'subcategory_id' => 'required|exists:ad_subcategories,id',
            'title' => 'required|string|max:70',
            'description' => 'required|string',
            'department' => 'nullable|string|max:255',
            'province'   => 'nullable|string|max:255',
            'district'   => 'nullable|string|max:255',
            'amount_visible' => 'required|in:0,1',
            'amount' => 'required_if:amount_visible,1|nullable|numeric|min:0',
            'verification_requested' => 'nullable|boolean',
            'days_active' => 'required|integer|min:1',
            'is_verified' => 'nullable|boolean',
        ]);

        $user = auth()->user();

        $days = (int) $request->days_active;
        $expiresAt = now()->addDays($days);

        // Subcategoría para calcular precio
        $subcategory = AdSubcategory::findOrFail($request->subcategory_id);
        $basePrice = (float) $subcategory->price;

        $prices = AdPrices::all();

        $urgentPrice = $request->boolean('urgent_publication') ? $prices['urgent'] : 0;
        $featuredPrice = $request->boolean('featured_publication') ? $prices['featured'] : 0;
        $premierePrice = $request->boolean('premiere_publication') ? $prices['premiere'] : 0;
        $semiNewPrice = $request->boolean('semi_new_publication') ? $prices['semi_new'] : 0;
        $newPrice = $request->boolean('new_publication') ? $prices['new'] : 0;
        $availablePrice = $request->boolean('available_publication') ? $prices['available'] : 0;
        $topPrice = $request->boolean('top_publication') ? $prices['top'] : 0;

        // Total final
       $finalPrice = ($basePrice * $days) + $urgentPrice + $featuredPrice + $premierePrice + $semiNewPrice + $newPrice + $availablePrice + $topPrice;

        // Validación de saldo
        if ($user->virtual_wallet < $finalPrice) {
            return back()->with('error', 'No tienes saldo suficiente para publicar este anuncio.');
        }

        // Descontar saldo
        $user->virtual_wallet -= $finalPrice;
        $user->save();

        $amount = $request->amount_visible == 1 ? $request->amount : 0;

        $receiptType = $request->filled('receipt_type')
        ? $request->receipt_type
        : 'nota_venta';

        $receiptData = [
            'receipt_type' => $receiptType,
            'dni'          => null,
            'ruc'          => null,
            'company_name' => null,
            'address'      => null,
            'full_name'    => $user->full_name,
        ];

        // BOLETA
        if ($receiptType === 'boleta') {
            $receiptData['dni'] = $request->dni ?? $user->dni;
            $receiptData['full_name'] = $request->boleta_full_name ?: $user->full_name;
        }

        // FACTURA
        if ($receiptType === 'factura') {
            $receiptData['ruc'] = $request->ruc;
            $receiptData['company_name'] = $request->company_name;
            $receiptData['address'] = $request->address;
            $receiptData['full_name'] = $request->company_name; 
        }

        // NOTA DE VENTA (explícita o implícita)
        if ($receiptType === 'nota_venta') {
            $receiptData['full_name'] = filled($request->nota_full_name)
                ? $request->nota_full_name
                : $user->full_name;
        }

        $verificationRequested = false;

        if (in_array($request->category_id, [2, 3])) {
            $verificationRequested = $request->boolean('verification_requested');
        }

        $receiptCode = null;

        if ($receiptType === 'nota_venta') {
            $receiptCode = $this->generateNotaVentaCode();
        }

        // Crear ANUNCIO *PUBLICADO AUTOMÁTICAMENTE*
        $ad = Advertisement::create([
            'ad_categories_id'      => $request->category_id,
            'ad_subcategories_id'   => $request->subcategory_id,
            'user_id'               => $user->id,
            'title'                 => $request->title,
            'description'           => $request->description,
            'department'          => $request->department,
            'province'            => $request->province,
            'district'            => $request->district,
            'contact_location'      => $request->contact_location,
            'amount'                => $amount,
            'amount_visible'        => $request->amount_visible,
            'amount_text'        => $request->amount_text,
            'days_active'           => $days,
            'expires_at'            => $expiresAt,
            'published'             => false,
            'status'                => 'pendiente',

            // ===== PUBLICACIONES =====
            'urgent_publication'    => $request->boolean('urgent_publication'),
            'urgent_price'          => $urgentPrice,

            'featured_publication'  => $request->boolean('featured_publication'),
            'featured_price'        => $featuredPrice,

            'premiere_publication'  => $request->boolean('premiere_publication'),
            'premiere_price'        => $premierePrice,

            'semi_new_publication'  => $request->boolean('semi_new_publication'),
            'semi_new_price'        => $semiNewPrice,

            'new_publication'       => $request->boolean('new_publication'),
            'new_price'             => $newPrice,

            'available_publication' => $request->boolean('available_publication'),
            'available_price'       => $availablePrice,

            'top_publication'       => $request->boolean('top_publication'),
            'top_price'             => $topPrice,

            'is_verified' => false,
            'verification_requested' => $verificationRequested,
            'verified_at' => null,

            // ===== COMPROBANTE =====
            'receipt_type' => $receiptData['receipt_type'],
            'receipt_code' => $receiptCode,
            'dni'          => $receiptData['dni'],
            'full_name'    => $receiptData['full_name'],
            'ruc'          => $receiptData['ruc'],
            'company_name' => $receiptData['company_name'],
            'address'      => $receiptData['address'],
        ]);

        $user->update([
            'whatsapp'   => $request->whatsapp,
            'call_phone' => $request->call_phone,
        ]);

        // IMAGEN DE SUBCATEGORÍA SELECCIONADA
        if ($request->filled('selected_subcategory_image')) {

            $ids = explode(',', $request->selected_subcategory_image);

            $destPath = public_path('images/advertisementss');
            if (!file_exists($destPath)) {
                mkdir($destPath, 0777, true);
            }

            foreach ($ids as $index => $id) {

                $img = AdSubcategoryImage::find($id);
                if (!$img) continue;

                $source = public_path($img->image);
                if (!file_exists($source)) continue;

                $filename = time().'_'.$index.'_subcat.'.pathinfo($source, PATHINFO_EXTENSION);
                copy($source, $destPath.'/'.$filename);

                AdvertisementImage::create([
                    'advertisementss_id' => $ad->id,
                    'image' => 'images/advertisementss/'.$filename,
                    'is_main' => $index === 0 
                ]);
            }
        }


        $folder = public_path('proof_payment');

        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $receiptFile = "receipt_{$ad->id}.pdf";
        $receiptPath = $folder . DIRECTORY_SEPARATOR . $receiptFile;

        $pdf = Pdf::loadView('public.pdf.receipt', [
            'ad' => $ad,
            'user' => $user,
            'finalPrice' => $finalPrice
        ]);

        // CREAR PDF REAL
        file_put_contents($receiptPath, $pdf->output());

        // Guardar ruta en BD
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
        $ad = Advertisement::with(['mainImage', 'images', 'fields_values', 'user'])->findOrFail($id);

        $categories = AdCategory::all();
        $subcategories = AdSubcategory::where('ad_categories_id', $ad->ad_categories_id)->get();
        $fields = FieldSubcategoryAd::where('ad_subcategories_id', $ad->ad_subcategories_id)->get();

        $isEmployment = $ad->ad_categories_id == 1;

        $urgentPrice     = Setting::get('urgent_publication_price', 7.00);
        $featuredPrice   = Setting::get('featured_publication_price', 6.00);
        $premierePrice   = Setting::get('premiere_publication_price', 5.00);
        $semiNewPrice    = Setting::get('semi_new_publication_price', 4.00);
        $newPrice        = Setting::get('new_publication_price', 3.00);
        $availablePrice  = Setting::get('available_publication_price', 2.00);
        $topPrice        = Setting::get('top_publication_price', 1.00);

        return view('advertising_user.my_ads.edit-my-ads', compact(
            'ad',
            'categories',
            'subcategories',
            'fields',
            'urgentPrice',
            'featuredPrice',
            'premierePrice',
            'semiNewPrice',
            'newPrice',
            'availablePrice',
            'topPrice',
            'isEmployment'
        ));
    }

    public function update(Request $request, $id)
    {
        $ad = Advertisement::with('images', 'mainImage')->findOrFail($id);

        // ELIMINAR IMÁGENES SI SE PIDE
        if ($request->remove_images === 'all') {
            foreach ($ad->images as $img) {
                if (file_exists(public_path($img->image))) {
                    @unlink(public_path($img->image));
                }
                $img->delete();
            }
        }

        // IMÁGENES SUBIDAS DESDE PC
        //EMPLEOS   
        $isEmployment = $ad->ad_categories_id == 1;

        // EMPLEOS
        if ($isEmployment && $request->filled('selected_subcategory_image_employment')) {

            foreach ($ad->images as $img) {
                @unlink(public_path($img->image));
                $img->delete();
            }

            $ref = AdSubcategoryImage::find(
                $request->selected_subcategory_image_employment
            );

            if ($ref) {
                $filename = time().'_empleo.'.pathinfo($ref->image, PATHINFO_EXTENSION);

                copy(
                    public_path($ref->image),
                    public_path('images/advertisementss/'.$filename)
                );

                AdvertisementImage::create([
                    'advertisementss_id' => $ad->id,
                    'image' => 'images/advertisementss/'.$filename,
                    'is_main' => 1,
                ]);
            }
        }

        // Bloque delete imagenes de otros
        if (!$isEmployment && $request->filled('remove_images')) {

            $idsToDelete = json_decode($request->remove_images, true);

            if (is_array($idsToDelete)) {
                foreach ($idsToDelete as $imgId) {

                    $img = $ad->images->where('id', $imgId)->first();

                    if ($img) {
                        if (file_exists(public_path($img->image))) {
                            @unlink(public_path($img->image));
                        }
                        $img->delete();
                    }
                }
            }
        }

        //Otros
        if (!$isEmployment &&$request->filled('selected_subcategory_image_general')) {

            $ids = explode(',', $request->selected_subcategory_image_general);

            foreach ($ids as $id) {

                $ref = AdSubcategoryImage::find($id);
                if (!$ref) continue;

                $filename = time().'_'.$id.'.'.pathinfo($ref->image, PATHINFO_EXTENSION);

                copy(
                    public_path($ref->image),
                    public_path('images/advertisementss/'.$filename)
                );

                AdvertisementImage::create([
                    'advertisementss_id' => $ad->id,
                    'image' => 'images/advertisementss/'.$filename,
                    'is_main' => 0,
                ]);
            }
        }

        // GUARDAR RESTO DE DATOS
        // Actualizar datos del usuario
        $user = $ad->user;

        if ($user) {
            $user->whatsapp   = $request->input('whatsapp');
            $user->call_phone = $request->input('call_phone');
            $user->save();
        }
        $ad->contact_location = $request->contact_location;
        $ad->save();

        return back()->with('success', 'Anuncio actualizado correctamente');
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
