<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SalesSeeder extends Seeder
{
    public function run()
    {
        $statuses = ['completed', 'pending', 'cancelled'];
        $paymentMethods = ['cash', 'mpesa', 'card'];
        
        $sales = [];

        for ($i = 0; $i < 5000; $i++) {
            // Random date in the last 12 months
            $date = Carbon::now()->subDays(rand(0, 365))->setTime(rand(8, 20), rand(0, 59));

            $sales[] = [
                'item_id' => rand(1, 50), // Assuming you have items with IDs 1-50
                'customer_id' => 1, // Walk-in customer
                'total_amount' => rand(100, 5000),
                'status' => $statuses[array_rand($statuses)],
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'created_at' => $date,
                'actionBy' => 1,
                'updated_at' => $date,
            ];
        }

        DB::table('sales')->insert($sales);
    }
}
