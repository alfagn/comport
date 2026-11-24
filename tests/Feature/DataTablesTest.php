<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Server-side Yajra DataTables: the dashboard listings answer ajax
 * requests with Yajra's JSON payload, search and sort run in SQL,
 * escaping survives into the rendered cells, and guests are walled off.
 */
class DataTablesTest extends TestCase
{
    use RefreshDatabase;

    /** Headers DataTables.net sends on its ajax request. */
    private function ajaxHeaders(): array
    {
        return ['X-Requested-With' => 'XMLHttpRequest'];
    }

    private function admin(): User
    {
        return $this->owner();
    }

    public function test_guests_cannot_read_datatable_json(): void
    {
        $this->getJson(route('admin.projects.index'))->assertUnauthorized();
    }

    public function test_index_ajax_returns_yajra_payload_with_real_rows(): void
    {
        $this->seed(DatabaseSeeder::class);
        $project = Project::firstOrFail();

        $response = $this->actingAs($this->admin())
            ->get(route('admin.projects.index', ['draw' => 1, 'start' => 0, 'length' => 15]), $this->ajaxHeaders());

        $response->assertOk()
            ->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data']);

        $payload = $response->json();
        $this->assertSame(Project::count(), $payload['recordsTotal']);
        // Title cells are intentionally HTML-escaped in the payload (see the
        // escaping regression test), so compare against the escaped title.
        $this->assertStringContainsString(e($project->title), json_encode($payload['data']));
    }

    public function test_global_search_filters_in_sql(): void
    {
        $this->seed(DatabaseSeeder::class);
        $project = Project::firstOrFail();
        $project->update(['title' => 'Zebra Platform']);
        Project::where('id', '!=', $project->id)->update(['title' => 'Alpha Site']);

        $payload = $this->actingAs($this->admin())
            ->get(route('admin.projects.index', [
                'draw' => 1, 'start' => 0, 'length' => 15,
                'columns' => [
                    ['data' => 'title', 'searchable' => 'true', 'orderable' => 'true'],
                    ['data' => 'tags', 'searchable' => 'false', 'orderable' => 'false'],
                    ['data' => 'is_featured', 'searchable' => 'false', 'orderable' => 'true'],
                    ['data' => 'is_active', 'searchable' => 'false', 'orderable' => 'true'],
                    ['data' => 'actions', 'searchable' => 'false', 'orderable' => 'false'],
                ],
                'search' => ['value' => 'Zebra'],
            ]), $this->ajaxHeaders())
            ->json();

        $this->assertSame(1, $payload['recordsFiltered']);
        $this->assertStringContainsString('Zebra Platform', $payload['data'][0]['title']);
    }

    public function test_column_sorting_runs_in_sql(): void
    {
        $this->seed(DatabaseSeeder::class);
        Project::query()->delete();
        Project::create(['title' => 'Bravo', 'slug' => 'bravo', 'kicker' => 'k', 'description' => 'd']);
        Project::create(['title' => 'Alpha', 'slug' => 'alpha', 'kicker' => 'k', 'description' => 'd']);

        $payload = $this->actingAs($this->admin())
            ->get(route('admin.projects.index', [
                'draw' => 1, 'start' => 0, 'length' => 15,
                'columns' => [['data' => 'title', 'searchable' => 'true', 'orderable' => 'true']],
                'order' => [['column' => 0, 'dir' => 'asc']],
            ]), $this->ajaxHeaders())
            ->json();

        $this->assertStringContainsString('Alpha', $payload['data'][0]['title']);
        $this->assertStringContainsString('Bravo', $payload['data'][1]['title']);
    }

    public function test_row_content_is_escaped_in_rendered_cells(): void
    {
        $this->seed(DatabaseSeeder::class);
        $project = Project::firstOrFail();
        $project->update(['title' => '<script>alert(1)</script>']);

        $payload = $this->actingAs($this->admin())
            ->get(route('admin.projects.index', ['draw' => 1, 'start' => 0, 'length' => 15]), $this->ajaxHeaders())
            ->json();

        $row = collect($payload['data'])->firstWhere('id', $project->id);
        $this->assertStringNotContainsString('<script>', $row['title']);
        $this->assertStringContainsString('&lt;script&gt;', $row['title']);
    }

    public function test_server_side_pagination_beyond_the_first_page(): void
    {
        $this->seed(DatabaseSeeder::class);
        // Grow past one page (15 rows) so a second page must exist.
        for ($i = 1; $i <= 20; $i++) {
            Project::create(['title' => "Bulk Project $i", 'slug' => "bulk-project-$i", 'kicker' => 'k', 'description' => 'd']);
        }
        $total = Project::count();
        $this->assertGreaterThan(15, $total);

        $cols = [
            'draw' => 1, 'length' => 15,
            'columns' => [
                ['data' => 'title', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => 'tags', 'searchable' => 'false', 'orderable' => 'false'],
                ['data' => 'is_featured', 'searchable' => 'false', 'orderable' => 'true'],
                ['data' => 'is_active', 'searchable' => 'false', 'orderable' => 'true'],
                ['data' => 'actions', 'searchable' => 'false', 'orderable' => 'false'],
            ],
            'order' => [['column' => 0, 'dir' => 'asc']],
        ];

        $page1 = $this->actingAs($this->admin())
            ->get(route('admin.projects.index', $cols + ['start' => 0]), $this->ajaxHeaders())->json();
        $page2 = $this->actingAs($this->admin())
            ->get(route('admin.projects.index', $cols + ['start' => 15]), $this->ajaxHeaders())->json();

        $this->assertSame($total, $page1['recordsTotal']);
        $this->assertCount(15, $page1['data']);
        $this->assertCount($total - 15, $page2['data']);
        // Pages must not overlap: second page rows differ from the first.
        $page1Titles = array_column($page1['data'], 'title');
        foreach ($page2['data'] as $row) {
            $this->assertNotContains($row['title'], $page1Titles);
        }
    }

