<?php

namespace Database\Seeders;

use App\Models\ContentListItem;
use App\Models\PageSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

/** Add missing About controls without replacing any existing edited values. */
class AboutContentSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(__DIR__.'/data/about-content.json'), true, flags: JSON_THROW_ON_ERROR);
        foreach (['about', 'founders'] as $page) {
            foreach ($data[$page] as $i => $row) {
                PageSection::firstOrCreate(['page' => $page, 'key' => $row['key']], [
                    'label' => $row['label'], 'type' => $row['type'], 'value' => $row['value'], 'sort_order' => 100 + $i,
                ]);
            }
        }
        // Seed this newly introduced list only when it has never been registered.
        // A section marker prevents a later upgrade resurrecting deleted people.
        $marker = PageSection::firstOrCreate(['page' => 'about', 'key' => 'directory_registered'], [
            'label' => 'Directory initialized (internal)', 'type' => 'internal', 'value' => '1', 'sort_order' => 999,
        ]);
        if ($marker->wasRecentlyCreated && ! ContentListItem::where('group', 'about.directory')->exists()) {
            foreach ($data['directory'] as $i => $person) {
                ContentListItem::create($person + ['group' => 'about.directory', 'sort_order' => $i, 'is_active' => true]);
            }
        }
        Cache::forget('page_sections.map');
        Cache::forget('content-list.about.directory');
    }
}
