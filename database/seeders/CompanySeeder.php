<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::updateOrCreate(
            ['name' => 'Jeflo auto spares'],
            [
                'logo_path'   => 'logo.png', 
                'address'     => 'Nkubu',
                'phone'       => '+254740538622',
                'email'       => 'info@jefloenterprise.com',
                'website'     => 'www.jefloenterprise.com',
                'location'    => 'Nkubu',
                'description' => 'Jeflo auto spares - Retail & Distribution',
            ]
        );
    }
}
