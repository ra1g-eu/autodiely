<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'registration_number', 'is_registered'];

    protected $casts = [
        'is_registered' => 'boolean',
    ];

    public function parts(): HasMany
    {
        return $this->hasMany(Part::class);
    }

    // Validation rules for creating/updating cars
    public static function validationRules($id = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'is_registered' => 'required|boolean',
            'registration_number' => 'nullable|string|max:255',
        ];

        if (request('is_registered') === true) {
            $rules['registration_number'] = 'required|string|max:255';
        }

        return $rules;
    }
}
