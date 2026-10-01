<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\Roles;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AdminUser extends Command
{
    protected $signature = 'admin:user {email} {--name=Administrator} {--password= : Leave empty to generate one} {--role=admin : viewer, sales, editor, manager or admin}';

    protected $description = 'Create a user (default role: admin), or reset the password and role of an existing one';

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
        $role = $this->option('role');
        if (! in_array($role, Roles::ORDER, true)) {
            $this->error('Role must be one of: '.implode(', ', Roles::ORDER));

            return self::FAILURE;
        }
        $user = User::query()->updateOrCreate(['email' => $email], ['name' => $this->option('name'), 'password' => $password, 'role' => $role, 'active' => true]);
        $this->info(($user->wasRecentlyCreated ? 'Created' : 'Updated')." {$user->roleLabel()} {$email}");
        if (! $this->option('password')) {
            $this->line("Password: {$password}");
        }

        return self::SUCCESS;
    }
}
