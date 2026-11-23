<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * End-to-end image upload: the file is stored, its path persisted, and the
 * public page renders the uploaded asset — not just a 200 on the form.
 */
class UploadTest extends TestCase
{
    use RefreshDatabase;

    /** Uploading a partner logo stores the file and renders it publicly. */
    public function test_partner_logo_upload_persists_and_renders(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.partners.store'), [
            'name' => 'Acme Cloud',
            'logo' => UploadedFile::fake()->image('acme.png', 400, 200),
            'tier' => 'Technology Alliance',
            'sort_order' => 1,
            'is_active' => '1',
        ])->assertRedirect(route('admin.partners.index'));

        $partner = \App\Models\Partner::where('name', 'Acme Cloud')->firstOrFail();
        $this->assertNotNull($partner->logo, 'logo path was not persisted');
        Storage::disk('public')->assertExists($partner->logo);

        // The public partners page must reference the uploaded file.
        $this->get('/partners')->assertOk()->assertSee($partner->logo, false);
    }

    /** Non-image uploads are rejected and nothing is persisted. */
    public function test_non_image_upload_is_rejected(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.partners.store'), [
            'name' => 'Bad Upload',
            'logo' => UploadedFile::fake()->create('evil.exe', 100, 'application/octet-stream'),
            'sort_order' => 1,
        ])->assertSessionHasErrors('logo');

        $this->assertDatabaseMissing('partners', ['name' => 'Bad Upload']);
    }

    /** Replacing a logo on update keeps the record and swaps the file. */
    public function test_logo_replacement_on_update(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();
        $partner = \App\Models\Partner::create(['name' => 'Old', 'logo' => null, 'sort_order' => 0, 'is_active' => true]);

        $this->actingAs($admin)->put(route('admin.partners.update', $partner->id), [
            'name' => 'Old',
            'logo' => UploadedFile::fake()->image('new.png'),
            'sort_order' => 0,
            'is_active' => '1',
        ])->assertRedirect(route('admin.partners.index'));

        $partner->refresh();
        $this->assertNotNull($partner->logo);
        Storage::disk('public')->assertExists($partner->logo);
    }
}
