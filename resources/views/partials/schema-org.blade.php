@php
  // Describe only public CMS content. No inferred office, expertise, platforms, or contacts.
  $organizationId = seo_url('/#organization');
  $schemaGraph = [
    [
      '@type' => 'Organization', '@id' => $organizationId,
      'name' => setting('site_name', 'Empetix'), 'url' => seo_url('/'),
      'logo' => brand_logo(),
    ],
    [
      '@type' => 'WebSite', '@id' => seo_url('/#website'),
      'url' => seo_url('/'), 'name' => setting('site_name', 'Empetix'),
      'publisher' => ['@id' => $organizationId], 'inLanguage' => 'en',
    ],
  ];
  if (isset($member) && $member instanceof \App\Models\Member) {
    $schemaGraph[] = [
      '@type' => 'ProfilePage', '@id' => seo_current_url().'#profile',
      'url' => seo_current_url(),
      'mainEntity' => [
        '@type' => 'Person', 'name' => $member->name,
        'jobTitle' => $member->role, 'url' => seo_current_url(),
        'worksFor' => ['@id' => $organizationId],
      ],
    ];
  } elseif (isset($project) && $project instanceof \App\Models\Project) {
    $schemaGraph[] = [
      '@type' => 'CreativeWork', '@id' => seo_current_url().'#work',
      'name' => $project->title, 'description' => strip_tags($project->excerpt ?? ''),
      'url' => seo_current_url(), 'publisher' => ['@id' => $organizationId],
    ];
  } elseif (isset($post) && $post instanceof \App\Models\Post) {
    $article = [
      '@type' => 'BlogPosting', '@id' => seo_current_url().'#article',
      'headline' => $post->title, 'description' => strip_tags($post->excerpt ?? ''),
      'mainEntityOfPage' => seo_current_url(), 'publisher' => ['@id' => $organizationId],
    ];
    if ($post->published_at) { $article['datePublished'] = $post->published_at->toIso8601String(); }
    if ($post->updated_at) { $article['dateModified'] = $post->updated_at->toIso8601String(); }
    if ($post->cover_image) { $article['image'] = content_asset($post->cover_image); }
    $schemaGraph[] = $article;
  }
@endphp
<script type="application/ld+json">{!! json_encode([
  '@@context' => 'https://schema.org', '@graph' => $schemaGraph,
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
