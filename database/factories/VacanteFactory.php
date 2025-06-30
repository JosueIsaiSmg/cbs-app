<?php

namespace Database\Factories;

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
            'area' => $this->faker->word(),
            'sueldo' => $this->faker->numberBetween(1000, 10000),
            'activo' => $this->faker->boolean(),
        ];
    }
}
