<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'document_date' => fake()->date('Y-m-d'),
            'document_no' => (string) fake()->numberBetween(1, 99999),
            'document_year' => (string) fake()->numberBetween(2020, 2035),
            'employee_name' => fake()->name(),
            'position' => fake()->jobTitle(),
            'assignment_station' => fake()->city().' Station',
            'conforme_name' => fake()->name(),
            'pdf_path' => null,
        ];
    }
}
