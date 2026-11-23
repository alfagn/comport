<?php

namespace Database\Seeders;

use App\Models\PageSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

/** Register remaining editorial copy/media while preserving edited rows. */
class EditorialContentSeeder extends Seeder
{
    public function run(): void
    {
        $rows = json_decode(file_get_contents(__DIR__.'/data/editorial-content.json'), true, flags: JSON_THROW_ON_ERROR);
        foreach ($rows as $i => $row) {
            PageSection::firstOrCreate(['page' => $row['page'], 'key' => $row['key']], [
                'label' => $row['label'], 'type' => $row['type'], 'value' => $row['value'], 'sort_order' => 200 + $i,
            ]);
        }
        Cache::forget('page_sections.map');
    }
}
