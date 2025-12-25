<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Advertisement;
use App\Models\PrivacyPolicySetting;
use App\Models\Alert;

use App\Http\Controllers\PublicController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ComplaintBookSettingController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\PrivacyPolicyAcceptanceController;
use App\Http\Controllers\HomeController;


use App\Http\Controllers\AdvertisingUser\AdvertisementController;
use App\Http\Controllers\AdvertisingUser\RechargeController;
use App\Http\Controllers\AdvertisingUser\MyAdRequestController;
use App\Http\Controllers\AdvertisingUser\UserProfileController;


use App\Http\Controllers\Auth\AuthController;


use App\Http\Controllers\Admin\ReloadRequestController;
use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\FieldController;
use App\Http\Controllers\Admin\AdsHistoryController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CashBoxController;
use App\Http\Controllers\Admin\LabelPriceController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\PrivacyPolicySettingController;
use App\Http\Controllers\Admin\SubcategoryImageController;
use App\Http\Controllers\Admin\SystemSocialLinkController;
use App\Http\Controllers\Admin\AdultContentViewTermController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

// Página principal

Route::get('/', function () {
    $policy = PrivacyPolicySetting::first();
    return view('public.home', compact('policy'));
})->name('home');

Route::get('/api/ads', function (Request $request) {

    $pages = [
        'featured'   => $request->get('page_featured', 1),
        'urgent'     => $request->get('page_urgent', 1),
        'premiere'   => $request->get('page_premiere', 1),
        'semi_new'   => $request->get('page_semi_new', 1),
        'new'        => $request->get('page_new', 1),
        'available'  => $request->get('page_available', 1),
        'top'        => $request->get('page_top', 1),
        'normal'     => $request->get('page_normal', 1),
    ];

    /*
    BASE QUERY
    */
    $baseQuery = Advertisement::where('published', 1)
        ->where('status', 'publicado')
        ->where('expires_at', '>=', now())
        ->with(['user', 'category', 'subcategory', 'images', 'dynamicFields.field'])
        ->inRandomOrder();
    
    if (!auth()->check()) {
        $baseQuery->whereNot(function ($q) {
            $q->where('ad_categories_id', 4) 
            ->where('ad_subcategories_id', 21);
        });
    }

    /*
    CONSULTAS POR TIPO
    */
    $adsFeatured = (clone $baseQuery)
        ->where('featured_publication', 1)
        ->paginate(20, ['*'], 'page_featured', $pages['featured']);

    $adsUrgent = (clone $baseQuery)
        ->where('urgent_publication', 1)
        ->where('featured_publication', 0)
        ->paginate(20, ['*'], 'page_urgent', $pages['urgent']);

    $adsPremiere = (clone $baseQuery)
        ->where('premiere_publication', 1)
        ->where('urgent_publication', 0)
        ->where('featured_publication', 0)
        ->paginate(20, ['*'], 'page_premiere', $pages['premiere']);

    $adsSemiNew = (clone $baseQuery)
        ->where('semi_new_publication', 1)
        ->paginate(20, ['*'], 'page_semi_new', $pages['semi_new']);

    $adsNew = (clone $baseQuery)
        ->where('new_publication', 1)
        ->paginate(20, ['*'], 'page_new', $pages['new']);

    $adsAvailable = (clone $baseQuery)
        ->where('available_publication', 1)
        ->paginate(20, ['*'], 'page_available', $pages['available']);

    $adsTop = (clone $baseQuery)
        ->where('top_publication', 1)
        ->paginate(20, ['*'], 'page_top', $pages['top']);

    /*
    NORMALES (SIN NINGUNA ETIQUETA)
    */
    $adsNormal = (clone $baseQuery)
        ->where('urgent_publication', 0)
        ->where('featured_publication', 0)
        ->where('premiere_publication', 0)
        ->where('semi_new_publication', 0)
        ->where('new_publication', 0)
        ->where('available_publication', 0)
        ->where('top_publication', 0)
        ->paginate(50, ['*'], 'page_normal', $pages['normal']);

    /*
    TRANSFORMADOR ÚNICO
    */
    $transform = function ($ad) {

        // URL segura
        $ad->full_url = route('public.ad.detail', [
            'slug' => $ad->slug,
            'id'   => $ad->id
        ]);

        // Tiempo (NO approved_at)
        $ad->time_ago = $ad->created_at
            ->locale('es')
            ->diffForHumans();

        $ad->user_info = [
            'full_name'      => optional($ad->user)->full_name ?? 'Usuario',
            'profile_image'  => optional($ad->user)->profile_image
                ? asset(optional($ad->user)->profile_image)
                : asset('assets/img/profile-image/default-user.png'),
            'is_verified'    => optional($ad->user)->is_verified ? true : false,
        ];

        // Usuario (user_id puede ser null)
        $ad->whatsapp = optional($ad->user)->whatsapp
            ?? optional($ad->user)->phone
            ?? null;

        $ad->call_phone = optional($ad->user)->call_phone
            ?? optional($ad->user)->phone
            ?? null;
        
        $ad->dynamic_fields = $ad->dynamicFields
            ->take(4)
            ->map(function ($df) {
            return [
                'label' => $df->field->name ?? '',
                'value' => $df->value
            ];
        })
        ->values();

        unset($ad->dynamicFields);

        // Flags (para frontend)
        $ad->urgent_publication    = (int) $ad->urgent_publication;
        $ad->featured_publication  = (int) $ad->featured_publication;
        $ad->premiere_publication  = (int) $ad->premiere_publication;
        $ad->semi_new_publication  = (int) $ad->semi_new_publication;
        $ad->new_publication       = (int) $ad->new_publication;
        $ad->available_publication = (int) $ad->available_publication;
        $ad->top_publication       = (int) $ad->top_publication;

        return $ad;
    };

    foreach ([
        $adsFeatured,
        $adsUrgent,
        $adsPremiere,
        $adsSemiNew,
        $adsNew,
        $adsAvailable,
        $adsTop,
        $adsNormal
    ] as $collection) {
        $collection->getCollection()->transform($transform);
    }

    /*
    RESPUESTA FINAL
    */
    return response()->json([
        'featured'  => $adsFeatured,
        'urgent'    => $adsUrgent,
        'premiere'  => $adsPremiere,
        'semi_new'  => $adsSemiNew,
        'new'       => $adsNew,
        'available' => $adsAvailable,
        'top'       => $adsTop,
        'normal'    => $adsNormal,
    ]);
});


