<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Order::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'address_id' => Address::factory(),
            'payment_method_id' => PaymentMethod::factory(),
            'total' => $this->faker->randomFloat(2, 50, 500),  // Total between $50 and $500
            'status' => $this->faker->randomElement(['Pending', 'Completed', 'Shipped']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
