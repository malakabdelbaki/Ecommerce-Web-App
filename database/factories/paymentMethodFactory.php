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
            'user_id'=>  $this->faker->numberBetween(1, User::all()->count()),
            'type' => $this->faker->randomElement($paymentTypes),
            'digits' => $this->faker->creditCardNumber, // Last 4 digits of card number for credit cards
        ];
    }
}