Route::get('/api/subcategories', function () {
    return \App\Models\AdSubcategory::select('id', 'name')->orderBy('name')->get();
});

// Contactar al anunciante
Route::get('/contact/{id}', [PublicController::class, 'contact'])->name('public.contact');

// Ver anuncio público
Route::get('/ad/{id}', [AdvertisementController::class, 'show'])->name('public.ad.show');
Route::get('/detalle-anuncio/{slug}/{id}', [AdvertisementController::class, 'show'])->name('public.ad.detail');

// Crear conversación desde anuncio
Route::post('/chat/start/{ad}', [ChatController::class, 'startConversation'])
    ->name('chat.start')
    ->middleware('auth');

Route::get('/chat', [ChatController::class, 'index'])
    ->name('chat.index')
    ->middleware('auth');

Route::get('/chat/check-new', [ChatController::class, 'checkNewConversations'])
        ->middleware('auth')
        ->name('chat.check-new');

Route::get('/chat/{id}', [ChatController::class, 'show'])
    ->name('chat.show')
    ->middleware('auth');

Route::post('/chat/{id}/send', [ChatController::class, 'sendMessage'])
    ->name('chat.send')
    ->middleware('auth');
    
Route::get('/chat/{id}/messages', [ChatController::class, 'getMessages'])
    ->name('chat.messages')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| RUTAS DE AUTENTICACIÓN (PÚBLICAS)
*/
Route::prefix('auth')->group(function () {

    // Login
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('auth.login');

    // Registro
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('auth.register');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('auth.logout');

    Route::get('/libro-de-reclamaciones', [ComplaintBookSettingController::class, 'publicView'])->name('public.complaint-book');

    Route::get('/api/alerts', function () {
        return \App\Models\Alert::where('is_active', true)
            ->orderByDesc('created_at')
            ->get();
    })->name('api.alerts');

});

/* Politicas de Privacidad */
Route::post('/privacy-policy/accept', [PrivacyPolicyAcceptanceController::class, 'accept'])
    ->name('privacy-policy.accept');

Route::post('/privacy-policy/reject', [PrivacyPolicyAcceptanceController::class, 'reject'])
    ->name('privacy-policy.reject');

