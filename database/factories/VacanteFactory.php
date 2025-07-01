<?php

namespace Database\Factories;

use App\Models\Vacante;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vacante>
 */
class VacanteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'area' => fake()->word(),
            'sueldo' => fake()->numberBetween(1000, 10000),
            'activo' => fake()->boolean(),
        ];
    }
}
