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
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Support\AdPrices;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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
        $summaryTotalCost = 0;

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
        $virtualWallet = $user->virtual_wallet ?? 0;

        return view('advertising_user.my_ads.create-my-ads', [
            'categories' => AdCategory::all(),
            'user' => $user,
            'virtualWallet' => $user->virtual_wallet ?? 0,
            'summaryTotalCost' => 0,

            'ad' => null,
            'subcategories' => collect(),
            'fields' => collect(),
            'values' => collect(),
            'images' => collect(),

            'urgentPrice'    => Setting::get('urgent_publication_price', 7),
            'featuredPrice'  => Setting::get('featured_publication_price', 6),
            'premierePrice'  => Setting::get('premiere_publication_price', 5),
            'semiNewPrice'   => Setting::get('semi_new_publication_price', 4),
            'newPrice'       => Setting::get('new_publication_price', 3),
            'availablePrice' => Setting::get('available_publication_price', 2),
            'topPrice'       => Setting::get('top_publication_price', 1),

            'alertsPrepared' => $alertsPrepared,
            'isEmployment' => false,
        ]);
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
     * Enviar Solicitud del anuncio
     */
    public function store(Request $request)
    { 

    Log::info('===== STORE DEBUG START =====');

    Log::info('FILES ALL:', $request->allFiles());
    Log::info('HAS FILE images:', ['hasFile' => $request->hasFile('images')]);
    Log::info('FILE images RAW:', $request->file('images'));
    Log::info('INPUT images:', $request->input('images'));
    Log::info('CROP DATA:', ['crop_data' => $request->crop_data]);

    Log::info('===== STORE DEBUG END =====');

        $request->validate([
            'category_id'     => 'required|exists:ad_categories,id',
            'subcategory_id'  => 'required|exists:ad_subcategories,id',

            'title'           => 'required|string|min:3|max:70',
            'description'     => 'required|string|min:3',

            'department'      => 'required|string|max:255',
            'province'        => 'required|string|max:255',
            'district'        => 'required|string|max:255',
            'contact_location'=> 'nullable|string|max:255',

            'whatsapp'        => 'required|string|max:9',
            'call_phone'      => 'required|string|max:9',

            'amount_visible'  => 'required|in:0,1',
            'amount'          => 'required_if:amount_visible,1|numeric|min:0',
            'amount_currency' => 'required_if:amount_visible,1|in:PEN,USD',

            'days_active'     => 'required|integer|min:2',

            // IMÁGENES OBLIGATORIAS
            'images'   => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:8192',
            'crop_data' => 'nullable|string',

            // CAMPOS DINÁMICOS (OBLIGATORIOS)
            'dynamic'         => 'nullable|array',
        ]);

         // Obtener campos dinámicos reales de la subcategoría
        $fields = FieldSubcategoryAd::where(
            'ad_subcategories_id',
            $request->subcategory_id
        )->get();


        // Validar SOLO los que existen
        foreach ($fields as $field) {
            $request->validate([
                "dynamic.{$field->id}" => 'required|string|max:255'
            ]);
        }

        $user = auth()->user();

        $days = (int) $request->days_active;
        $expiresAt = now()->addDays($days);

        // Subcategoría para calcular precio
        $subcategory = AdSubcategory::findOrFail($request->subcategory_id);

        $basePrice = (float) $subcategory->price;

        $urgentPrice = $request->boolean('urgent_publication')
            ? (float) Setting::get('urgent_publication_price', 0)
            : 0;

        $featuredPrice = $request->boolean('featured_publication')
            ? (float) Setting::get('featured_publication_price', 0)
            : 0;

        $premierePrice = $request->boolean('premiere_publication')
            ? (float) Setting::get('premiere_publication_price', 0)
            : 0;

        $semiNewPrice = $request->boolean('semi_new_publication')
            ? (float) Setting::get('semi_new_publication_price', 0)
            : 0;

        $newPrice = $request->boolean('new_publication')
            ? (float) Setting::get('new_publication_price', 0)
            : 0;

        $availablePrice = $request->boolean('available_publication')
            ? (float) Setting::get('available_publication_price', 0)
            : 0;

        $topPrice = $request->boolean('top_publication')
            ? (float) Setting::get('top_publication_price', 0)
            : 0;


        $saveAsDraft = $request->boolean('save_as_draft');

        // Total final
       $finalPrice = $saveAsDraft
        ? 0
        : ($basePrice * $days)
            + $urgentPrice
            + $featuredPrice
            + $premierePrice
            + $semiNewPrice
            + $newPrice
            + $availablePrice
            + $topPrice;

        // Validación de saldo
        if ($user->virtual_wallet < $finalPrice && !$saveAsDraft) {
            return back()->with('error', 'Saldo insuficiente.');
        }

        // Descontar saldo
        if (!$saveAsDraft) {
            $user->virtual_wallet -= $finalPrice;
            $user->save();
        }

        $amount = $request->amount_visible == 1 ? $request->amount : 0;

        $receiptType = $request->filled('receipt_type') ? $request->receipt_type : 'nota_venta';

        $receiptData = [
            'receipt_type' => $receiptType,
            'dni'          => null,
            'ruc'          => null,
            'company_name' => null,
            'address'      => null,
            'full_name'    => $user->full_name,
        ];

        // Sobrescribir según tipo de comprobante
        if ($receiptType === 'boleta') {
            $receiptData['dni']       = $request->input('dni');
            $receiptData['full_name'] = $request->input('boleta_full_name');
        }

        if ($receiptType === 'factura') {
            $receiptData['ruc']          = $request->input('ruc');
            $receiptData['company_name'] = $request->input('company_name');
            $receiptData['address']      = $request->input('address');
            $receiptData['full_name']    = $request->input('company_name'); 
        }

        if ($receiptType === 'nota_venta') {
            $receiptData['full_name'] = $request->input('nota_full_name');
        }

        $verificationRequested = false;

        if (in_array($request->category_id, [2, 3])) {
            $verificationRequested = $request->boolean('verification_requested');
        }

        $receiptCode = null;

        if ($receiptType === 'nota_venta') {
            $receiptCode = $this->generateNotaVentaCode();
        }

        $request->merge([
            'title'      => mb_strtoupper(trim($request->title), 'UTF-8'),
            'department' => $request->department
                ? mb_strtoupper(trim($request->department), 'UTF-8')
                : null,
            'province'   => $request->province
                ? mb_strtoupper(trim($request->province), 'UTF-8')
                : null,
            'district'   => $request->district
                ? mb_strtoupper(trim($request->district), 'UTF-8')
                : null,
        ]);

        $status = $saveAsDraft ? 'draft' : 'pendiente';

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
            'whatsapp'              => $request->whatsapp,
            'call_phone'            => $request->call_phone,
            'amount'                => $amount,
            'amount_currency' => $request->amount_currency ?? 'PEN',
            'amount_visible'        => $request->amount_visible,
            'amount_text'        => $request->amount_text,
            'days_active'           => $days,
            //'expires_at'            => $expiresAt,
            'published'             => false,
            'status'     => $status,
            'expires_at' => $expiresAt,

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

        if (!$saveAsDraft) {

            $folder = public_path('proof_payment');
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            $receiptFile = "receipt_{$ad->id}.pdf";
            $receiptPath = $folder . DIRECTORY_SEPARATOR . $receiptFile;

            $pdf = Pdf::loadView('public.pdf.receipt', [
                'ad' => $ad,
                'user' => $user,
                'finalPrice' => $finalPrice,
            ]);

            file_put_contents($receiptPath, $pdf->output());

            $ad->update([
                'receipt_file' => "proof_payment/{$receiptFile}"
            ]);
        }

        // CAMPOS DINÁMICOS
        if ($request->has('dynamic')) {
            foreach ($request->dynamic as $fieldId => $value) {

                ValueFieldAd::create([
                    'advertisementss_id' => $ad->id,
                    'fields_subcategory_ads_id' => $fieldId,
                    'value' => is_string($value)
                    ? mb_strtolower(trim($value), 'UTF-8')
                    : $value
                ]);
            }
        }

        if ($request->hasFile('images')) {

    Log::info('ENTRÓ A hasFile');

    $files = $request->file('images');

    Log::info('TOTAL FILES COUNT:', ['count' => count($files)]);

    foreach ($files as $index => $file) {

        Log::info('Procesando archivo:', [
            'index' => $index,
            'originalName' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'valid' => $file->isValid()
        ]);

        if (!$file->isValid()) {
            Log::warning('Archivo inválido');
            continue;
        }

        $filename = time().'_'.uniqid().'.webp';
        $path = public_path('images/advertisementss/'.$filename);

        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);
        $image->toWebp(85)->save($path);

        AdvertisementImage::create([
            'advertisementss_id' => $ad->id,
            'image' => 'images/advertisementss/'.$filename,
            'crop_data' => null,
            'is_main' => $index === 0
        ]);

        Log::info('Imagen guardada:', ['filename' => $filename]);
    }

} else {

    Log::warning('NO ENTRÓ A hasFile(images)');
}


        return redirect()
            ->route('my-ads.index')
            ->with(
                'success',
                $saveAsDraft
                    ? 'Tu anuncio fue guardado como borrador. Recarga saldo para publicarlo.'
                    : 'Tu anuncio fue enviado para revisión.'
            );
    }

    public function edit($id)
    {
        $user = auth()->user();

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
        $virtualWallet = $user->virtual_wallet ?? 0;

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
            'virtualWallet',
            'isEmployment'
        ));
    }

    public function update(Request $request, $id)
    {

        $ad = Advertisement::with(['images', 'fields_values'])->findOrFail($id);
        $cropPayload = json_decode($request->input('crop_data', '[]'), true);
        
        // Si está publicado, solo permitir ciertos campos
        $isPublished = $ad->status === 'publicado';
        
        $user = auth()->user();

        if ($isPublished) {

            // SOLO CAMPOS EDITABLES
            $request->validate([
                'department'       => 'required|string|max:255',
                'province'         => 'required|string|max:255',
                'district'         => 'required|string|max:255',
                'contact_location' => 'required|string|max:255',
                'whatsapp'         => 'required|string|max:9',
                'call_phone'       => 'required|string|max:9',
            ]);

        } else {

            $request->validate([
                'category_id'     => 'required|exists:ad_categories,id',
                'subcategory_id'  => 'required|exists:ad_subcategories,id',

                'title'           => 'required|string|min:3|max:70',
                'description'     => 'required|string|min:3',

                'department'      => 'required|string|max:255',
                'province'        => 'required|string|max:255',
                'district'        => 'required|string|max:255',
                'contact_location'=> 'required|string|max:255',

                'whatsapp'        => 'required|string|max:9',
                'call_phone'      => 'required|string|max:9',

                'amount_visible'  => 'required|in:0,1',
                'amount'          => 'required_if:amount_visible,1|numeric|min:0',
                //'amount_text' => 'required_if:amount_visible,0|string|max:50',
                'amount_currency' => 'required_if:amount_visible,1|in:PEN,USD',

                'days_active'     => 'required|integer|min:2',

                'images'          => 'nullable|array|max:5',
                'images.*'        => 'image|mimes:jpg,jpeg,png,webp|max:8192',

                'dynamic'         => 'nullable|array',
            ]);
        }

        // ===== VALIDAR CAMPOS DINÁMICOS REALES =====
        if (!$isPublished && $request->filled('subcategory_id')) {
            $fields = FieldSubcategoryAd::where(
                'ad_subcategories_id',
                $request->subcategory_id
            )->get();

            foreach ($fields as $field) {
                $request->validate([
                    "dynamic.{$field->id}" => 'required|string|max:255'
                ]);
            }
        }

        // ===== NORMALIZAR TEXTO =====
        $request->merge([
            'title'      => mb_strtoupper(trim($request->title), 'UTF-8'),
            'department' => mb_strtoupper(trim($request->department), 'UTF-8'),
            'province'   => mb_strtoupper(trim($request->province), 'UTF-8'),
            'district'   => mb_strtoupper(trim($request->district), 'UTF-8'),
        ]);

        // ===== DÍAS Y EXPIRACIÓN =====
        $days = $isPublished ? $ad->days_active : (int) $request->days_active;
        $expiresAt = $isPublished ? $ad->expires_at : now()->addDays($days);

        // ===== PRECIOS =====
        if (!$isPublished) {

            $subcategory = AdSubcategory::findOrFail($request->subcategory_id);
            $basePrice = (float) $subcategory->price;

            $urgentPrice     = $request->boolean('urgent_publication')    ? (float) Setting::get('urgent_publication_price', 0)    : 0;
            $featuredPrice   = $request->boolean('featured_publication')  ? (float) Setting::get('featured_publication_price', 0)  : 0;
            $premierePrice   = $request->boolean('premiere_publication')  ? (float) Setting::get('premiere_publication_price', 0)  : 0;
            $semiNewPrice    = $request->boolean('semi_new_publication')   ? (float) Setting::get('semi_new_publication_price', 0)   : 0;
            $newPrice        = $request->boolean('new_publication')        ? (float) Setting::get('new_publication_price', 0)        : 0;
            $availablePrice  = $request->boolean('available_publication')  ? (float) Setting::get('available_publication_price', 0)  : 0;
            $topPrice        = $request->boolean('top_publication')        ? (float) Setting::get('top_publication_price', 0)        : 0;

        } else {

            $basePrice = 0;
            $urgentPrice =
            $featuredPrice =
            $premierePrice =
            $semiNewPrice =
            $newPrice =
            $availablePrice =
            $topPrice = 0;
        }

        $saveAsDraft = $request->boolean('save_as_draft');

        $finalPrice = $saveAsDraft
            ? 0
            : ($basePrice * $days)
                + $urgentPrice
                + $featuredPrice
                + $premierePrice
                + $semiNewPrice
                + $newPrice
                + $availablePrice
                + $topPrice;

        // ===== MONTO =====
        $amount = $request->amount_visible == 1 ? $request->amount : 0;

        // ===== COMPROBANTE =====
        $receiptType = $request->filled('receipt_type') ? $request->receipt_type : 'nota_venta';

        $receiptData = [
            'receipt_type' => $receiptType,
            'dni'          => null,
            'ruc'          => null,
            'company_name' => null,
            'address'      => null,
            'full_name'    => $user->full_name,
        ];

        if ($receiptType === 'boleta') {
            $receiptData['dni']       = $request->dni;
            $receiptData['full_name'] = $request->boleta_full_name;
        }

        if ($receiptType === 'factura') {
            $receiptData['ruc']          = $request->ruc;
            $receiptData['company_name'] = $request->company_name;
            $receiptData['address']      = $request->address;
            $receiptData['full_name']    = $request->company_name;
        }

        if ($receiptType === 'nota_venta') {
            $receiptData['full_name'] = $request->nota_full_name;
        }

        $verificationRequested = in_array($request->category_id, [2, 3])
            ? $request->boolean('verification_requested')
            : false;

        if ($isPublished) {

            // SOLO CAMPOS PERMITIDOS
            $ad->update([
                'department'       => mb_strtoupper(trim($request->department), 'UTF-8'),
                'province'         => mb_strtoupper(trim($request->province), 'UTF-8'),
                'district'         => mb_strtoupper(trim($request->district), 'UTF-8'),
                'contact_location' => $request->contact_location,
                'whatsapp'         => $request->whatsapp,
                'call_phone'       => $request->call_phone,
            ]);

        }
        else {

            // ===== ACTUALIZAR ANUNCIO =====
            $ad->update([
                'ad_categories_id'      => $request->category_id,
                'ad_subcategories_id'   => $request->subcategory_id,
                'title'                 => $request->title,
                'description'           => $request->description,
                'department'            => $request->department,
                'province'              => $request->province,
                'district'              => $request->district,
                'contact_location'      => $request->contact_location,
                'whatsapp'              => $request->whatsapp,
                'call_phone'            => $request->call_phone,
                'amount'                => $amount,
                'amount_currency'       => $request->amount_currency ?? 'PEN',
                'amount_visible'        => $request->amount_visible,
                'amount_text'           => $request->amount_text,
                'days_active'           => $days,
                'expires_at'            => $expiresAt,
                'status'                => $saveAsDraft ? 'draft' : 'pendiente',

                // publicaciones
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

                // comprobante
                'receipt_type' => $receiptData['receipt_type'],
                'dni'          => $receiptData['dni'],
                'full_name'    => $receiptData['full_name'],
                'ruc'          => $receiptData['ruc'],
                'company_name' => $receiptData['company_name'],
                'address'      => $receiptData['address'],

                'verification_requested' => $verificationRequested,
            ]);
        }

        // ===== CAMPOS DINÁMICOS (SIN DUPLICAR) =====
        if (!$isPublished) {
            ValueFieldAd::where('advertisementss_id', $ad->id)->delete();

            if ($request->has('dynamic')) {
                foreach ($request->dynamic as $fieldId => $value) {
                    ValueFieldAd::create([
                        'advertisementss_id' => $ad->id,
                        'fields_subcategory_ads_id' => $fieldId,
                        'value' => mb_strtolower(trim($value), 'UTF-8'),
                    ]);
                }
            }
        }

        // ===== IMÁGENES (REEMPLAZA SOLO SI SUBEN NUEVAS) =====
        // ELIMINAR IMÁGENES MARCADAS CON X
        if (!$isPublished) {

            if ($request->filled('remove_images')) {

                $idsToRemove = explode(',', $request->remove_images);


                if (is_array($idsToRemove)) {

                    $images = AdvertisementImage::whereIn('id', $idsToRemove)
                        ->where('advertisementss_id', $ad->id)
                        ->get();

                    foreach ($images as $img) {
                        if (file_exists(public_path($img->image))) {
                            unlink(public_path($img->image));
                        }
                        $img->delete();
                    }
                }
            }

            // SUBIR NUEVAS IMÁGENES (CONVERSIÓN A WEBP) SUBIR NUEVAS IMÁGENES (SIN BORRAR LAS EXISTENTES)
            if ($request->hasFile('images')) {

                $path = public_path('images/advertisementss');
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                $manager = new ImageManager(new Driver());

                // contar cuántas imágenes quedan
                $currentCount = AdvertisementImage::where('advertisementss_id', $ad->id)->count();

                $cropPayload = json_decode($request->crop_data, true) ?? [];

                // solo UIDs de imágenes NUEVAS (id === null)
                $newUids = collect($cropPayload)
                    ->filter(fn ($i) => empty($i['id']) && !empty($i['uid']))
                    ->values();

                foreach ($request->file('images') as $index => $file) {

                    if ($currentCount >= 5) break;

                    $filename = time().'_'.uniqid().'.webp';

                    $image = $manager->read($file);
                    $image->toWebp(85)->save($path.'/'.$filename);

                    $cropPayload = json_decode($request->crop_data, true) ?? [];

                    // SOLO crops de imágenes nuevas (id === null)
                    $newCrops = collect($cropPayload)
                        ->filter(fn ($i) => empty($i['id']) && !empty($i['uid']))
                        ->values();

                    $cropInfo = $newCrops->get($index);

                    AdvertisementImage::create([
                        'advertisementss_id' => $ad->id,
                        'image'     => 'images/advertisementss/'.$filename,
                        'is_main'   => $currentCount === 0,
                        'uid'       => $cropInfo['uid'] ?? null,
                        'crop_data' => $cropInfo['cropData'] ?? null,
                    ]);

                    $currentCount++;
                }
            }
        }

        $cropPayload = json_decode($request->crop_data, true) ?? [];

        foreach ($cropPayload as $imgCrop) {

            // IMAGEN EXISTENTE
            if (!empty($imgCrop['id'])) {

                $img = AdvertisementImage::where('id', $imgCrop['id'])
                    ->where('advertisementss_id', $ad->id)
                    ->first();

                if ($img) {
                    $img->crop_data = $imgCrop['cropData'] ?? null;
                    $img->save();
                }

                continue;
            }

            // IMAGEN NUEVA (buscar por UID)
            if (!empty($imgCrop['uid'])) {

                $img = AdvertisementImage::where('uid', $imgCrop['uid'])
                    ->where('advertisementss_id', $ad->id)
                    ->first();

                if ($img) {
                    $img->crop_data = $imgCrop['cropData'] ?? null;
                    $img->save();
                }
            }
        }


        // ASEGURAR IMAGEN PRINCIPAL
        $hasMain = AdvertisementImage::where('advertisementss_id', $ad->id)
            ->where('is_main', true)
            ->exists();

        if (!$hasMain) {
            $first = AdvertisementImage::where('advertisementss_id', $ad->id)->first();
            if ($first) {
                $first->update(['is_main' => true]);
            }
        }

        // ===== GENERAR / ACTUALIZAR COMPROBANTE DE PAGO =====
        if (!$isPublished && !$saveAsDraft) {

            $folder = public_path('proof_payment');
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            // siempre el mismo archivo (se sobrescribe)
            $receiptFile = 'receipt_' . $ad->id . '.pdf';
            $receiptPath = $folder . DIRECTORY_SEPARATOR . $receiptFile;

            $pdf = Pdf::loadView('public.pdf.receipt', [
                'ad'         => $ad->fresh(), // datos ya actualizados
                'user'       => $user,
                'finalPrice' => $finalPrice,
            ]);

            file_put_contents($receiptPath, $pdf->output());

            // guardar ruta del comprobante
            $ad->update([
                'receipt_file' => 'proof_payment/' . $receiptFile,
            ]);
        }

        return redirect()
            ->route('my-ads.index')
            ->with('success', 'Anuncio actualizado correctamente.');
    }

    public function editDraft(Advertisement $ad)
    {
        $user = auth()->user();

        if ($ad->user_id !== auth()->id()) {
            abort(403);
        }

        if ($ad->status !== 'draft') {
            return redirect()->route('my-ads.index');
        }

        // ALERTAS SWEETALERT
        $alertsPrepared = [
            'adult_services' => [
                'title' => 'Contenido sensible',
                'text' => 'Este anuncio pertenece a la categoría Servicios Privados. Confirma que eres mayor de edad.',
                'icon' => 'warning',
                'confirmButtonText' => 'Entiendo',
                'cancelButtonText' => 'Salir',
            ],
        ];

        // SALDO VIRTUAL
        $virtualWallet = (int) $user->virtual_wallet;

        // ===== RESUMEN DE COSTO (SOLO VISTA) =====
        $days = (int) $ad->days_active;

        $subcategory = AdSubcategory::find($ad->ad_subcategories_id);
        $basePrice = $subcategory ? (float) $subcategory->price : 0;

        // precios guardados en el anuncio (clave)
        $totalCost =
            ($basePrice * $days)
            + ($ad->urgent_publication ? $ad->urgent_price : 0)
            + ($ad->featured_publication ? $ad->featured_price : 0)
            + ($ad->premiere_publication ? $ad->premiere_price : 0)
            + ($ad->semi_new_publication ? $ad->semi_new_price : 0)
            + ($ad->new_publication ? $ad->new_price : 0)
            + ($ad->available_publication ? $ad->available_price : 0)
            + ($ad->top_publication ? $ad->top_price : 0);

        return view('advertising_user.my_ads.create-my-ads', [
            'ad' => $ad,
            'user' => $user,
            'virtualWallet' => $virtualWallet, 
            'summaryTotalCost' => $totalCost,
            'alertsPrepared' => $alertsPrepared,

            'categories' => AdCategory::all(),
            'subcategories' => AdSubcategory::where('ad_categories_id', $ad->ad_categories_id)->get(),
            'fields' => FieldSubcategoryAd::where('ad_subcategories_id', $ad->ad_subcategories_id)->get(),
            'values' => ValueFieldAd::where('advertisementss_id', $ad->id)->get()->keyBy('fields_subcategory_ads_id'),
            'images' => AdvertisementImage::where('advertisementss_id', $ad->id)->get(),

            'urgentPrice' => Setting::get('urgent_publication_price', 7),
            'featuredPrice' => Setting::get('featured_publication_price', 6),
            'premierePrice' => Setting::get('premiere_publication_price', 5),
            'semiNewPrice' => Setting::get('semi_new_publication_price', 4),
            'newPrice' => Setting::get('new_publication_price', 3),
            'availablePrice' => Setting::get('available_publication_price', 2),
            'topPrice' => Setting::get('top_publication_price', 1),

            'isEmployment' => $ad->ad_categories_id == 1,
        ]);
    }

    public function updateDraft(Request $request, Advertisement $ad)
    {
        if ($ad->user_id !== auth()->id()) abort(403);
        if ($ad->status !== 'draft') abort(403);

        // Recibir crop_data enviado desde JS
        $cropPayload = json_decode($request->input('crop_data', '[]'), true);

        $request->validate([
            'title' => 'required|string|max:70',
            'description' => 'required|string',
            'days_active' => 'required|integer|min:1',
            'amount_visible' => 'required|in:0,1',
            'amount' => 'required_if:amount_visible,1|nullable|numeric|min:0',
            'remove_images' => 'nullable|string',
            'images'        => 'nullable|array|max:5',
            'images.*'      => 'image|mimes:jpg,jpeg,png,webp|max:8192',
        ]);

        $user = auth()->user();
        $isPublishing = $request->boolean('publish'); 
        $days = (int) $request->days_active;

        // =======================
        // PRECIO BASE
        // =======================
        $subcategory = AdSubcategory::findOrFail($ad->ad_subcategories_id);
        $basePrice = (float) $subcategory->price;

        $urgentPrice = $request->boolean('urgent_publication')
            ? (float) Setting::get('urgent_publication_price', 0)
            : 0;

        $featuredPrice = $request->boolean('featured_publication')
            ? (float) Setting::get('featured_publication_price', 0)
            : 0;

        $premierePrice = $request->boolean('premiere_publication')
            ? (float) Setting::get('premiere_publication_price', 0)
            : 0;

        $semiNewSelected = $request->boolean('semi_new_publication');
        $semiNewPrice = $semiNewSelected
            ? (float) Setting::get('semi_new_publication_price', 0)
            : 0;

        $newPrice = $request->boolean('new_publication')
            ? (float) Setting::get('new_publication_price', 0)
            : 0;

        $availablePrice = $request->boolean('available_publication')
            ? (float) Setting::get('available_publication_price', 0)
            : 0;

        $topPrice = $request->boolean('top_publication')
            ? (float) Setting::get('top_publication_price', 0)
            : 0;

        // =======================
        // PRECIO FINAL (SIEMPRE REAL)
        // =======================
        $finalPrice =
            ($basePrice * $days)
            + $urgentPrice
            + $featuredPrice
            + $premierePrice
            + $semiNewPrice
            + $newPrice
            + $availablePrice
            + $topPrice;
    
        // =======================
        // COBRO SOLO SI PUBLICA
        // =======================
        if ($isPublishing) {
            if ($user->virtual_wallet < $finalPrice) {
                return back()->with('error', 'Saldo insuficiente para publicar el anuncio.');
            }

            $user->decrement('virtual_wallet', $finalPrice);
        }

        // =======================
        // NORMALIZAR TEXTO
        // =======================
        $request->merge([
            'title'      => mb_strtoupper(trim($request->title), 'UTF-8'),
            'department' => $request->department ? mb_strtoupper(trim($request->department), 'UTF-8') : null,
            'province'   => $request->province   ? mb_strtoupper(trim($request->province), 'UTF-8') : null,
            'district'   => $request->district   ? mb_strtoupper(trim($request->district), 'UTF-8') : null,
        ]);

        $amount = $request->amount_visible == 1 ? $request->amount : 0;

        $verificationRequested = false;
        if (in_array($ad->ad_categories_id, [2, 3])) {
            $verificationRequested = $request->boolean('verification_requested');
        }

        // =======================
        // ACTUALIZAR ANUNCIO
        // =======================
        $ad->update([
            'title'       => $request->title,
            'description' => $request->description,
            'department'  => $request->department,
            'province'    => $request->province,
            'district'    => $request->district,
            'contact_location' => $request->contact_location,

            'amount' => $amount,
            'amount_visible' => $request->amount_visible,
            'amount_text' => $request->amount_text,
            'days_active' => $days,

            'status'     => $isPublishing ? 'pendiente' : 'draft',
            'published'  => false,
            'expires_at' => $isPublishing ? now()->addDays($days) : null,

            // PUBLICACIONES
            'urgent_publication'    => $request->boolean('urgent_publication'),
            'urgent_price'          => $urgentPrice,

            'featured_publication'  => $request->boolean('featured_publication'),
            'featured_price'        => $featuredPrice,

            'premiere_publication'  => $request->boolean('premiere_publication'),
            'premiere_price'        => $premierePrice,

            'semi_new_publication' => $semiNewSelected,
            'semi_new_price'       => $semiNewPrice,

            'new_publication'       => $request->boolean('new_publication'),
            'new_price'             => $newPrice,

            'available_publication' => $request->boolean('available_publication'),
            'available_price'       => $availablePrice,

            'top_publication'       => $request->boolean('top_publication'),
            'top_price'             => $topPrice,

            'verification_requested' => $verificationRequested,
        ]);

        // =======================
        // DATOS DE CONTACTO
        // =======================
        $user->update([
            'whatsapp'   => $request->whatsapp,
            'call_phone' => $request->call_phone,
        ]);

        // =======================
        // PDF SOLO SI PUBLICA
        // =======================
        if ($isPublishing) {

            $receiptType = $request->receipt_type ?? 'nota_venta';
            $receiptCode = $receiptType === 'nota_venta'
                ? $this->generateNotaVentaCode()
                : null;

            $ad->update([
                'receipt_type' => $receiptType,
                'receipt_code' => $receiptCode,
            ]);

            $folder = public_path('proof_payment');
            if (!file_exists($folder)) mkdir($folder, 0755, true);

            $pdf = Pdf::loadView('public.pdf.receipt', [
                'ad' => $ad,
                'user' => $user,
                'finalPrice' => $finalPrice
            ]);

            $filename = "receipt_{$ad->id}.pdf";
            file_put_contents($folder.'/'.$filename, $pdf->output());

            $ad->update([
                'receipt_file' => "proof_payment/{$filename}"
            ]);
        }

        // =======================
        // CAMPOS DINÁMICOS
        // =======================
        if ($request->has('dynamic')) {
            ValueFieldAd::where('advertisementss_id', $ad->id)->delete();

            foreach ($request->dynamic as $fieldId => $value) {
                ValueFieldAd::create([
                    'advertisementss_id' => $ad->id,
                    'fields_subcategory_ads_id' => $fieldId,
                    'value' => is_string($value)
                        ? mb_strtolower(trim($value), 'UTF-8')
                        : $value
                ]);
            }
        }

        // =======================
        // ELIMINAR IMÁGENES MARCADAS (X)
        // =======================
        if ($request->filled('remove_images')) {

            $idsToRemove = json_decode($request->remove_images, true);

            if (is_array($idsToRemove)) {

                $images = AdvertisementImage::whereIn('id', $idsToRemove)
                    ->where('advertisementss_id', $ad->id)
                    ->get();

                foreach ($images as $img) {
                    if (file_exists(public_path($img->image))) {
                        unlink(public_path($img->image));
                    }
                    $img->delete();
                }
            }
        }

        // =======================
        // SUBIR NUEVAS IMÁGENES (SIN BORRAR LAS EXISTENTES)
        // =======================
        if ($request->hasFile('images')) {

            $path = public_path('images/advertisementss');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            // cuántas imágenes quedan actualmente
            $currentCount = AdvertisementImage::where(
                'advertisementss_id',
                $ad->id
            )->count();

            $cropPayload = json_decode($request->crop_data, true) ?? [];

            $newImagesPayload = collect($cropPayload)
                ->filter(fn ($img) => empty($img['id']) && !empty($img['uid']))
                ->values();

            foreach ($request->file('images') as $index => $file) {

                if ($currentCount >= 5) break;

                $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                $file->move($path, $filename);

                $uidFromJs = $newImagesPayload[$index]['uid'] ?? null;

                AdvertisementImage::create([
                    'advertisementss_id' => $ad->id,
                    'image' => 'images/advertisementss/'.$filename,
                    'crop_data' => null,
                    'is_main' => $currentCount === 0,
                    'uid' => $uidFromJs
                ]);

                $currentCount++;
            }
        }

        $cropPayload = json_decode($request->crop_data, true) ?? [];

        foreach ($cropPayload as $imgCrop) {

            // IMAGEN EXISTENTE
            if (!empty($imgCrop['id'])) {

                $img = AdvertisementImage::where('id', $imgCrop['id'])
                    ->where('advertisementss_id', $ad->id)
                    ->first();

                if ($img) {
                    $img->crop_data = $imgCrop['cropData'] ?? null;
                    $img->save();
                }

                continue;
            }

            // IMAGEN NUEVA → buscar por UID
            if (!empty($imgCrop['uid'])) {

                $img = AdvertisementImage::where('uid', $imgCrop['uid'])
                    ->where('advertisementss_id', $ad->id)
                    ->first();

                if ($img) {
                    $img->crop_data = $imgCrop['cropData'] ?? null;
                    $img->save();
                }
            }
        }

        // =======================
        // ASEGURAR IMAGEN PRINCIPAL
        // =======================
        $hasMain = AdvertisementImage::where('advertisementss_id', $ad->id)
            ->where('is_main', true)
            ->exists();

        if (!$hasMain) {
            $first = AdvertisementImage::where('advertisementss_id', $ad->id)->first();
            if ($first) {
                $first->update(['is_main' => true]);
            }
        }

        return back()->with(
            'success',
            $isPublishing
                ? 'Tu anuncio fue enviado a revisión.'
                : 'Borrador actualizado correctamente.'
        );
    }

    public function destroy(Request $request, $id)
    {
        DB::transaction(function () use ($request, $id) {

            $ad = Advertisement::with(['images', 'user', 'subcategory'])
                ->lockForUpdate()
                ->findOrFail($id);

            $user = $ad->user;

            if (in_array($ad->status, ['pendiente'])) {

                $totalPaid =
                    ($ad->subcategory->price * $ad->days_active)
                    + $ad->urgent_price
                    + $ad->featured_price
                    + $ad->premiere_price
                    + $ad->semi_new_price
                    + $ad->new_price
                    + $ad->available_price
                    + $ad->top_price;

                if ($totalPaid > 0) {
                    $user->virtual_wallet += $totalPaid;
                    $user->save();
                }
            }

            if ($ad->receipt_file && file_exists(public_path($ad->receipt_file))) {
                @unlink(public_path($ad->receipt_file));
            }

            foreach ($ad->images as $img) {
                if (file_exists(public_path($img->image))) {
                    @unlink(public_path($img->image));
                }
                $img->delete();
            }

            ValueFieldAd::where('advertisementss_id', $ad->id)->delete();

            $ad->delete();
        });

        return redirect()->to($request->return_to)
            ->with('success', 'Anuncio eliminado correctamente.');
    }
}
