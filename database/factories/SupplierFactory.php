<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'contact' => fake()->unique()->phoneNumber(),
            'email' => fake()->unique()->email(),
            'country' => fake()->country(),
            'product' => Str::random(8),
            'address' => fake()->address()
        ];
    }
}
