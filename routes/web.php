<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AffiliateController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/affiliates', [AffiliateController::class, 'index']);
Route::get('/api/affiliates/filtered', [AffiliateController::class, 'filtered']);

Route::get('/affiliates', function () {
    return view('affiliates');
});

Route::get('/affiliates/filtered', function () {
    return view('affiliates-filtered');
});

