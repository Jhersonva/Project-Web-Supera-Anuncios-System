<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ChatController;


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
use App\Http\Controllers\Admin\UrgentPriceController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\FeaturedPriceController;
use App\Http\Controllers\Admin\PremierePriceController;
use App\Http\Controllers\Admin\SystemSettingController;


/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

// Página principal
Route::get('/', function () {
    return view('public.home');
})->name('home');

Route::get('/api/ads', function (Request $request) {

    $pageFeatured = $request->get('page_featured', 1);
    $pageUrgent = $request->get('page_urgent', 1);
    $pageNormal = $request->get('page_normal', 1);
    $pagePremiere = $request->get('page_premiere', 1);

    $adsFeatured = \App\Models\Advertisement::where('published', 1)
        ->where('status', 'publicado')
        ->where('featured_publication', 1)
        ->where('expires_at', '>=', now())
        ->with(['category', 'subcategory', 'images'])
        ->orderBy('created_at', 'desc')
        ->paginate(20, ['*'], 'page_featured', $pageFeatured);

    $adsUrgent = \App\Models\Advertisement::where('published', 1)
        ->where('status', 'publicado')
        ->where('urgent_publication', 1)
        ->where('featured_publication', 0)
        ->where('expires_at', '>=', now())
        ->with(['category', 'subcategory', 'images'])
        ->orderBy('created_at', 'desc')
        ->paginate(20, ['*'], 'page_urgent', $pageUrgent); 

    $adsPremiere = \App\Models\Advertisement::where('published', 1)
        ->where('status', 'publicado')
        ->where('premiere_publication', 1)
        ->where('featured_publication', 0)
        ->where('urgent_publication', 0)
        ->where('expires_at', '>=', now())
        ->with(['category', 'subcategory', 'images'])
        ->orderBy('created_at', 'desc')
        ->paginate(20, ['*'], 'page_premiere', $pagePremiere);

    $adsNormal = \App\Models\Advertisement::where('published', 1)
        ->where('status', 'publicado')
        ->where('urgent_publication', 0)
        ->where('featured_publication', 0)
        ->where('expires_at', '>=', now())
        ->with(['category', 'subcategory', 'images'])
        ->orderBy('created_at', 'desc')
        ->paginate(50, ['*'], 'page_normal', $pageNormal);

    $adsFeatured->getCollection()->transform(function($ad){
        $ad->full_url = $ad->detail_url;

        $date = $ad->approved_at ?: $ad->created_at;
        $ad->time_ago = $date->locale('es')->diffForHumans();

        // Datos usuario
        $ad->whatsapp = $ad->user->whatsapp ?? $ad->user->phone ?? null;
        $ad->call_phone = $ad->user->call_phone ?? $ad->user->phone ?? null;

        // Montos
        $ad->amount_visible = $ad->amount_visible;
        $ad->amount = $ad->amount;

        return $ad;
    });

    // Agregar URL completo
    $adsUrgent->getCollection()->transform(function($ad){
        $ad->full_url = $ad->detail_url;

        $date = $ad->approved_at ?: $ad->created_at;
        $ad->time_ago = $date->locale('es')->diffForHumans();

        // AGREGAR CAMPOS DEL USUARIO
        $ad->whatsapp = $ad->user->whatsapp ?? $ad->user->phone ?? null;
        $ad->call_phone = $ad->user->call_phone ?? $ad->user->phone ?? null;

        $ad->amount_visible = $ad->amount_visible; 
        $ad->amount = $ad->amount;                

        return $ad;
    });

    $adsPremiere->getCollection()->transform(function($ad){
        $ad->full_url = $ad->detail_url;

        $date = $ad->approved_at ?: $ad->created_at;
        $ad->time_ago = $date->locale('es')->diffForHumans();

        $ad->whatsapp = $ad->user->whatsapp ?? $ad->user->phone ?? null;
        $ad->call_phone = $ad->user->call_phone ?? $ad->user->phone ?? null;

        $ad->amount_visible = $ad->amount_visible;
        $ad->amount = $ad->amount;

        return $ad;
    });

    $adsNormal->getCollection()->transform(function($ad){
        $ad->full_url = $ad->detail_url;

        $date = $ad->approved_at ?: $ad->created_at;
        $ad->time_ago = $date->locale('es')->diffForHumans();

        // Datos del usuario
        $ad->whatsapp = $ad->user->whatsapp ?? $ad->user->phone ?? null;
        $ad->call_phone = $ad->user->call_phone ?? $ad->user->phone ?? null;

        // CAMPOS QUE FALTABAN
        $ad->amount_visible = $ad->amount_visible;
        $ad->amount = $ad->amount;

        return $ad;
    });
        return response()->json([
            'featured' => $adsFeatured,
            'urgent' => $adsUrgent,
            'premiere' => $adsPremiere,
            'normal' => $adsNormal
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
|--------------------------------------------------------------------------
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
});


/*
|--------------------------------------------------------------------------
| RUTAS PRIVADAS — SOLO advertising_user
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('advertising')->group(function () {

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
    Route::delete('/my-ads/{id}/delete', [MyAdRequestController::class, 'destroy'])->name('my-ads.deleteAd');
    Route::get('/my-ads/{id}/stats', [MyAdRequestController::class, 'stats'])->name('my-ads.stats');

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

});

/*
|--------------------------------------------------------------------------
| RUTAS PRIVADAS — SOLO admin y employee
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // Solicitud de Recargas de saldo
    Route::get('/reload-request', [ReloadRequestController::class, 'index'])->name('admin.reload-request.index');
    Route::post('/reload-request/{id}/approve', [ReloadRequestController::class, 'approve'])->name('admin.reload-request.approve');
    Route::post('/reload-request/{id}/reject', [ReloadRequestController::class, 'reject'])->name('admin.reload-request.reject');
    Route::get('/reload-request/pending-count', [ReloadRequestController::class, 'pendingCount'])->name('admin.reload-request.pending-count');

    // Configuración
    Route::get('/config', [ConfigController::class, 'index'])->name('admin.config');

        // Administracion de categorias
        Route::post('/config/urgent-price/update', [UrgentPriceController::class, 'update'])->name('admin.config.urgent-price.update');
        Route::post('/config/featured-price/update', [FeaturedPriceController::class, 'update'])->name('admin.config.featured-price.update');
        Route::post('/config/premiere-price/update', [PremierePriceController::class, 'update'])->name('admin.config.premiere-price.update');

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
    
    //Historial de Anuncios
    Route::get('/anuncios/historial', [AdsHistoryController::class, 'index'])->name('admin.ads-history.index');
    Route::get('/ad/{id}/notify/{status}', [AdsHistoryController::class, 'notifyUser'])->name('admin.ads.notify');
    Route::post('/ad/{id}/approve', [AdsHistoryController::class, 'approve'])->name('admin.ads.approve');
    Route::post('/ad/{id}/reject', [AdsHistoryController::class, 'reject'])->name('admin.ads.reject');
});
