<?php

namespace Database\Factories;

use App\Models\Entrevista;
use App\Models\Vacante;
use App\Models\Prospecto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entrevista>
 */
class EntrevistaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vacante' => fn() => Vacante::factory(),
            'prospecto' => fn() => Prospecto::factory(),
            'fecha_entrevista' => fake()->dateTimeBetween('now', '+2 months'),
            'notas' => fake()->paragraph(),
            'reclutado' => fake()->boolean(),
        ];
    }

    /**
     * Indicate that the interview resulted in recruitment.
     */
    public function reclutado(): static
    {
        return $this->state(fn (array $attributes) => [
            'reclutado' => true,
        ]);
    }

    /**
     * Indicate that the interview did not result in recruitment.
     */
    public function noReclutado(): static
    {
        return $this->state(fn (array $attributes) => [
            'reclutado' => false,
        ]);
    }
} 