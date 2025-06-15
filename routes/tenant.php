<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    'universal',
    PreventAccessFromCentralDomains::class,
    \App\Http\Middleware\Tenant\CheckTenantStatus::class,
    Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,

])->group(function () {

    /** ROUTES DU PATRON */

    /** CONNECTED */
    Route::controller(App\Http\Controllers\Tenant\Patron\AuthController::class)
        ->group(function (){
            Route::get('/login','loginView')->name('patron.loginView');
            Route::post('/login','login')->name('patron.login');

            Route::delete('/logout','logout')->name('patron.logout');

        });
    /** DISCONNECTED */
    Route::controller(App\Http\Controllers\Tenant\Patron\BaseController::class)
        ->middleware([ App\Http\Middleware\Tenant\PatronMiddleware::class ])
        ->group(function (){
            Route::get('/patron-panel','patronPanel')->name('patron.patronPanel');
        });

});
