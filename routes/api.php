<?php

use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(ProfileController::class)->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
       Route::post('/profiles', 'store');
       Route::put('/profile/{profile}', 'update');
    });
    Route::get('/profiles/{profile}', 'show');
    Route::get('/profiles', 'index');
});

Route::post('/tokens/create', function (Request $request) {

    //$user = User::where('email', $request->email)->first();

    //permettre une meilleure auth de l'utilisateur


    $user = User::all()->first();
    $token = $user->createToken('api-auth');

    return ['token' => $token->plainTextToken];
});
