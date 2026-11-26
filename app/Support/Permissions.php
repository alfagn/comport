<?php

namespace App\Support;

/**
 * Single source of truth for the dashboard permission catalog.
 *
 * The seeder creates exactly these keys, the Roles UI lists them, and route
 * middleware enforces them server-side. A user with no roles sees the
 * dashboard only; the seeded "owner" role bypasses every gate (see
 * AppServiceProvider) and is meant for the site owners.
 */
final class Permissions
{
    /** Role that implicitly holds every permission. */
    public const OWNER_ROLE = 'owner';

    /**
     * Permission key => human label shown in the Roles screen.
     *
     * @return array<string, string>
     */
    public static function all(): array
    {
        return [
            'manage inbox' => 'Inbox & briefs',
            'manage pages' => 'Pages & content',
            'manage hero slides' => 'Hero slides',
            'manage services' => 'Services',
            'manage industries' => 'Industries',
            'manage projects' => 'Projects',
            'manage members' => 'Team & founders',
            'manage jobs' => 'Careers',
            'manage applications' => 'Job applications',
            'manage posts' => 'Blog & media',
            'manage partners' => 'Partners',
            'manage navigation' => 'Navigation',
            'manage content lists' => 'Content lists',
            'manage settings' => 'Site settings',
            'manage admins' => 'Admins & roles',
            'use ai assistant' => 'AI dashboard assistant',
            'manage support chats' => 'AI support chats',
        ];
    }
}
