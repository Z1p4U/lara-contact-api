<?php

use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\SearchHistoryController;
use App\Http\Middleware\ApiTokenCheck;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix("v1")->group(function () {

    Route::middleware('auth:sanctum')->group(function () {

        Route::apiResource('contact', ContactController::class);
        Route::delete("contact/{id}/trash", [ContactController::class, 'destroy']);
        Route::patch("contact/{id}/restore", [ContactController::class, 'restore']);
        Route::delete("contact/{id}/delete", [ContactController::class, 'forceDelete']);
        Route::get("contact/trash", [ContactController::class, 'trash']);

        Route::post("logout", [ApiAuthController::class, 'logout']);
        Route::post("logout-all", [ApiAuthController::class, 'logoutAll']);
        Route::get("devices", [ApiAuthController::class, 'devices']);

        Route::get("search-history", [SearchHistoryController::class, 'index']);
        Route::delete("search-history/{id}", [SearchHistoryController::class, 'destroy']);

        Route::get("favorites", [FavouriteController::class, 'index']);
        Route::get("favorites/{id}", [FavouriteController::class, 'favToggle']);
    });


    Route::post("register", [ApiAuthController::class, 'register']);
    Route::post("login", [ApiAuthController::class, 'login']);
});
