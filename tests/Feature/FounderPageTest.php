<?php

namespace Tests\Feature;

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FounderPageTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_founder_profile_uses_founder_page_scope_not_team_scope(): void
    {
        $founder = Member::query()->founders()->firstOrFail();

        // founder.css defines all profile design tokens under .founder-page;
        // the layout must apply that class or the page renders unstyled.
        $this->get('/founders/'.$founder->slug)
            ->assertOk()
            ->assertSee('class="founder-page"', false)
            ->assertDontSee('class="team-page"', false)
            ->assertSee($founder->name)
            ->assertSee($founder->role);
    }

    public function test_every_active_founder_profile_renders_with_founder_scope(): void
    {
        foreach (Member::query()->founders()->get() as $founder) {
            $this->get('/founders/'.$founder->slug)
                ->assertOk()
                ->assertSee('class="founder-page"', false)
                ->assertSee($founder->name);
        }
    }

    public function test_breadcrumb_and_portrait_alt_follow_the_viewed_founder(): void
    {
        $founders = Member::query()->founders()->orderBy('sort_order')->get();
        $first = $founders->firstOrFail();
        $second = $founders->skip(1)->firstOrFail();

        // The current-page crumb and hero alt must name the viewed founder,
        // not whichever person the original static HTML was converted from.
        $this->get('/founders/'.$second->slug)
            ->assertOk()
            ->assertSee('aria-current="page">'.$second->name.'</li>', false)
            ->assertSee('Portrait of '.$second->name, false)
            ->assertDontSee('aria-current="page">'.$first->name.'</li>', false);
    }

    public function test_non_founder_member_slug_and_unknown_slug_404(): void
    {
        $member = Member::query()->where('kind', 'member')->where('is_active', true)->first();
        if ($member) {
            $this->get('/founders/'.$member->slug)->assertNotFound();
        }

        $inactive = Member::query()->founders()->firstOrFail();
        $inactive->update(['is_active' => false]);
        $this->get('/founders/'.$inactive->slug)->assertNotFound();

        $this->get('/team')->assertOk()->assertSee('class="team-page"', false);
    }
}
