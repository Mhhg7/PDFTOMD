<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([SiteContentSeeder::class, SalesSeeder::class]);

        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');
        if ($email && $password) {
            User::query()->updateOrCreate(['email' => $email], ['name' => env('ADMIN_NAME', 'Administrator'), 'password' => $password, 'role' => 'admin', 'active' => true]);
            $this->command?->info("Dashboard user ready: {$email}");
        } else {
            $this->command?->warn('No dashboard user created. Run: php artisan admin:user you@example.com');
        }
    }
}
