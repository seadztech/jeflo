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
        // Admin User 1 - Primary Admin
        $email1 = 'seadztech@gmail.com';
        $user1 = User::where('email', $email1)->first();
        
        if ($user1 === null) {
            $user1 = User::create([
                'name' => 'Super Admin',
                'email' => $email1,
                'email_verified_at' => now(),
                'password' => Hash::make('password'), 
                'remember_token' => Str::random(10),
            ]);
        }
        
        // Assign admin role to first user
        $user1->assignRole('Admin');
        $user1->save();

        // Admin User 2 - Additional Admin
        $email2 = 'admin@seadztech.co.ke'; // Change this to your desired email
        $user2 = User::where('email', $email2)->first();
        
        if ($user2 === null) {
            $user2 = User::create([
                'name' => 'Admin User',
                'email' => $email2,
                'email_verified_at' => now(),
                'password' => Hash::make('password'), // Change this to a secure password
                'remember_token' => Str::random(10),
            ]);
        }
        
        // Assign admin role to second user
        $user2->assignRole('Admin');
        $user2->save();

        
    }
}