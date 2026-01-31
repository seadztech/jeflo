<?php

namespace Database\Seeders;

use App\Models\Transactions;
use App\Models\SalesItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        // $salesItems = SalesItem::pluck('id')->toArray();

        // if (empty($salesItems)) {
        //     $this->command->warn('No sales items found. Please seed sales_items table first.');
        //     return;
        // }

        foreach (range(1, 1000) as $i) {
            Transactions::create([
                'sales_item_id' => null,
                'type' => fake()->randomElement([1, 2]), // e.g. 1 = Cash, 2 = Mpesa
                'transaction_code' => strtoupper(Str::random(10)),
                'response' => json_encode([
                    'status' => 'success',
                    'message' => 'Transaction completed',
                    'reference' => Str::uuid(),
                ]),
                'amount' => fake()->randomFloat(2, 100, 5000),
            ]);
        }
    }
}
