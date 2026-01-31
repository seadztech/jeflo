<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItemType;
use App\Models\Items;
use App\Models\Stockins;
use Illuminate\Support\Str;
use Carbon\Carbon;

class HardwareInventorySeeder extends Seeder
{
    public function run(): void
    {
        $itemTypes = [
            'Fasteners', 'Hand Tools', 'Power Tools', 'Electrical', 'Plumbing',
            'Building Materials', 'Paint & Finishing', 'Safety Gear', 'Welding',
            'Automotive', 'Garden & Outdoor', 'Measuring Tools', 'Cutting Tools',
            'Storage & Organization', 'Abrasives', 'Adhesives & Sealants',
            'Lighting', 'Machinery Parts', 'HVAC', 'Networking Hardware'
        ];

        foreach ($itemTypes as $typeName) {

            $itemType = ItemType::create([
                'name' => $typeName,
            ]);

            for ($i = 1; $i <= 30; $i++) {

                $item = Items::create([
                    'name'         => $typeName . ' Item ' . $i,
                    'item_type_id' => $itemType->id,
                    'strength'     => null,
                    'unit_price'   => rand(150, 25000),
                    'supplier'     => 'Default Hardware Supplier',
                    'manufacturer' => 'Generic Hardware Co.',
                    'description'  => 'High quality hardware item under ' . $typeName,
                    'image'        => null,
                ]);

                Stockins::create([
                    'item_id'        => $item->id,
                    'batch_id'       => strtoupper(Str::random(6)),
                    'branch_id'      => 1,          // adjust if needed
                    'received_by'    => 1,          // adjust if needed
                    'quantity'       => rand(20, 300),
                    'expiry_date'    => Carbon::now()->addYears(5),
                    'supplier'       => 'Default Hardware Supplier',
                    'additional_info'=> 'Initial opening stock',
                ]);
            }
        }
    }
}
