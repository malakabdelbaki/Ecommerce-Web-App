<?php

namespace Database\Seeders;

use App\Models\Address;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Address::create([
            'user_id' => 1,  // Assuming user with ID 1 exists
            'label' => 'Home',
            'name' => 'John Doe',
            'address_line1' => '123 Main St',
            'address_line2' => 'Apt 4B',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'USA',
            'phone_number' => '555-555-5555',
        ]);

        // Optionally, generate additional random addresses using the factory
        Address::factory()->count(10)->create();
    }
}
