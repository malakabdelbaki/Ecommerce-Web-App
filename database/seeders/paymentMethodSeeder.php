<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class paymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed specific payment methods
        PaymentMethod::create([
            'user_id' => 1,
            'type' => 'credit_card',
            'digits' => '1234', // Dummy digits
        ]);

        PaymentMethod::create([
            'user_id' => 1,
            'type' => 'paypal',
            'digits' => null, // No digits needed for PayPal
        ]);

        PaymentMethod::create([
            'user_id' => 1,
            'type' => 'bank_transfer',
            'digits' => null, // No digits needed for bank transfer
        ]);

        // Optionally, generate random payment methods using the factory
        PaymentMethod::factory()->count(10)->create();
    }
}