    public function test_ampersand_displays_once_and_markup_stays_escaped_in_inbox(): void
    {
        $this->seed(DatabaseSeeder::class);
        ContactMessage::create([
            'name' => '<b>Ampersand</b> Co',
            'email' => 'amp@example.com',
            'services' => ['SaaS & Cloud'],
            'brief' => 'Checks the escaping boundary.',
        ]);

        $payload = $this->actingAs($this->admin())
            ->get(route('admin.inbox.index', ['draw' => 1, 'start' => 0, 'length' => 15]), $this->ajaxHeaders())
            ->json();

        $row = collect($payload['data'])->firstWhere('email', 'amp@example.com');
        // Ordinary ampersand: encoded exactly once, so the browser shows "&".
        $this->assertSame('SaaS &amp; Cloud', $row['services']);
        $this->assertStringNotContainsString('&amp;amp;', $row['services']);
        // Malicious markup in a raw-rendered cell stays entity-escaped.
        $this->assertStringContainsString('&lt;b&gt;Ampersand&lt;/b&gt;', $row['name']);
        $this->assertStringNotContainsString('<b>', $row['name']);
    }

    public function test_subscribers_listing_is_a_datatable_too(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->actingAs($this->admin())
            ->get(route('admin.inbox.subscribers', ['draw' => 1, 'start' => 0, 'length' => 15]), $this->ajaxHeaders());

        $response->assertOk()->assertJsonStructure(['draw', 'recordsTotal', 'data']);
    }

    public function test_subscribers_page_renders_its_table(): void
    {
        $this->seed(DatabaseSeeder::class);

        $html = $this->actingAs($this->admin())
            ->get(route('admin.inbox.subscribers'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('dt-table', $html);
    }

    public function test_index_pages_embed_the_datatable_markup(): void
    {
        $this->seed(DatabaseSeeder::class);

        $html = $this->actingAs($this->admin())
            ->get(route('admin.projects.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('dt-table', $html);
        $this->assertStringContainsString('jquery.dataTables.min.js', $html);
    }

    public function test_inline_status_toggle_switches_records_and_enforces_permissions(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = $this->admin();

        // 1. DataTable AJAX payload renders interactive data-status-toggle button
        $industry = \App\Models\Industry::firstOrFail();
        $dtPayload = $this->actingAs($admin)
            ->get(route('admin.industries.index', ['draw' => 1, 'start' => 0, 'length' => 15]), $this->ajaxHeaders())
            ->assertOk()
            ->json();

        $firstRow = collect($dtPayload['data'])->firstWhere('id', $industry->id);
        $this->assertStringContainsString('data-status-toggle', $firstRow['is_active']);

        // 2. One-click PATCH flips Industry status Live -> Hidden -> Live
        $initial = (bool) $industry->is_active;
        $this->actingAs($admin)
            ->patchJson(route('admin.status.toggle', ['entity' => 'industries', 'id' => $industry->id]))
            ->assertOk()
            ->assertJson(['ok' => true, 'active' => ! $initial, 'label' => ! $initial ? 'Live' : 'Hidden']);
        $this->assertSame(! $initial, (bool) $industry->fresh()->is_active);

        // 3. Post toggle sets published_at when publishing a draft
        $post = \App\Models\Post::firstOrFail();
        $post->update(['is_published' => false, 'published_at' => null]);
        $this->actingAs($admin)
            ->patchJson(route('admin.status.toggle', ['entity' => 'posts', 'id' => $post->id]))
            ->assertOk()
            ->assertJson(['ok' => true, 'active' => true, 'label' => 'Published']);
        $this->assertTrue((bool) $post->fresh()->is_published);
        $this->assertNotNull($post->fresh()->published_at);

        // 4. Service Page toggle updates publication & blocks archived Arabic rows
        $enServicePage = \App\Models\ServicePage::where('locale', 'en')->firstOrFail();
        $this->actingAs($admin)
            ->patchJson(route('admin.status.toggle', ['entity' => 'service-pages', 'id' => $enServicePage->id]))
            ->assertOk()
            ->assertJson(['ok' => true, 'active' => true, 'label' => 'Published']);
        $arServicePage = \App\Models\ServicePage::where('locale', 'ar')->firstOrFail();
        $this->actingAs($admin)
            ->patchJson(route('admin.status.toggle', ['entity' => 'service-pages', 'id' => $arServicePage->id]))
            ->assertNotFound();

        // 5. Unprivileged user cannot toggle status
        $unprivileged = User::factory()->create();
        $this->actingAs($unprivileged)
            ->patchJson(route('admin.status.toggle', ['entity' => 'industries', 'id' => $industry->id]))
            ->assertForbidden();
    }
}
