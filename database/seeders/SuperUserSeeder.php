<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'seadztech@gmail.com';
        $user = User::where('email', $email)->first();
        
        if ($user === null) {
            $user = User::create([
                'name' => 'Super Admin',
                'email' => $email,
                'email_verified_at' => now(),
                'password' => Hash::make('Kenya2024?Kenya2024?'), 
                'remember_token' => Str::random(10),
            ]);
        }
        
        // Assign admin role
        $user->assignRole('admin'); // If using Spatie Laravel Permission
     
        $user->save();
    }
}