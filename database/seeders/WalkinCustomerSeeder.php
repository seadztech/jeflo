<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class WalkinCustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::firstOrCreate(
            ['phone_number' => 'WALKIN'],
            [
                'name' => 'Walk-in Customer',
                'email' => null,
            ]
        );
    }
}
