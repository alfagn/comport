<?php

use App\Models\PageSection;
use App\Models\Setting;
use App\Support\EditorialDefaults;
use Database\Seeders\PageSectionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Preserve any custom edits from consolidated legacy groups (mega / global -> header / footer)
        $carryOver = [
            ['from' => ['mega', 'capabilities_title'], 'to' => ['header', 'capabilities_title', 'Capabilities Mega-Menu Heading', 'text']],
            ['from' => ['mega', 'capabilities_desc'], 'to' => ['header', 'capabilities_desc', 'Capabilities Mega-Menu Description', 'textarea']],
            ['from' => ['mega', 'journal_title'], 'to' => ['header', 'journal_title', 'Journal Mega-Menu Heading', 'text']],
            ['from' => ['mega', 'journal_desc'], 'to' => ['header', 'journal_desc', 'Journal Mega-Menu Description', 'textarea']],
            ['from' => ['footer', 'search_placeholder'], 'to' => ['header', 'search_placeholder', 'Site Search Input Placeholder', 'text']],
            ['from' => ['global', 'copy_0a2e9effe060'], 'to' => ['footer', 'banner_cta_primary', 'CTA Banner Primary Button', 'text']],
            ['from' => ['global', 'copy_af45d288e6ae'], 'to' => ['footer', 'banner_cta_secondary', 'CTA Banner Secondary Button', 'text']],
            ['from' => ['global', 'copy_ce0795d9d101'], 'to' => ['footer', 'copyright', 'Legal Copyright Notice', 'text']],
        ];

        foreach ($carryOver as $map) {
            [$fromPage, $fromKey] = $map['from'];
            [$toPage, $toKey, $label, $type] = $map['to'];
            $existingVal = PageSection::query()
                ->where('page', $fromPage)
                ->where('key', $fromKey)
                ->value('value');

            if ($existingVal !== null) {
                PageSection::query()->firstOrCreate(
                    ['page' => $toPage, 'key' => $toKey],
                    ['label' => $label, 'type' => $type, 'value' => $existingVal, 'sort_order' => 50]
                );
            }
        }

        // 2. Build authoritative map of curated editorial fields + friendly labels
        $curated = EditorialDefaults::rows();
        foreach (PageSectionSeeder::definitions() as $page => $items) {
            foreach ($items as $idx => [$key, $label, $type]) {
                $id = "{$page}.{$key}";
                $curated[$id] = [
                    'page' => $page,
                    'key' => $key,
                    'label' => $label,
                    'type' => $type,
                    'value' => $curated[$id]['value'] ?? null,
                    'sort_order' => $idx,
                ];
            }
        }

        // 3. Delete all non-editable UI-chrome / noisy template rows from page_sections
        $allowedIds = array_fill_keys(array_keys($curated), true);
        $allowedIds['about.directory_registered'] = true;

        PageSection::query()->get(['id', 'page', 'key'])->each(function (PageSection $row) use ($allowedIds) {
            if (! isset($allowedIds["{$row->page}.{$row->key}"])) {
                $row->delete();
            }
        });

        // 4. Ensure every curated row exists and upgrade its label & type (preserving saved values)
        $orderCounter = 0;
        foreach ($curated as $row) {
            $orderCounter++;
            $section = PageSection::query()->firstOrCreate(
                ['page' => $row['page'], 'key' => $row['key']],
                [
                    'label' => $row['label'],
                    'type' => $row['type'],
                    'value' => $row['value'] ?? null,
                    'sort_order' => $row['sort_order'] ?? $orderCounter,
                ]
            );

            $updates = [
                'label' => $row['label'],
                'type' => $row['type'],
                'sort_order' => $row['sort_order'] ?? $orderCounter,
            ];

            if ($section->value === null && isset($row['value'])) {
                $updates['value'] = $row['value'];
            }

            $section->update($updates);
        }

        // 5. Normalize settings groups so seo_*, brand_*, sitemap_*, and cookie_* never leak into General
        Setting::query()->get()->each(function (Setting $setting) {
            $expectedGroup = Setting::inferGroup($setting->key);
            if (in_array($expectedGroup, ['seo', 'brand', 'cookies', 'sitemap'], true) && $setting->group !== $expectedGroup) {
                $setting->update(['group' => $expectedGroup]);
            }
        });

        // 6. Seed default Cookie Consent settings without overwriting existing admin choices
        $cookieDefaults = [
            'cookie_consent_enabled' => '1',
            'cookie_banner_title' => 'Cookie & Privacy Preferences',
            'cookie_banner_message' => 'We use strictly necessary cookies to keep Empetix secure and reliable, and optional analytics and marketing cookies to understand how visitors use our site. Do you allow us to store optional cookies on your device?',
            'cookie_accept_label' => 'Accept All Cookies',
            'cookie_reject_label' => 'Essential Only',
            'cookie_settings_label' => 'Cookie Settings',
        ];

        foreach ($cookieDefaults as $key => $value) {
            Setting::query()->firstOrCreate(
                ['key' => $key],
                ['group' => 'cookies', 'value' => $value]
            );
        }

        Cache::forget('page_sections.map');
    }

    public function down(): void
    {
        // Curated CMS cleanup migration.
    }
};
