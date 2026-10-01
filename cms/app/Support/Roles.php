<?php

namespace App\Support;

use App\Models\User;

/**
 * Fixed roles. Each role includes everything the roles before it can do.
 *
 *  viewer   sees the sales catalog and prints order sheets, never prices
 *  sales    also sees prices and can print sheets with prices
 *  editor   also adds, edits and deletes products and companies
 *  manager  also manages sales users and the sheet settings
 *  admin    also edits the public website, and manages every user
 */
class Roles
{
    public const ORDER = ['viewer', 'sales', 'editor', 'manager', 'admin'];

    public const LABELS = [
        'viewer' => 'Viewer',
        'sales' => 'Sales',
        'editor' => 'Editor',
        'manager' => 'Manager',
        'admin' => 'Admin',
    ];

    public const HELP = [
        'viewer' => 'Sees products and prints order sheets. No prices.',
        'sales' => 'Like Viewer, plus sees prices and can print sheets with prices.',
        'editor' => 'Like Sales, plus adds, edits and deletes products and companies.',
        'manager' => 'Like Editor, plus manages sales users and sheet settings.',
        'admin' => 'Everything, including the public website dashboard and all users.',
    ];

    /** The lowest role each ability needs. */
    public const ABILITIES = [
        'sales.view' => 'viewer',
        'sales.prices' => 'sales',
        'sales.edit' => 'editor',
        'sales.users' => 'manager',
        'website' => 'admin',
    ];

    public static function rank(?string $role): int
    {
        $i = array_search($role, self::ORDER, true);

        return $i === false ? -1 : $i;
    }

    public static function allows(?User $user, string $ability): bool
    {
        if (! $user || ! $user->active || ! isset(self::ABILITIES[$ability])) {
            return false;
        }

        return self::rank($user->role) >= self::rank(self::ABILITIES[$ability]);
    }

    /** Roles a user may give to others: up to their own, and only admins create admins. */
    public static function assignableBy(User $user): array
    {
        return array_values(array_filter(self::ORDER, fn ($r) => self::rank($r) <= self::rank($user->role)));
    }
}
