<?php

namespace Database\Seeders;

use App\Models\NavItem;
use Illuminate\Database\Seeder;

/**
 * Seeds the navigation links that match the current public header so the
 * dashboard starts from the same menus the static site shipped with.
 */
class NavItemSeeder extends Seeder
{
    public function run(): void
    {
        $utility = [
            ['label' => 'Careers', 'url' => '/careers'],
            ['label' => 'Projects', 'url' => '/work'],
            ['label' => 'Partners', 'url' => '/partners'],
            ['label' => 'Team', 'url' => '/team'],
            ['label' => 'Blog', 'url' => '/blog'],
            ['label' => 'Contact Us', 'url' => '/contact'],
        ];

        foreach ($utility as $i => $link) {
            NavItem::updateOrCreate(
                ['menu' => 'utility', 'url' => $link['url']],
                $link + ['sort_order' => $i, 'is_active' => true]
            );
        }

        $drawer = [
            ['label' => 'Careers', 'url' => '/careers'],
            ['label' => 'Contact Us', 'url' => '/contact'],
        ];

        foreach ($drawer as $i => $link) {
            NavItem::updateOrCreate(
                ['menu' => 'drawer', 'url' => $link['url']],
                $link + ['sort_order' => $i, 'is_active' => true]
            );
        }
    }
}
