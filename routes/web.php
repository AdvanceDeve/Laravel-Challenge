<?php

use App\Jobs\FetchGuardianJob;
use App\Jobs\FetchNewsApiJob;
use App\Jobs\FetchNYTJob;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/run-news-jobs', function () {
    dispatch(new FetchGuardianJob());
    dispatch(new FetchNYTJob());
    dispatch(new FetchNewsApiJob());

    return response()->json(['message' => 'All jobs dispatched successfully!']);
});