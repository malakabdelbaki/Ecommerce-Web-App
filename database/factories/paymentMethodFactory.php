<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\paymentMethod>
 */
class paymentMethodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = PaymentMethod::class;

    public function definition(): array
    {
        // List of possible payment method types
        $paymentTypes = ['credit_card', 'paypal', 'bank_transfer'];

        return [
            'name' => $this->faker->randomElement($paymentTypes),
        ];
    }
}
