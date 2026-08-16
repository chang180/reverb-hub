<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => (string) config('reverb-hub.admin.email')],
            [
                'name' => (string) config('reverb-hub.admin.name'),
                'password' => (string) config('reverb-hub.admin.password'),
                'email_verified_at' => now(),
            ],
        );
    }
}
