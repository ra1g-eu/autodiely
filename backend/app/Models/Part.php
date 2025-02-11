<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Part extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'serialnumber', 'car_id'];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    // Validation rules for creating/updating parts
    public static function validationRules($id = null): array
    {
        return [
            'name' => 'required|string|max:255',
            'serialnumber' => 'required|string|max:255',
            'car_id' => 'required|exists:cars,id',
        ];
    }
}
