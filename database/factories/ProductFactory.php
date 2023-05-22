<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category = $this->faker->randomElement(['dairy', 'fruit', 'vegetable']);
        $name = $this->faker->unique()->words(2, true);
        $unit_weight = $category == 'dairy' ? $this->faker->numberBetween(1, 2) : $this->faker->randomFloat(1.2, 0.4, 3);

        return [
            'name' => $name,
            'desc' => $this->faker->sentence,
            'category' => $category,
            'unit_weight' => $unit_weight,
            'in_stock_quantity' => $this->faker->numberBetween(0, 1000),
            'price' => $this->faker->numberBetween(145, 1245),
        ];
    }
}