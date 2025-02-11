<?php

namespace App\Http\Controllers;

use App\Http\Services\CarService;
use App\Models\Car;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarController extends Controller
{
    use JsonResponseTrait;

    protected CarService $carService;

    public function __construct(CarService $carService)
    {
        $this->carService = $carService;
    }

    /**
     * @return JsonResponse
     */
    public function getAllCars(): JsonResponse
    {
        $cars = $this->carService->getCars();

        return $this->successResponse($cars);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createCar(Request $request): JsonResponse
    {
        $validated = $request->validate(Car::validationRules());
        [$status, $response] = $this->carService->saveCar($validated);

        if (!$status) {
            return $this->errorResponse($response);
        }

        return $this->successResponse($response);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function getCarById(int $id): JsonResponse
    {
        $car = $this->carService->getCar($id);

        if (!$car) {
            return $this->errorResponse('Car not found', 404);
        }

        return $this->successResponse($car);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateCar(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate(Car::validationRules($id));
        [$status, $response] = $this->carService->updateCar($validated, $id);

        if (!$status) {
            return $this->errorResponse($response);
        }

        return $this->successResponse($response);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function deleteCar(int $id): JsonResponse
    {
        $car = $this->carService->deleteCar($id);

        if (!$car) {
            return $this->errorResponse('Car not deleted');
        }

        return $this->successResponse('Car deleted');
    }
}
