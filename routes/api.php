<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\LaptopController;
use App\Http\Controllers\CreateStudentsTableController;
use App\Http\Controllers\RentalController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/students', [CreateStudentsTableController::class, 'index']);
Route::get('/students/search', [CreateStudentsTableController::class, 'search']);
Route::get('/students/{id}', [CreateStudentsTableController::class, 'show']);
Route::post('/students', [CreateStudentsTableController::class, 'store']);
Route::put('/students/{id}', [CreateStudentsTableController::class, 'update']);
Route::delete('/students/{id}', [CreateStudentsTableController::class, 'destroy']);

Route::get('/laptops', [LaptopController::class, 'index']);
Route::post('/laptops', [LaptopController::class, 'store']);
Route::get('/laptops/{id}', [LaptopController::class, 'show']);
Route::put('/laptops/{id}', [LaptopController::class, 'update']);
Route::delete('/laptops/{id}', [LaptopController::class, 'destroy']);
Route::post('laptops/{id}/upload-image', [LaptopController::class, 'uploadImage']);
Route::get('laptops/{id}/image', [LaptopController::class, 'getImage']);
Route::delete('laptops/{id}/image', [LaptopController::class, 'removeImage']);


Route::get('/rentals', [RentalController::class, 'index']);
Route::post('/rentals', [RentalController::class, 'store']);
Route::put('/rentals/{id}/return', [RentalController::class, 'return']);
Route::put('/rentals/{id}/cancel', [RentalController::class, 'cancel']);
Route::put('/rentals/{id}/approve', [RentalController::class, 'approve']);


Route::get('/fetch-user', [UserController::class, 'showUser']);
Route::post('/register-user', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);



Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out']);
});





// Student routes
Route::prefix('student')->group(function () {
    Route::post('/login', [UserController::class, 'studentLogin']);
    
    Route::post('/student/rent-laptop', [StudentController::class, 'rentLaptop']);
    Route::get('/student/rental-history', [StudentController::class, 'getRentalHistory']);


    
});

Route::apiResource('rentals', RentalController::class);
Route::get('rentals/student/{studentId}', [RentalController::class, 'getByStudent']);
Route::get('rentals/active', [RentalController::class, 'getActiveRentals']);
Route::get('rentals/check-overdue', [RentalController::class, 'checkOverdueRentals']);
