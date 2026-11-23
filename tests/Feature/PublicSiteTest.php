<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Public site smoke tests: every route renders from the seeded database,
 * dynamic content actually appears, and the contact/newsletter forms
 * persist their submissions.
 */
class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    /** Seed the real content + admin user for every test. */
    protected $seed = true;

    /** The main listing pages render. */
    public function test_listing_pages_render(): void
    {
        foreach (['/', '/about', '/work', '/team', '/careers', '/partners', '/blog', '/contact'] as $route) {
            $this->get($route)->assertOk();
        }
    }

    /** Every seeded detail page renders by slug. */
    public function test_detail_pages_render(): void
    {
        $this->get('/work/nilepay')->assertOk()->assertSee('NilePay');
        $this->get('/work/qahwa-roasters')->assertOk();
        $this->get('/founders/mohanad')->assertOk()->assertSee('Mohanad');
        $this->get('/founders/ayman')->assertOk();
        $this->get('/founders/bishoy')->assertOk();
        $this->get('/careers/laravel')->assertOk()->assertSee('Laravel');
        $this->get('/careers/producer')->assertOk();
        $this->get('/blog/arabic-first-design-systems')->assertOk();
        $this->get('/blog/five-day-product-growth-sprint')->assertOk();
        $this->get('/blog/behind-the-lens-qahwa-launch')->assertOk();
    }

    /** Unknown slugs 404 instead of erroring. */
    public function test_unknown_slugs_404(): void
    {
        $this->get('/work/does-not-exist')->assertNotFound();
        $this->get('/founders/nobody')->assertNotFound();
        $this->get('/careers/nothing')->assertNotFound();
        $this->get('/blog/missing')->assertNotFound();
    }

    /** The contact form stores a message and redirects with a flash. */
    public function test_contact_form_stores_message(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Test Lead',
            'email' => 'lead@example.com',
            'company' => 'Acme',
            'budget_tier' => '10k-25k',
            'timeline' => 'Q1',
            'description' => 'We need a platform.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('contact_messages', ['email' => 'lead@example.com']);
    }

    /** Contact validation rejects a bad payload. */
    public function test_contact_form_validates(): void
    {
        $this->post('/contact', [])->assertSessionHasErrors(['name', 'email']);
    }

    /** The footer newsletter form stores a subscriber. */
    public function test_newsletter_subscribe(): void
    {
        $this->post('/subscribe', ['email' => 'fan@example.com'])->assertRedirect();
        $this->assertDatabaseHas('newsletter_subscribers', ['email' => 'fan@example.com']);
    }

    /** Settings and page sections flow into the rendered layout. */
    public function test_settings_and_sections_render(): void
    {
        \App\Models\Setting::setValue('email', 'changed@empetix.com');
        app(\App\Services\SectionService::class)->savePage('footer', ['banner_title' => 'An edited banner title.']);

        $this->get('/')->assertOk()
            ->assertSee('changed@empetix.com')
            ->assertSee('An edited banner title.');
    }
}
