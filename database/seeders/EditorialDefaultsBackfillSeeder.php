<?php

namespace Database\Seeders;

use App\Models\PageSection;
use App\Support\EditorialDefaults;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

/** Materialize missing shipped defaults, never replace a saved value. */
class EditorialDefaultsBackfillSeeder extends Seeder
{
    public function run(): void
    {
        $legacy = collect(json_decode(file_get_contents(__DIR__.'/data/template-defaults.json'), true))
            ->mapWithKeys(fn ($row) => [$row['page'].'.'.$row['key'] => true])->all();
        foreach (EditorialDefaults::rows() as $id => $row) {
            $section = PageSection::firstOrCreate(['page' => $row['page'], 'key' => $row['key']], [
                'label' => $row['label'], 'type' => $row['type'], 'value' => $row['value'], 'sort_order' => 500,
            ]);
            // Legacy NULL means "use the template default", not an empty string.
            // Persist that same effective value. Explicit blanks remain blank.
            if ($section->value === null && isset($legacy[$id])) {
                $section->update(['value' => $row['value']]);
            }
        }
        Cache::forget('page_sections.map');
    }
}
