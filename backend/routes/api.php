<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CarController;
use App\Http\Controllers\PartController;

Route::prefix('cars')->group(function () {
    Route::get('/', [CarController::class, 'getAllCars']);
    Route::post('/', [CarController::class, 'createCar']);
    Route::get('/{id}', [CarController::class, 'getCarById']);
    Route::put('/{id}', [CarController::class, 'updateCar']);
    Route::delete('/{id}', [CarController::class, 'deleteCar']);
});

Route::prefix('parts')->group(function () {
    Route::get('/', [PartController::class, 'getAllParts']);
    Route::get('/car/{car_id}', [PartController::class, 'getPartsForCar']);
    Route::post('/', [PartController::class, 'createPart']);
    Route::get('/{id}', [PartController::class, 'getPartById']);
    Route::put('/{id}', [PartController::class, 'updatePart']);
    Route::delete('/{id}', [PartController::class, 'deletePart']);
});
