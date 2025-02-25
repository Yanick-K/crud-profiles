<?php

use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(ProfileController::class)->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
       Route::post('/profiles', 'store');
       Route::put('/profiles/{profile}', 'update');
       Route::delete('/profiles/{profile}', 'destroy');
    });
    Route::get('/profiles/{profile}', 'show');
    Route::get('/profiles', 'index');
});

Route::post('/tokens/create', function (Request $request) {

    $request->validate([
        'email' => 'required|string|email|exists:users,email',
        'password' => 'required|string'
    ]);

    $user = User::where('email', $request->email)->first();

    if (Hash::check($request->password, $user->password)) {
        $token = $user->createToken('api-auth');
        return ['token' => $token->plainTextToken];
    }
    return 'Invalid credentials';
});
