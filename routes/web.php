<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AdvertisingUser\AdvertisementController;
use App\Http\Controllers\AdvertisingUser\RechargeController;

use App\Http\Controllers\Admin\ReloadRequestController;
use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\CategoryConfigController;
use App\Http\Controllers\Admin\AdsRequestController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CashBoxController;


/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

// Página principal
Route::get('/', function () {
    return view('public.home');
})->name('home');

Route::get('/api/ads', function () {
    $adsUrgent = \App\Models\Advertisement::where('published', 1)
        ->where('status', 'aceptado')
        ->where('urgent_publication', 1)
        ->with(['category', 'subcategory', 'images'])
        ->get()
        ->shuffle()
        ->take(10)
        ->map(function($ad){
            $ad->full_url = $ad->detail_url; // agrega URL amigable
            return $ad;
        });

    $adsNormal = \App\Models\Advertisement::where('published', 1)
        ->where('status', 'aceptado')
        ->where('urgent_publication', 0)
        ->with(['category', 'subcategory', 'images'])
        ->get()
        ->shuffle()
        ->take(20)
        ->map(function($ad){
            $ad->full_url = $ad->detail_url; 
            return $ad;
        });

    return response()->json([
        'urgent' => $adsUrgent->values(),
        'normal' => $adsNormal->values()
    ]);
});



Route::get('/ad/{id}', [AdvertisementController::class, 'show'])
        ->name('public.ad.show');

        Route::get('/detalle-anuncio/{slug}/{id}', [AdvertisementController::class, 'show'])
    ->name('public.ad.detail');

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
    Route::get('/my-ads', [AdvertisementController::class, 'index'])
        ->name('my-ads.index');

     // Crear anuncio
    Route::get('/my-ads/create', [\App\Http\Controllers\AdvertisingUser\MyAdRequestController::class, 'create'])
        ->name('my-ads.createAd');

    // Guardar solicitud
    Route::post('/my-ads/create', [\App\Http\Controllers\AdvertisingUser\MyAdRequestController::class, 'store'])
        ->name('my-ads.storeAdRequest');

    // Cargar dependencias dinámicas
    Route::get('/my-ads/subcategories/{id}', [\App\Http\Controllers\AdvertisingUser\MyAdRequestController::class, 'loadSubcategories']);
    Route::get('/fields/{id}', [\App\Http\Controllers\AdvertisingUser\MyAdRequestController::class, 'loadFields']);


    /*
    |--------------------------------------------------------------------------
    | Recargas de saldo
    |--------------------------------------------------------------------------
    */
    Route::get('/recharges', [RechargeController::class, 'index'])
        ->name('recharges.index');

    Route::post('/recharges', [RechargeController::class, 'store'])
        ->name('recharges.store');
});

/*
|--------------------------------------------------------------------------
| RUTAS PRIVADAS — SOLO admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // Solicitud de Recargas de saldo
    Route::get('/reload-request', [ReloadRequestController::class, 'index'])
        ->name('admin.reload-request.index');

    Route::post('/reload-request/{id}/approve', [ReloadRequestController::class, 'approve'])
        ->name('admin.reload-request.approve');

    Route::post('/reload-request/{id}/reject', [ReloadRequestController::class, 'reject'])
        ->name('admin.reload-request.reject');

    // Configuración
    Route::get('/config', [ConfigController::class, 'index'])
        ->name('admin.config');
    
        // Administracion de categorias
        Route::get('/config/categorias', [CategoryConfigController::class, 'index'])
            ->name('admin.config.categories');

        // Gestión de empleados
        Route::get('/config/employees', [EmployeeController::class, 'index'])
            ->name('admin.config.employees');

            // Gestión de Clientes
        Route::get('/config/clients', [ClientController::class, 'index'])
            ->name('admin.config.clients');

        // Caja
        Route::get('/cash', [CashBoxController::class, 'index'])
            ->name('admin.cash.index');
        Route::post('/cash/open', [CashBoxController::class, 'open'])->name('admin.cash.open');
        Route::post('/cash/{id}/movement', [CashBoxController::class, 'addMovement'])->name('admin.cash.movement');
        Route::post('/cash/{id}/close', [CashBoxController::class, 'close'])->name('admin.cash.close');
        Route::get('/cash/{id}', [CashBoxController::class, 'show'])->name('admin.cash.show');



    // Solicitudes de anuncios
    Route::get('/ads-requests', [AdsRequestController::class, 'index'])
        ->name('admin.ads-requests.index');

    Route::post('/ads-requests/{id}/approve', [AdsRequestController::class, 'approve'])
        ->name('admin.ads-requests.approve');

    Route::post('/ads-requests/{id}/reject', [AdsRequestController::class, 'reject'])
        ->name('admin.ads-requests.reject');

});
