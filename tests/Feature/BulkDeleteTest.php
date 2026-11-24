<?php

namespace Tests\Feature;

use App\Models\Industry;
use App\Models\Partner;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkDeleteTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return $this->owner();
    }

    public function test_guests_cannot_bulk_delete(): void
    {
        $this->postJson(route('admin.bulk.delete', ['entity' => 'partners']), ['ids' => [1, 2]])
            ->assertUnauthorized();
    }

    public function test_admin_can_bulk_delete_multiple_partners(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = $this->admin();

        $p1 = Partner::create(['name' => 'Bulk Partner 1', 'is_active' => true, 'sort_order' => 1]);
        $p2 = Partner::create(['name' => 'Bulk Partner 2', 'is_active' => true, 'sort_order' => 2]);
        $p3 = Partner::create(['name' => 'Keep Partner 3', 'is_active' => true, 'sort_order' => 3]);

        $this->actingAs($admin)
            ->postJson(route('admin.bulk.delete', ['entity' => 'partners']), [
                'ids' => [$p1->id, $p2->id],
            ])
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'count' => 2,
                'message' => 'Successfully deleted 2 records.',
            ]);

        $this->assertDatabaseMissing('partners', ['id' => $p1->id]);
        $this->assertDatabaseMissing('partners', ['id' => $p2->id]);
        $this->assertDatabaseHas('partners', ['id' => $p3->id]);
    }

    public function test_admin_cannot_bulk_delete_their_own_account(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = $this->admin();

        $other = User::factory()->create();

        $this->actingAs($admin)
            ->postJson(route('admin.bulk.delete', ['entity' => 'admins']), [
                'ids' => [$admin->id],
            ])
            ->assertStatus(422);

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_unprivileged_user_cannot_bulk_delete(): void
    {
        $this->seed(DatabaseSeeder::class);
        $unprivileged = User::factory()->create();
        $industry = Industry::firstOrFail();

        $this->actingAs($unprivileged)
            ->postJson(route('admin.bulk.delete', ['entity' => 'industries']), [
                'ids' => [$industry->id],
            ])
            ->assertForbidden();
    }

    public function test_datatable_renders_select_row_checkboxes(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = $this->admin();
        $partner = Partner::firstOrFail();

        $response = $this->actingAs($admin)
            ->get(route('admin.partners.index', ['draw' => 1, 'start' => 0, 'length' => 15]), [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertOk();
        $payload = $response->json();

        $firstRow = collect($payload['data'])->firstWhere('id', $partner->id);
        $this->assertNotNull($firstRow);
        $this->assertArrayHasKey('select_row', $firstRow);
        $this->assertStringContainsString('dt-row-select', $firstRow['select_row']);
        $this->assertStringContainsString((string) $partner->id, $firstRow['select_row']);
    }
}
