<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
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
        return User::factory()->create();
    }

    public function test_guests_cannot_read_datatable_json(): void
    {
        $this->getJson(route('admin.projects.index'))->assertUnauthorized();
    }

    public function test_index_ajax_returns_yajra_payload_with_real_rows(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $project = Project::firstOrFail();

        $response = $this->actingAs($this->admin())
            ->get(route('admin.projects.index', ['draw' => 1, 'start' => 0, 'length' => 15]), $this->ajaxHeaders());

        $response->assertOk()
            ->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data']);

        $payload = $response->json();
        $this->assertSame(Project::count(), $payload['recordsTotal']);
        $this->assertStringContainsString($project->title, json_encode($payload['data']));
    }

    public function test_global_search_filters_in_sql(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
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
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
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
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $project = Project::firstOrFail();
        $project->update(['title' => '<script>alert(1)</script>']);

        $payload = $this->actingAs($this->admin())
            ->get(route('admin.projects.index', ['draw' => 1, 'start' => 0, 'length' => 15]), $this->ajaxHeaders())
            ->json();

        $row = collect($payload['data'])->firstWhere('id', $project->id);
        $this->assertStringNotContainsString('<script>', $row['title']);
        $this->assertStringContainsString('&lt;script&gt;', $row['title']);
    }

    public function test_subscribers_listing_is_a_datatable_too(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $response = $this->actingAs($this->admin())
            ->get(route('admin.inbox.subscribers', ['draw' => 1, 'start' => 0, 'length' => 15]), $this->ajaxHeaders());

        $response->assertOk()->assertJsonStructure(['draw', 'recordsTotal', 'data']);
    }

    public function test_index_pages_embed_the_datatable_markup(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $html = $this->actingAs($this->admin())
            ->get(route('admin.projects.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('dt-table', $html);
        $this->assertStringContainsString('jquery.dataTables.min.js', $html);
    }
}
