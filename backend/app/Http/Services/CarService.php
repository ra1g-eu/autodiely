<?php

namespace App\Http\Services;

use App\Models\Car;
use App\Traits\CanInstantiate;
use Illuminate\Database\Eloquent\Collection;

class CarService
{
    use CanInstantiate;

    /**
     * @return Collection
     */
    public function getCars(): Collection
    {
        return Car::query()->orderBy('updated_at', 'desc')->get();
    }

    /**
     * @param array $validated
     * @return array
     */
    public function saveCar(array $validated): array
    {
        if ($this->carRegistrationNumberExists($validated['registration_number'])) {
            return [
                false,
                'Car with registration number already exists'
            ];
        }

        return [
            true,
            Car::query()->create($validated)
        ];
    }

    /**
     * @param int $id
     * @return Car|null
     */
    public function getCar(int $id): ?Car
    {
        return Car::query()
            ->where('id', $id)
            ->first();
    }

    /**
     * @param array $validated
     * @param int $id
     * @return array
     */
    public function updateCar(array $validated, int $id): array
    {
        $car = $this->getCar($id);

        if (!$car) {
            return [
                false,
                'Car not found'
            ];
        }

        if ($this->carRegistrationNumberExists($validated['registration_number'])) {
            return [
                false,
                'Car with registration number already exists'
            ];
        }

        $car->update($validated);

        return [
            true,
            $car
        ];
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteCar(int $id): bool
    {
        $car = $this->getCar($id);

        return $car->delete();
    }

    /**
     * @param string|null $registrationNumber
     * @return bool
     */
    public function carRegistrationNumberExists(?string $registrationNumber): bool
    {
        return $registrationNumber && Car::query()->where('registration_number', $registrationNumber)->exists();
    }
}
