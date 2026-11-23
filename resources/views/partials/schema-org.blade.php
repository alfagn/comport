@php
  $schemaGraph = [
    [
      '@type' => 'Organization',
      '@id' => url('/#organization'),
      'name' => 'Empetix Solutions',
      'alternateName' => [
        'Empetix',
        'Empetix Solution',
        'إمبيتكس',
        'حلول إمبيتكس',
        'شركة إمبيتكس للبرمجيات',
        'Empetix Software Studio',
      ],
      'url' => url('/'),
      'logo' => [
        '@type' => 'ImageObject',
        '@id' => url('/#logo'),
        'url' => brand_logo(),
        'caption' => 'Empetix Solutions Logo',
      ],
      'image' => seo_og_image(),
      'description' => seo_meta_description('en'),
      'email' => setting('contact_email', 'hello@empetix.com'),
      'telephone' => setting('contact_phone', '+201000000000'),
      'address' => [
        '@type' => 'PostalAddress',
        'addressLocality' => 'Cairo',
        'addressCountry' => 'EG',
      ],
      'knowsAbout' => [
        'SaaS Architecture & Development',
        'Custom Enterprise Software',
        'FinTech Solutions',
        'Mobile App Development',
        'Flutter & Laravel Engineering',
        'Bilingual Arabic-First UX Design Systems',
        'Growth Engineering & CAPI Integration',
        'برمجة وتطوير البرمجيات',
        'حلول الساس والأنظمة المؤسسية',
        'تطوير تطبيقات الويب والهاتف',
      ],
    ],
    [
      '@type' => 'WebSite',
      '@id' => url('/#website'),
      'url' => url('/'),
      'name' => seo_meta_title('en'),
      'alternateName' => seo_meta_title('ar'),
      'publisher' => [
        '@id' => url('/#organization'),
      ],
      'inLanguage' => ['en-US', 'ar-EG', 'ar-SA'],
      'potentialAction' => [
        '@type' => 'SearchAction',
        'target' => url('/blog') . '?q={search_term_string}',
        'query-input' => 'required name=search_term_string',
      ],
    ],
  ];

  if (isset($project) && $project instanceof \App\Models\Project) {
    $schemaGraph[] = [
      '@type' => 'SoftwareApplication',
      '@id' => url('/work/'.$project->slug.'#software'),
      'name' => $project->title,
      'headline' => $project->excerpt ?? $project->title,
      'applicationCategory' => 'BusinessApplication',
      'operatingSystem' => 'Web, iOS, Android',
      'author' => ['@id' => url('/#organization')],
      'creator' => ['@id' => url('/#organization')],
      'publisher' => ['@id' => url('/#organization')],
      'description' => $project->excerpt ?? '',
      'url' => url('/work/'.$project->slug),
      'image' => $project->image ? content_asset($project->image) : seo_og_image(),
    ];
  } elseif (isset($post) && $post instanceof \App\Models\Post) {
    $schemaGraph[] = [
      '@type' => 'BlogPosting',
      '@id' => url('/blog/'.$post->slug.'#article'),
      'headline' => $post->title,
      'description' => $post->excerpt ?? '',
      'datePublished' => $post->published_at ? \Illuminate\Support\Carbon::parse($post->published_at)->toIso8601String() : now()->toIso8601String(),
      'dateModified' => $post->updated_at ? $post->updated_at->toIso8601String() : now()->toIso8601String(),
      'mainEntityOfPage' => url('/blog/'.$post->slug),
      'image' => $post->cover_image ? content_asset($post->cover_image) : seo_og_image(),
      'author' => [
        '@type' => 'Person',
        'name' => $post->author_name ?? 'Empetix Solutions',
      ],
      'publisher' => ['@id' => url('/#organization')],
    ];
  }
@endphp
<script type="application/ld+json">
{!! json_encode([
  '@context' => 'https://schema.org',
  '@graph' => $schemaGraph,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
