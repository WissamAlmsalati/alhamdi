<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // In production set ADMIN_EMAIL and ADMIN_PASSWORD (Render generates the password).
        // The fallbacks are for local development only.
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@wahatalteeb.com')],
            [
                'name' => env('ADMIN_NAME', 'مدير النظام'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'is_admin' => true,
            ]
        );
    }
}
