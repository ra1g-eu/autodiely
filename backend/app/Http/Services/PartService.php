<?php

namespace App\Http\Services;

use App\Models\Part;
use App\Traits\CanInstantiate;
use Illuminate\Database\Eloquent\Collection;

class PartService
{
    use CanInstantiate;

    /**
     * @return Collection
     */
    public function getParts(): Collection
    {
        return Part::query()->with('car')->get();
    }

    /**
     * @param array $validated
     * @return array
     */
    public function saveParts(array $validated): array
    {
        if ($this->carPartWithSerialNumberExists($validated['serialnumber'], $validated['car_id'])) {
            return [
                false,
                'Part with this serial number for this car already exists'
            ];
        }

        return [
            true,
            Part::query()->create($validated)
        ];
    }

    /**
     * @param int $id
     * @return Part|null
     */
    public function getPart(int $id): ?Part
    {
        return Part::query()
            ->where('id', $id)
            ->first();
    }

    /**
     * @param int $carId
     * @return Collection
     */
    public function getPartsForCar(int $carId): Collection
    {
        return Part::query()
            ->where('car_id', $carId)
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * @param array $validated
     * @param int $id
     * @return array
     */
    public function updatePart(array $validated, int $id): array
    {
        $part = $this->getPart($id);

        if (!$part) {
            return [
                false,
                null
            ];
        }

        if ($this->carPartWithSerialNumberExists($validated['serialnumber'], $validated['car_id'])) {
            return [
                false,
                'Part with this serial number for this car already exists'
            ];
        }

        $part->update($validated);

        return [
            true,
            $part
        ];
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deletePart(int $id): bool
    {
        $part = $this->getPart($id);

        return $part->delete();
    }

    /**
     * @param string $serialNumber
     * @param int $carId
     * @return bool
     */
    public function carPartWithSerialNumberExists(string $serialNumber, int $carId): bool
    {
        return Part::query()
            ->where('serialnumber', $serialNumber)
            ->where('car_id', $carId)
            ->exists();
    }
}
