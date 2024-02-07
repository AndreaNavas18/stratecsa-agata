<?php

use App\Http\Controllers\Customers\CustomerServiceController;

Route::middleware(['can:customers_show'])->namespace('Customers')->name('customers.')->group(function () {

    /**********************
    *--------- servicios
    ***********************/

    Route::get('/servicios/index', [ CustomerServiceController::class, 'indexAll' ])->name('services.index.all')
    ->where('id', '[0-9]+')
    ->middleware('can:customers_edit');

    Route::get('/servicios/show/{id}', [ CustomerServiceController::class, 'showService' ])->name('services.show.service')
    ->where('id', '[0-9]+')
    ->middleware('can:customers_edit');

    Route::get('/clientes/servicios/editar/{customerId}', [ CustomerServiceController::class, 'index' ])->name('services.index')
    ->where('id', '[0-9]+')
    ->middleware('can:customers_edit');

    Route::get('/clientes/servicios/editar/buscar/{customerId}', [ CustomerServiceController::class, 'indexSearch' ])->name('services.index.search')
    ->where('id', '[0-9]+')
    ->middleware('can:customers_edit');

    Route::get('/clientes/servicios/ver/{customerId}', [ CustomerServiceController::class, 'show' ])->name('services.show')
    ->where('id', '[0-9]+')
    ->middleware('can:customers_edit');

    Route::get('/clientes/servicios/ver/buscar/{customerId}', [ CustomerServiceController::class, 'showSearch' ])->name('services.show.search')
    ->where('id', '[0-9]+')
    ->middleware('can:customers_edit');

    Route::post('/clientes/servicios/crear/{customerId?}',[ CustomerServiceController::class, 'store' ])->name('services.store')
    ->middleware('can:customers_create');

    Route::put('/clientes/servicios/editar/{id}', [ CustomerServiceController::class, 'update' ])->name('services.update')
    ->where('id', '[0-9]+')
    ->middleware('can:customers_edit');


	Route::delete('/servicios/eliminar/{id}', [ CustomerServiceController::class, 'destroy' ])->name('services.destroy')
    ->where('id', '[0-9]+')
    ->middleware('can:empleados_destroy');
});


