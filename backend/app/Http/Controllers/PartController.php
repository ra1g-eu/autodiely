<?php

namespace App\Http\Controllers;

use App\Http\Services\PartService;
use App\Models\Part;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartController extends Controller
{
    use JsonResponseTrait;

    protected PartService $partService;

    public function __construct(PartService $partService)
    {
        $this->partService = $partService;
    }

    /**
     * @return JsonResponse
     */
    public function getAllParts(): JsonResponse
    {
        $parts = $this->partService->getParts();

        return $this->successResponse($parts);
    }

    /**
     * @param int $car_id
     * @return JsonResponse
     */
    public function getPartsForCar(int $car_id): JsonResponse
    {
        $parts = $this->partService->getPartsForCar($car_id);

        return $this->successResponse($parts);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createPart(Request $request): JsonResponse
    {
        $validated = $request->validate(Part::validationRules());
        [$status, $response] = $this->partService->saveParts($validated);

        if (!$status) {
            return $this->errorResponse($response);
        }

        return $this->successResponse($response);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function getPartById(int $id): JsonResponse
    {
        $carPart = $this->partService->getPart($id);

        if (!$carPart) {
            return $this->errorResponse('Part not found', 404);
        }

        return $this->successResponse($carPart);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updatePart(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate(Part::validationRules());
        [$status, $response] = $this->partService->updatePart($validated, $id);

        if (!$status) {
            return $this->errorResponse($response);
        }

        return $this->successResponse($response);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function deletePart(int $id): JsonResponse
    {
        $part = $this->partService->deletePart($id);

        if (!$part) {
            return $this->errorResponse('Part not deleted');
        }

        return $this->successResponse('Part deleted');
    }
}