//Route::get('/', [PublicAdController::class, 'index'])->name('home');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get(
    '/terminos/contenido-adulto',
    [AdultContentViewTermController::class, 'publicTerms']
)->name('adult.terms');


/*
|--------------------------------------------------------------------------
| RUTAS PRIVADAS — SOLO advertising_user
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::middleware(['auth'])->prefix('advertising')->group(function () {

        Route::get('/adult/view-terms',[AdultContentViewTermController::class, 'index']);

        /*
        |--------------------------------------------------------------------------
        | Mis anuncios
        |--------------------------------------------------------------------------
        */
        Route::get('/my-ads', [AdvertisementController::class, 'index'])->name('my-ads.index');
        Route::get('/my-ads/create', [MyAdRequestController::class, 'create'])->name('my-ads.createAd');
        Route::post('/my-ads/create', [MyAdRequestController::class, 'store'])->name('my-ads.storeAdRequest');
        Route::get('/my-ads/subcategories/{id}', [MyAdRequestController::class, 'loadSubcategories']);
        Route::get('/my-ads/category-info/{id}', [MyAdRequestController::class, 'categoryInfo']);
        Route::get('/my-ads/subcategories-with-category/{id}', [SubcategoryController::class, 'subcategoriesWithCategory']);
        Route::get('/fields/{id}', [MyAdRequestController::class, 'loadFields']);
        Route::get('/my-ads/{id}', [MyAdRequestController::class, 'show'])->name('my-ads.show');
        Route::get('/my-ads/{id}/edit', [MyAdRequestController::class, 'edit'])->name('my-ads.editAd');
        Route::post('/my-ads/{id}/update', [MyAdRequestController::class, 'update'])->name('my-ads.updateAd');
        Route::post('/my-ads/{id}/deactivate', [MyAdRequestController::class, 'deactivate'])->name('my-ads.deactivate');
        Route::delete('/my-ads/{id}/delete', [MyAdRequestController::class, 'destroy'])->name('my-ads.deleteAd');
        Route::get('/my-ads/{id}/stats', [MyAdRequestController::class, 'stats'])->name('my-ads.stats');
        Route::post('/ads/{ad}/request-verification', [AdvertisementController::class, 'requestVerification'])->name('ads.request-verification');

        /*
        |--------------------------------------------------------------------------
        | Recargas de saldo
        |--------------------------------------------------------------------------
        */
        Route::get('/recharges', [RechargeController::class, 'index'])->name('recharges.index');
        Route::post('/recharges', [RechargeController::class, 'store'])->name('recharges.store');

        /*
        |--------------------------------------------------------------------------
        | PERFIL DE USUARIO — advertising_user
        |--------------------------------------------------------------------------
        */
        Route::get('/profile', [UserProfileController::class, 'index'])->name('profile.index');
        Route::post('/profile/update', [UserProfileController::class, 'update'])->name('profile.update');

        /*
        |--------------------------------------------------------------------------
        | Configuración del libro de reclamaciones
        |--------------------------------------------------------------------------
        */
        Route::get('/complaint-book', [ComplaintBookSettingController::class, 'show']);
        Route::post('/complaints', [ComplaintController::class, 'store']);

        /*
        |--------------------------------------------------------------------------
        | Ver politicas de privacidad
        |--------------------------------------------------------------------------
        */
        Route::get('/privacy-policy', [PrivacyPolicySettingController::class, 'show'])->name('privacy-policy.show');
        Route::get('/subcategories/{id}/images',[SubcategoryImageController::class, 'bySubcategory'])->name('subcategories.images');

    });

    /*
    |--------------------------------------------------------------------------
    | RUTAS PRIVADAS — SOLO admin y employee
    |--------------------------------------------------------------------------
    */

    Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

        // PANEL GENERAL
        Route::get('/config/privacy-policy',[PrivacyPolicySettingController::class, 'indexGestion'])->name('admin.config.privacy-policy.indexGestion');

        //Ver Contenido con auth
        Route::get('/config/privacy-policy/authentication',[PrivacyPolicySettingController::class, 'index'])->name('admin.adult.authentication_terms.index');
        Route::put('/config/privacy-policy/authentication',[PrivacyPolicySettingController::class, 'update'])->name('admin.adult.authentication_terms.update');

        // Ver contenido adulto
        Route::get('adult-content/view-terms', [\App\Http\Controllers\Admin\AdultContentViewTermController::class, 'index'])->name('adult.view_terms.index');
        Route::put('adult-content/view-terms/{adultContentViewTerm}',[\App\Http\Controllers\Admin\AdultContentViewTermController::class, 'update'])->name('adult.view_terms.update');

        // Publicar contenido adulto
        Route::get('adult-content/publish-terms',[\App\Http\Controllers\Admin\AdultContentPublishTermController::class, 'index'])->name('adult.publish_terms.index');
        Route::put('adult-content/publish-terms/{adultContentPublishTerm}',[\App\Http\Controllers\Admin\AdultContentPublishTermController::class, 'update'])->name('adult.publish_terms.update');

        // Alertas
        Route::get('alerts',[\App\Http\Controllers\Admin\AlertController::class, 'index'])->name('alerts.index');
        Route::put('alerts/{alert}',[\App\Http\Controllers\Admin\AlertController::class, 'update'])->name('alerts.update');

        // Solicitud de Recargas de saldo
        Route::get('/reload-request', [ReloadRequestController::class, 'index'])->name('admin.reload-request.index');
        Route::post('/reload-request/{id}/approve', [ReloadRequestController::class, 'approve'])->name('admin.reload-request.approve');
        Route::post('/reload-request/{id}/reject', [ReloadRequestController::class, 'reject'])->name('admin.reload-request.reject');
        Route::get('/reload-request/pending-count', [ReloadRequestController::class, 'pendingCount'])->name('admin.reload-request.pending-count');

        // Configuración
        Route::get('/config', [ConfigController::class, 'index'])->name('admin.config');

            // Administracion de precios de etiquetas
            Route::post('/admin/config/label-price', [LabelPriceController::class,'update'])->name('admin.config.label-price.update');

            // Administracion de categorias
            Route::get('/config/categorias', [CategoryController::class, 'index'])->name('admin.config.categories');
            Route::post('/config/categorias/store', [CategoryController::class, 'store'])->name('admin.config.categories.store');
            Route::put('/config/categorias/update/{id}', [CategoryController::class, 'update'])->name('admin.config.categories.update');
            Route::delete('/config/categorias/delete/{id}', [CategoryController::class, 'destroy'])->name('admin.config.categories.destroy');
            // Subcategorías
            Route::post('/config/sub/store', [SubcategoryController::class, 'store'])->name('admin.config.subcategories.store');
            Route::put('/config/sub/update/{id}', [SubcategoryController::class, 'update'])->name('admin.config.subcategories.update');
            Route::delete('/config/sub/delete/{id}', [SubcategoryController::class, 'destroy'])->name('admin.config.subcategories.destroy');
            // Campos
            Route::post('/config/field/store', [FieldController::class, 'store'])->name('admin.config.fields.store');
            Route::put('/config/field/update/{id}', [FieldController::class, 'update'])->name('admin.config.fields.update');
            Route::delete('/config/field/delete/{id}', [FieldController::class, 'destroy'])->name('admin.config.fields.destroy');

            // Gestión de empleados
            Route::get('/config/employees', [EmployeeController::class, 'index'])->name('admin.config.employees');
            Route::get('/config/employees/create', [EmployeeController::class, 'create'])->name('admin.config.employees.create');
            Route::post('/config/employees/store', [EmployeeController::class, 'store'])->name('admin.config.employees.store');
            Route::get('/config/employees/edit/{employee}', [EmployeeController::class, 'edit'])->name('admin.config.employees.edit');
            Route::put('/config/employees/update/{employee}', [EmployeeController::class, 'update'])->name('admin.config.employees.update');
            Route::put('/config/employees/toggle/{employee}', [EmployeeController::class, 'toggle'])->name('admin.config.employees.toggle');

            // Gestión de Clientes
            Route::get('/config/clients', [ClientController::class, 'index'])->name('admin.config.clients');
            Route::get('/config/clients/{client}/edit', [ClientController::class, 'edit'])->name('admin.config.clients.edit');
            Route::put('/config/clients/{client}', [ClientController::class, 'update'])->name('admin.config.clients.update');
            Route::put('/config/clients/{client}/toggle', [ClientController::class, 'toggleStatus'])->name('admin.config.clients.toggle');
            Route::put('/config/clients/{client}/verify',[ClientController::class, 'verify'])->name('admin.config.clients.verify');

            // Caja
            Route::get('/config/cash', [CashBoxController::class, 'index'])->name('admin.config.cash.index');
            Route::post('/config/cash/open', [CashBoxController::class, 'open'])->name('admin.config.cash.open');
            Route::post('/config/cash/{id}/movement', [CashBoxController::class, 'addMovement'])->name('admin.config.cash.movement');
            Route::post('/config/cash/{id}/close', [CashBoxController::class, 'close'])->name('admin.config.cash.close');
            Route::get('/config/cash/{id}', [CashBoxController::class, 'show'])->name('admin.config.cash.show');

            // Metodos de Pago
            Route::get('/config/payment-methods', [PaymentMethodController::class, 'index'])->name('admin.config.payment_methods.index');
            Route::get('/config/payment-methods/create', [PaymentMethodController::class, 'create'])->name('admin.config.payment_methods.create');
            Route::post('/config/payment-methods/store', [PaymentMethodController::class, 'store'])->name('admin.config.payment_methods.store');
            Route::get('/config/payment-methods/edit/{id}', [PaymentMethodController::class, 'edit'])->name('admin.config.payment_methods.edit');
            Route::put('/config/payment-methods/update/{id}', [PaymentMethodController::class, 'update'])->name('admin.config.payment_methods.update');
            Route::delete('/config/payment-methods/delete/{id}', [PaymentMethodController::class, 'destroy'])->name('admin.config.payment_methods.delete');

            // Configuración del sistema 
            Route::get('/config/system', [SystemSettingController::class, 'edit'])->name('admin.config.system');
            Route::put('/config/system', [SystemSettingController::class, 'update'])->name('admin.config.system.update');

            // Configuración del sistema las redes sociales de contacto 
            Route::post('/config/system/social', [SystemSocialLinkController::class, 'store'])->name('admin.config.system.social.store');
            Route::delete('/config/system/social/{social}', [SystemSocialLinkController::class, 'destroy'])->name('admin.config.system.social.destroy');
        
            // Libro de reclamaciones (configuración)
            Route::get('/config/complaint-book',[ComplaintBookSettingController::class, 'index'])->name('admin.config.complaint_book_settings.index');
            Route::put('/config/complaint-book',[ComplaintBookSettingController::class, 'updateView'])->name('admin.config.complaint_book_settings.update');
            Route::get('/complaint-book', [ComplaintBookSettingController::class, 'show']);
            Route::put('/complaint-book', [ComplaintBookSettingController::class, 'update']);

            // Subcategory Images
            Route::get('/config/subcategory/{id}/images',[SubcategoryImageController::class, 'index'])->name('admin.config.subcategory.images.index');
            Route::post('/config/subcategory-images',[SubcategoryImageController::class, 'store'])->name('admin.config.subcategory.images.store');
            Route::delete('/config/subcategory-images/{id}',[SubcategoryImageController::class, 'destroy'])->name('admin.config.subcategory.images.delete');

        //Historial de Anuncios
        Route::get('/anuncios/historial', [AdsHistoryController::class, 'index'])->name('admin.ads-history.index');
        Route::get('/ad/{id}/notify/{status}', [AdsHistoryController::class, 'notifyUser'])->name('admin.ads.notify');
        Route::post('/ad/{id}/approve', [AdsHistoryController::class, 'approve'])->name('admin.ads.approve');
        Route::post('/ad/{id}/reject', [AdsHistoryController::class, 'reject'])->name('admin.ads.reject');
        Route::post('/ad/{ad}/verify',[AdsHistoryController::class, 'verify'])->name('admin.ads.verify');

        // Gestión de reclamos (admin)
        Route::get('/complaints-management', [ComplaintController::class, 'indexView'])->name('admin.config.complaints.index');
        Route::get('/complaints/{complaint}', [ComplaintController::class, 'show'])->name('admin.config.complaints.show');
        Route::put('/complaints/{complaint}', [ComplaintController::class, 'update'])->name('admin.config.complaints.update');
        Route::delete('/complaints/{complaint}', [ComplaintController::class, 'destroy'])->name('admin.config.complaints.destroy');


        // Termunos y condiciones login/register
        //Route::get('/config/privacy-policy', [PrivacyPolicySettingController::class, 'index'])->name('admin.adult.authentication_terms.index');
        //Route::put('/config/privacy-policy', [PrivacyPolicySettingController::class, 'update'])->name('admin.adult.authentication_terms.update');
    });
});
