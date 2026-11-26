<?php

use App\Support\Permissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Registers the AI assistant permissions on existing installs (fresh
 * installs also get them from RolesAndPermissionsSeeder) and seeds the
 * dashboard-editable support chatbot settings.
 */
return new class extends Migration
{
    private const PERMISSIONS = ['use ai assistant', 'manage support chats'];

    private const SETTINGS = [
        'ai_support_enabled' => '1',
        'ai_support_greeting' => "Hi there! I'm the Empetix assistant. Ask me about our services, case studies or open roles — or tell me about your project and I'll pass it to the team.",
        'ai_support_knowledge' => '',
    ];

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $name) {
            Permission::findOrCreate($name, 'web');
        }

        Role::query()
            ->where('name', Permissions::OWNER_ROLE)
            ->first()
            ?->givePermissionTo(self::PERMISSIONS);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::SETTINGS as $key => $value) {
            if (! DB::table('settings')->where('key', $key)->exists()) {
                DB::table('settings')->insert([
                    'group' => 'ai',
                    'key' => $key,
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', array_keys(self::SETTINGS))->delete();

        Permission::query()->whereIn('name', self::PERMISSIONS)->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
