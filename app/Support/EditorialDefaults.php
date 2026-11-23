<?php

namespace App\Support;

/** The original shipped copy, shared by rendering and safe DB registration. */
class EditorialDefaults
{
    private static ?array $rows = null;

    public static function rows(): array
    {
        if (self::$rows !== null) return self::$rows;
        $read = fn (string $name) => json_decode(file_get_contents(database_path('seeders/data/'.$name.'.json')), true, flags: JSON_THROW_ON_ERROR);
        $rows = [];
        foreach ($read('template-defaults') as $row) {
            $row += ['label' => ucwords(str_replace('_', ' ', $row['key'])), 'type' => strlen($row['value']) > 130 ? 'textarea' : 'text'];
            $rows[$row['page'].'.'.$row['key']] = $row;
        }
        $about = $read('about-content');
        foreach (['about', 'founders'] as $page) {
            foreach ($about[$page] as $row) {
                $row['page'] = $page;
                $rows[$page.'.'.$row['key']] = $row;
            }
        }
        foreach ($read('editorial-content') as $row) {
            $rows[$row['page'].'.'.$row['key']] = $row;
        }
        return self::$rows = $rows;
    }
}
