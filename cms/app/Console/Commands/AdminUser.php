<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AdminUser extends Command
{
    protected $signature = 'admin:user {email} {--name=Administrator} {--password= : Leave empty to generate one}';

    protected $description = 'Create a dashboard user, or reset the password of an existing one';

    public function handle(): int
    {
        $email = strtolower(trim($this->argument('email')));
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('That is not a valid email address.');

            return self::FAILURE;
        }
        $password = $this->option('password') ?: Str::password(16, symbols: false);
        if (strlen($password) < 10) {
            $this->error('Use a password of at least 10 characters.');

            return self::FAILURE;
        }
        $user = User::query()->updateOrCreate(['email' => $email], ['name' => $this->option('name'), 'password' => $password]);
        $this->info(($user->wasRecentlyCreated ? 'Created' : 'Updated')." dashboard user {$email}");
        if (! $this->option('password')) {
            $this->line("Password: {$password}");
        }

        return self::SUCCESS;
    }
}
