<?php

namespace Database\Seeders;

use App\Models\ContentListItem;
use Illuminate\Database\Seeder;

/**
 * Seeds the repeating card lists with the site's current content so the
 * dashboard starts from exactly what the static site shipped with.
 */
class ContentListSeeder extends Seeder
{
    /** group => rows of [title, body, meta, badge, cta, url]. */
    private const LISTS = [
        'about.pillars' => [
            ['Software engineering', 'Full-stack web and mobile platforms built for reliability, speed, and long-term scale.', null, null, null, null],
            ['Growth marketing', 'Engineers and marketers working as one unified team to turn clean data into customer acquisition.', null, null, null, null],
            ['Strategic investment', 'Backing bold founders with capital, senior technical leadership, and go-to-market execution.', null, null, null, null],
        ],
        'about.principles' => [
            ['Code and campaigns share one scoreboard.', "We don't measure engineering in story points and marketing in impressions. Both teams are accountable for activation, retention, and net revenue.", null, null, null, null],
            ['Senior principals on the tools.', "We don't bait-and-switch with senior pitch decks and junior execution. Our founders and staff engineers write production code and direct campaigns daily.", null, null, null, null],
            ['Arabic and English as equal first-class citizens.', 'We engineer bidirectional RTL/LTR interfaces and bilingual brand systems natively — never as a rushed translation layer at the end.', null, null, null, null],
            ['Full IP ownership and clean handovers.', 'Every line of Laravel/React code, Figma token, ad account, and raw camera file belongs to our client, documented so your internal team can run with it.', null, null, null, null],
        ],
        'about.timeline' => [
            ['Founded in Downtown Cairo', 'Omar, Lina, Karim, and Sara launch Empetix in a two-room studio at the Greek Campus, shipping three FinTech and e-commerce platforms in year one.', '2018', null, null, null],
            ['In-House Film & Performance Lab', 'Added a dedicated commercial film crew, photo studio, and server-side attribution practice to close the loop between software launches and customer acquisition.', '2021', null, null, null],
            ['Riyadh Studio & London Desk', 'Expanded on-the-ground engineering and growth squads to KAFD in Riyadh and Shoreditch in London to support GCC and European enterprise clients.', '2024', null, null, null],
            ['48 Specialists & 64+ Shipped Products', 'Operating across 14 countries with audited client platforms processing over USD 1.9B in annual gross transaction volume.', '2026', null, null, null],
        ],
        'team.culture' => [
            ['Small senior squads', 'Projects are staffed with experienced engineers, designers, and marketers who work directly in the codebase and campaigns every day.', null, null, null, null],
            ['Shared accountability', 'Product teams and growth specialists review the same activation, reliability, and retention metrics each week.', null, null, null, null],
            ['One studio in New Cairo', 'Working side by side in our New Cairo headquarters keeps architecture reviews, design critiques, and launch shoots fast and direct.', null, null, null, null],
        ],
        'careers.tracks' => [
            ['Software Engineering & Cloud', 'Backend (Laravel/Go), Frontend Architecture (Next.js/TS), Mobile (Flutter), and DevOps infrastructure.', 'TRACK CODE', '3 ROLES', 'Filter Engineering Jobs', 'engineering'],
            ['Performance Marketing & Film', 'Paid acquisition (Meta/Google/TikTok), server-side attribution, CRO analytics, and commercial video production.', 'TRACK GROWTH', '2 ROLES', 'Filter Marketing Jobs', 'marketing'],
            ['Product Design & Brand Systems', 'Bilingual RTL/LTR design systems, UX research, conversion prototyping, and brand identity architecture.', 'TRACK DESIGN', '1 ROLE', 'Filter Design Jobs', 'design'],
            ['View All Open Requisitions', 'Browse all active openings across Cairo, Riyadh, London, and remote EMEA engineering and creative squads.', 'TRACK ALL', '6 ROLES', 'Show All 6 Positions', 'all'],
        ],
        'careers.benefits' => [
            ['USD-indexed compensation & profit share', 'Transparent salary bands benchmarked in USD against international standards, plus bi-annual studio profit-sharing distributions across every full-time role.', null, null, null, null],
            ['Protected deep-work mornings', 'No internal standups or client calls before 1:00 PM Cairo time, giving engineers, designers, and writers four uninterrupted hours of flow every morning.', null, null, null, null],
            ['M3 Max workstation & USD 1,800 learning fund', 'Top-spec MacBook Pro, 4K display, ergonomic chair stipend, and an annual USD 1,800 budget for books, courses, conferences, or camera gear.', null, null, null, null],
            ['Tier-1 family medical & 28 days paid leave', 'Comprehensive private medical insurance covering you and your family, 28 days of annual paid vacation, and paid parental leave.', null, null, null, null],
        ],
        'careers.process' => [
            ['Intro Call with Practice Lead', 'A direct conversation with the hiring manager (not an external recruiter) covering your past work and salary expectations.', 'STEP 30 MIN', null, null, null],
            ['Paid 3-Hour Craft Sample', 'No trick algorithm riddles. You complete a focused, paid 3-hour real-world architecture, design, or campaign exercise at your own pace.', 'STEP PAID EXERCISE', null, null, null],
            ['Peer Walkthrough', 'Discuss your solution and engineering/creative trade-offs with two future teammates in a collaborative session.', 'STEP 60 MIN', null, null, null],
            ['Founder Chat & Written Offer', 'Meet one of our four co-founders and receive a clear written offer within 48 hours.', 'STEP 48 HOURS', null, null, null],
        ],
    ];

    public function run(): void
    {
        foreach (self::LISTS as $group => $rows) {
            foreach ($rows as $i => [$title, $body, $meta, $badge, $cta, $url]) {
                ContentListItem::updateOrCreate(
                    ['group' => $group, 'title' => $title],
                    compact('body', 'meta', 'badge', 'cta', 'url') + ['sort_order' => $i, 'is_active' => true]
                );
            }
        }
    }
}
