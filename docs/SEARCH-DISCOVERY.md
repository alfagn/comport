# Search and AI discovery operations

The intended production origin is `https://empetix.com`, confirmed by the owner. The additive migration supplies it only when `seo_canonical_base` has never been stored. Existing custom and blank values remain unchanged. Review the preferred origin in **SEO & AI Search Discovery** after updating. A blank or invalid legacy value falls back to `APP_URL`, so configure that environment value for the real host too.

## Update safely

Back up the database, uploads and `.env`, and preserve local source changes. On `laravel-13-dashboard`:

```sh
git pull --ff-only origin laravel-13-dashboard
php artisan migrate
php artisan optimize:clear
```

No reset, seed or destructive migration is required. No deployment, DNS change or search-engine submission is performed by this patch.

Discovery endpoints are dynamic Laravel responses. Git removes the old tracked `public/robots.txt`, `public/sitemap.xml`, `public/llms.txt` and `public/llms-full.txt` mirrors. If an existing installation has generated, locally modified copies, preserve/review those before pulling, then remove stale mirrors from the deployed public directory so they cannot bypass Laravel. Do not delete the controllers or routes. Configure the web root as Laravel's `public/` directory and route missing files to `public/index.php`.

## What the controls mean

- The English title and description overrides affect the homepage HTML and sharing metadata. Other pages keep their own CMS-backed or entity-backed copy. Explicit blank overrides remain blank.
- Arabic metadata is a saved draft, not a translated page. No Arabic `hreflang` is emitted until real Arabic URLs exist.
- The canonical origin is used by canonical links, social URLs, schema IDs and sitemap locations. Social image overrides are now used by the public templates.
- The URL inventory lists current public records, not confirmed indexed pages. Static-page last modification dates are omitted when they are not known.
- The dashboard score is a local settings checklist, not a Google, Bing or AI visibility score. Previews are illustrative.
- The legacy AI crawler toggle preserves the existing policy. It includes training-related agents as well as search agents. ChatGPT search uses `OAI-SearchBot`; `GPTBot` concerns training, and training access is not required for search visibility. This patch does not silently change a saved training choice.
- `llms.txt` is an optional summary of visible CMS content. It is not a ranking requirement or guaranteed ingestion channel. The saved company briefing is retained as a draft and is no longer published automatically. No new office, platform, client-result or service claims are added by the generator.
- The old Ping action now truthfully explains that it only refreshes local discovery state. It does not notify any search engine.

## Production follow-through

After an authorized deployment, inspect actual responses on the production host. Check canonical/OG URL agreement, status codes, fresh XML, no private or unpublished records, and crawler access through hosting/CDN rules. Browser access alone does not prove that Googlebot or OAI-SearchBot can access the site.

Use verified Search Console and Bing Webmaster Tools accounts for ownership, sitemap submission and indexing diagnostics. Google also exposes a Search generative AI inclusion control in Search Console. Those account operations are separate from this code patch.

For service-search growth, confirm the real markets, customer languages, SaaS/ERP/marketing scope, genuine case studies and supported outcomes before publishing focused service pages. Do not create doorway pages, invented client quotes, fake offices, or mass keyword variants. Existing CMS content remains the owner's responsibility to verify. Rankings and AI citations are not guaranteed.

## Primary references checked

- https://developers.google.com/search/docs/appearance/ai-features
- https://support.google.com/webmasters/answer/16908024?hl=en
- https://developers.openai.com/api/docs/bots
- https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap
- https://developers.google.com/search/blog/2023/06/sitemaps-lastmod-ping
- https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls
- https://developers.google.com/search/docs/specialty/international/localized-versions
- https://developers.google.com/search/docs/appearance/structured-data/sd-policies
- https://developers.google.com/search/docs/crawling-indexing/special-tags

## English service-page drafts

The service-page workflow is English-only. Open **Services > English service pages and drafts** to review the three unpublished English drafts: SaaS development, ERP systems and performance marketing. These are separate from the existing homepage Services. The draft copy contains scoping questions, not proof of delivered capability. Review it against the actual offer before publishing.

Each page has an editor and an authenticated **Private preview**. Previews send `noindex` and private/no-store headers; anonymous or unauthorized users cannot access them. An unpublished page returns 404 on its future public URL and is absent from public links, sitemap and optional summaries. The explicit publish switch affects only that installation. It is not a deployment.

Public URLs are `/services` and `/services/{slug}`, with English language metadata and left-to-right layout. There is no Arabic service route, language switch or Arabic `hreflang`. Existing Arabic draft rows and SEO settings are preserved in the database but excluded from the active editor and all service discovery, even if a legacy Arabic row is marked published. They are not silently deleted or translated. The historical creation migration still includes those archived drafts for reproducibility; no data-reset command is needed.

The SEO dashboard now edits English metadata only. Saving it does not erase the archived Arabic settings. Existing user-entered CMS content outside these service drafts is not overwritten; review any legacy copy before launch. Published English pages enter sitemap and optional summaries. The main public layout only exposes service navigation when an English page is published. No location/keyword doorway pages or automatic language redirects are added.

The original logo mark now has a stable square 192px PNG fallback and an ICO. Existing custom favicon settings are preserved. Google's latest documented guidance recommends a square raster icon larger than 48px, a stable URL, and crawl access to the homepage and icon. Correct markup makes an icon eligible, not guaranteed: https://developers.google.com/search/docs/appearance/favicon-in-search

Before launch, validate the copy, edition publication state, actual canonical production URLs, English metadata, contact routes, and mobile layout. Google/Bing webmaster ownership, Search Console generative-AI inclusion and any URL submission remain separate authorized work. Bing's current guidance also emphasizes clear internal links and accurate content for search/grounding eligibility: https://www2.bing.com/webmasters/help/Webmaster-Guidelines-30fba23a
