@extends('layouts.admin')

@section('title', 'SEO & AI Search Discovery')

@section('content')
<div class="content-header">
  <div class="content-header__meta">
    <div class="content-header__badge">
      <img src="{{ asset('assets/images/3d/3d-magnifier.png') }}" alt="" width="20" height="20" />
      <span>Search &amp; AI Intelligence</span>
    </div>
    <h1 class="content-header__title">SEO &amp; AI Search Discovery</h1>
    <p class="content-header__sub">
      Configure discovery metadata for
      <strong>Empetix (إمبيتكس)</strong> and all your <strong>SaaS &amp; Software projects</strong> in both Arabic and English.
    </p>
  </div>
  <div class="content-header__actions">
    <a href="{{ url('/llms.txt') }}" target="_blank" class="btn btn--ghost btn--sm">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
      <span>/llms.txt</span>
    </a>
    <a href="{{ url('/sitemap.xml') }}" target="_blank" class="btn btn--ghost btn--sm">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
      <span>/sitemap.xml</span>
    </a>
  </div>
</div>

{{-- Top Row: Live SEO & AI Health Score + Interactive Live SERP Preview --}}
<div class="seo-overview-grid">
  
  {{-- Health Score Card --}}
  <div class="card card--glass seo-health-card">
    <div class="card__header">
      <div class="card__header-icon">
        <img src="{{ asset('assets/images/3d/3d-target.png') }}" alt="" width="28" height="28" />
      </div>
      <div>
        <h3 class="card__title">Discovery &amp; Search Readiness</h3>
        <p class="card__sub">Local configuration checks only. Not a ranking, indexing, or AI-citation audit.</p>
      </div>
      <div class="seo-score-pill seo-score-pill--{{ strtolower(str_replace(' ', '-', $health['grade'])) }}">
        {{ $health['grade'] }}
      </div>
    </div>
    <div class="card__body">
      <div class="seo-score-meter-wrap">
        <div class="seo-score-meter">
          <div class="seo-score-meter__val">{{ $health['score'] }}%</div>
          <div class="seo-score-meter__bar">
            <div class="seo-score-meter__fill" style="width: {{ $health['score'] }}%;"></div>
          </div>
        </div>
      </div>

      <div class="seo-audit-checklist">
        @foreach($health['checks'] as $check)
          <div class="seo-audit-item {{ $check['passed'] ? 'is-passed' : 'is-pending' }}">
            <div class="seo-audit-item__icon">
              @if($check['passed'])
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              @else
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              @endif
            </div>
            <div class="seo-audit-item__content">
              <span class="seo-audit-item__label">{{ $check['label'] }}</span>
              <span class="seo-audit-item__msg">{{ $check['message'] }}</span>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Google & AI Search Mockup Preview --}}
  <div class="card card--glass seo-preview-card">
    <div class="card__header">
      <div class="card__header-icon">
        <img src="{{ asset('assets/images/3d/3d-eye.png') }}" alt="" width="28" height="28" />
      </div>
      <div>
        <h3 class="card__title">Real-Time Search Result Simulation</h3>
        <p class="card__sub">Illustrative previews only. Search providers choose their own snippets and citations.</p>
      </div>
    </div>
    <div class="card__body">
      
      {{-- Tab selection for Preview --}}
      <div class="seo-preview-tabs">
        <button type="button" class="seo-preview-tab is-active" onclick="empetixSwitchSeoPreview(event, 'google-en')">Google EN</button>
        <button type="button" class="seo-preview-tab" onclick="empetixSwitchSeoPreview(event, 'google-ar')">Google AR (إمبيتكس)</button>
        <button type="button" class="seo-preview-tab" onclick="empetixSwitchSeoPreview(event, 'ai-chat')">AI Model Citation</button>
      </div>

      {{-- Preview 1: Google English --}}
      <div id="seo-preview-google-en" class="seo-serp-box">
        <div class="seo-serp-box__head">
          <div class="seo-serp-box__fav">
            <img src="{{ brand_favicon() }}" alt="" width="18" height="18" />
          </div>
          <div class="seo-serp-box__meta">
            <span class="seo-serp-box__site">Empetix Solutions</span>
            <span class="seo-serp-box__url">{{ seo_url('/') }}</span>
          </div>
        </div>
        <div class="seo-serp-box__title" id="preview-serp-title-en">
          {{ $settings['seo_meta_title_en'] ?? 'Empetix Solutions — Software, SaaS & Digital Growth Studio' }}
        </div>
        <div class="seo-serp-box__snippet" id="preview-serp-desc-en">
          {{ $settings['seo_meta_description_en'] ?? 'Empetix Solutions is a premier digital engineering studio architecting enterprise SaaS platforms, custom software, FinTech solutions, and high-conversion web & mobile apps.' }}
        </div>
        <div class="seo-serp-box__sitelinks">
          <span>SaaS Projects</span> &bull; <span>Engineering Blog</span> &bull; <span>Team</span> &bull; <span>Contact</span>
        </div>
      </div>

      {{-- Preview 2: Google Arabic --}}
      <div id="seo-preview-google-ar" class="seo-serp-box is-hidden" dir="rtl">
        <div class="seo-serp-box__head">
          <div class="seo-serp-box__fav">
            <img src="{{ brand_favicon() }}" alt="" width="18" height="18" />
          </div>
          <div class="seo-serp-box__meta">
            <span class="seo-serp-box__site">حلول إمبيتكس للبرمجيات</span>
            <span class="seo-serp-box__url">{{ seo_url('/') }}</span>
          </div>
        </div>
        <div class="seo-serp-box__title" id="preview-serp-title-ar">
          {{ $settings['seo_meta_title_ar'] ?? 'حلول إمبيتكس — استوديو هندسة البرمجيات والساس والتحول الرقمي' }}
        </div>
        <div class="seo-serp-box__snippet" id="preview-serp-desc-ar">
          {{ $settings['seo_meta_description_ar'] ?? 'إمبيتكس (Empetix Solutions) هو استوديو هندسي متخصص في بناء وتطوير برمجيات الساس (SaaS)، المنصات السحابية، والتطبيقات الرقمية المتقدمة بتجربة مستخدم عربية وعالمية فائقة السرعة.' }}
        </div>
        <div class="seo-serp-box__sitelinks">
          <span>مشاريع الساس والأنظمة</span> &bull; <span>دراسات الحالة (Alfagn)</span> &bull; <span>تطوير تطبيقات الموبايل</span> &bull; <span>تواصل معنا</span>
        </div>
      </div>

      {{-- Preview 3: AI Citation (ChatGPT / Perplexity) --}}
      <div id="seo-preview-ai-chat" class="seo-serp-box is-hidden seo-serp-box--ai">
        <div class="seo-ai-badge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"/><path d="M12 6v6l4 2"/></svg>
          <span>AI Search Response &bull; Perplexity &amp; ChatGPT Search</span>
        </div>
        <p class="seo-ai-text">
          This is an illustrative card, not a real AI response. Public summaries contain the same page and service information visitors can read. Search providers decide whether to cite any page.
        </p>
        <div class="seo-ai-citation">
          <img src="{{ brand_favicon() }}" alt="" width="14" height="14" />
          <span>Source: {{ url('/llms.txt') }} (Optional content summary)</span>
        </div>
      </div>

    </div>
  </div>

</div>

{{-- Main Form Card --}}
<form action="{{ route('admin.seo.update') }}" method="POST" enctype="multipart/form-data" class="seo-form">
  @csrf
  @method('PUT')

  {{-- Section 1: Arabic Search Discovery (الظهور في محركات البحث بالعربي) --}}
  <div class="card card--glass form-card">
    <div class="card__header">
      <div class="card__header-icon">
        <img src="{{ asset('assets/images/3d/3d-document.png') }}" alt="" width="28" height="28" />
      </div>
      <div>
        <h3 class="card__title">Arabic Metadata Draft | مسودة البيانات العربية</h3>
        <p class="card__sub">هذه مسودة محفوظة. الصفحات الحالية بالإنجليزية؛ لا نعلن نسخة عربية قبل نشر ترجمة فعلية.</p>
      </div>
    </div>
    <div class="card__body form-grid" dir="rtl">
      
      <div class="form-group form-group--full">
        <label for="seo_meta_title_ar" class="form-label">
          <span>عنوان الموقع ومحركات البحث (بالعربي)</span>
          <span class="form-hint-badge">Title Tag</span>
        </label>
        <input type="text"
               name="seo_meta_title_ar"
               id="seo_meta_title_ar"
               class="form-control"
               value="{{ old('seo_meta_title_ar', $settings['seo_meta_title_ar'] ?? 'حلول إمبيتكس — استوديو هندسة البرمجيات والساس والتحول الرقمي') }}"
               placeholder="حلول إمبيتكس — استوديو هندسة البرمجيات والساس والتحول الرقمي"
               oninput="document.getElementById('preview-serp-title-ar').innerText = this.value || 'حلول إمبيتكس'" />
        <span class="form-hint">يظهر كعنوان رئيسي في نتائج بحث جوجل وشاشات المشاركة بالعربية (مفضل 50-60 حرف).</span>
      </div>

      <div class="form-group form-group--full">
        <label for="seo_meta_description_ar" class="form-label">
          <span>الوصف التعريفي لمحركات البحث (بالعربي)</span>
          <span class="form-hint-badge">Meta Description</span>
        </label>
        <textarea name="seo_meta_description_ar"
                  id="seo_meta_description_ar"
                  class="form-control form-control--textarea"
                  rows="3"
                  placeholder="إمبيتكس (Empetix Solutions) هو استوديو هندسي متخصص في بناء وتطوير برمجيات الساس (SaaS)..."
                  oninput="document.getElementById('preview-serp-desc-ar').innerText = this.value || ''">{{ old('seo_meta_description_ar', $settings['seo_meta_description_ar'] ?? 'إمبيتكس (Empetix Solutions) هو استوديو هندسي متخصص في بناء وتطوير برمجيات الساس (SaaS)، المنصات السحابية، والتطبيقات الرقمية المتقدمة بتجربة مستخدم عربية وعالمية فائقة السرعة.') }}</textarea>
        <span class="form-hint">يظهر أسفل العنوان في جوجل لمساعدة الزوار العرب على الضغط والتفاعل.</span>
      </div>

      <div class="form-group form-group--full">
        <label for="seo_keywords_ar" class="form-label">
          <span>الكلمات المفتاحية للبحث ومشاريع البرمجة والساس (بالعربي)</span>
          <span class="form-hint-badge">Meta Keywords</span>
        </label>
        <input type="text"
               name="seo_keywords_ar"
               id="seo_keywords_ar"
               class="form-control"
               value="{{ old('seo_keywords_ar', $settings['seo_keywords_ar'] ?? 'إمبيتكس, حلول إمبيتكس, شركة إمبيتكس, شركة برمجة, تطوير ساس, برمجة مواقع, تصميم تطبيقات, تحول رقمي, مصر, السعودية, نايل باي, محفظة رقمية') }}"
               placeholder="إمبيتكس, شركة برمجة, تطوير ساس, تصميم تطبيقات..." />
        <span class="form-hint">افصل بين الكلمات بفواصل (,). تشمل اسم الشركة والمشاريع البرمجية وتقنيات الاستوديو.</span>
      </div>

    </div>
  </div>

  {{-- Section 2: English Search Discovery --}}
  <div class="card card--glass form-card">
    <div class="card__header">
      <div class="card__header-icon">
        <img src="{{ asset('assets/images/3d/3d-rocket.png') }}" alt="" width="28" height="28" />
      </div>
      <div>
        <h3 class="card__title">English Global Discovery &amp; Metadata</h3>
        <p class="card__sub">English homepage title and description overrides. Meta keywords do not affect Google ranking.</p>
      </div>
    </div>
    <div class="card__body form-grid">
      
      <div class="form-group form-group--full">
        <label for="seo_meta_title_en" class="form-label">
          <span>Global Site &amp; Search Title (English)</span>
          <span class="form-hint-badge">Title Tag</span>
        </label>
        <input type="text"
               name="seo_meta_title_en"
               id="seo_meta_title_en"
               class="form-control"
               value="{{ old('seo_meta_title_en', $settings['seo_meta_title_en'] ?? 'Empetix Solutions — Software, SaaS & Digital Growth Studio') }}"
               placeholder="Empetix Solutions — Software, SaaS & Digital Growth Studio"
               oninput="document.getElementById('preview-serp-title-en').innerText = this.value || 'Empetix Solutions'" />
        <span class="form-hint">Displayed in Google, Bing, browser tabs, and bookmark previews (optimal: 50-60 characters).</span>
      </div>

      <div class="form-group form-group--full">
        <label for="seo_meta_description_en" class="form-label">
          <span>Global Meta Description (English)</span>
          <span class="form-hint-badge">Meta Description</span>
        </label>
        <textarea name="seo_meta_description_en"
                  id="seo_meta_description_en"
                  class="form-control form-control--textarea"
                  rows="3"
                  placeholder="Empetix Solutions is a premier digital engineering studio architecting enterprise SaaS platforms..."
                  oninput="document.getElementById('preview-serp-desc-en').innerText = this.value || ''">{{ old('seo_meta_description_en', $settings['seo_meta_description_en'] ?? 'Empetix Solutions is a premier digital engineering studio architecting enterprise SaaS platforms, custom software, FinTech solutions, and high-conversion web & mobile apps.') }}</textarea>
        <span class="form-hint">Appears under the headline in Google search results (optimal: 140-160 characters).</span>
      </div>

      <div class="form-group form-group--full">
        <label for="seo_keywords_en" class="form-label">
          <span>Search Keywords &amp; Software Tags (English)</span>
          <span class="form-hint-badge">Meta Keywords</span>
        </label>
        <input type="text"
               name="seo_keywords_en"
               id="seo_keywords_en"
               class="form-control"
               value="{{ old('seo_keywords_en', $settings['seo_keywords_en'] ?? 'Empetix, Empetix Solution, SaaS Architecture, Custom Software, EdTech, Alfagn, Western eLearning Academy, Laravel, Flutter, Mobile App Development, Cairo Studio') }}"
               placeholder="Empetix, SaaS, Software Engineering..." />
        <span class="form-hint">Comma-separated terms describing Empetix, your products, and engineering capabilities.</span>
      </div>

      <div class="form-group form-group--full">
        <label for="seo_canonical_base" class="form-label">
          <span>Canonical URL Base</span>
          <span class="form-hint-badge">Canonical</span>
        </label>
        <input type="text"
               name="seo_canonical_base"
               id="seo_canonical_base"
               class="form-control"
               value="{{ old('seo_canonical_base', $settings['seo_canonical_base'] ?? url('/')) }}"
               placeholder="https://empetix.com" />
        <span class="form-hint">Prevents duplicate content penalties by explicitly declaring the preferred domain for search engines.</span>
      </div>

    </div>
  </div>

  {{-- Section 3: Social & OpenGraph Visual Assets --}}
  <div class="card card--glass form-card">
    <div class="card__header">
      <div class="card__header-icon">
        <img src="{{ asset('assets/images/3d/3d-image.png') }}" alt="" width="28" height="28" />
      </div>
      <div>
        <h3 class="card__title">Social Share &amp; OpenGraph Media Card</h3>
        <p class="card__sub">The visual image shown when your link is shared on LinkedIn, X/Twitter, WhatsApp, and Slack</p>
      </div>
    </div>
    <div class="card__body">
      <x-admin.image
        name="seo_og_image"
        label="Default Social Share (OG) Preview Image"
        :current="$settings['seo_og_image'] ?? null"
        hint="Recommended 1200 x 630 px (PNG, JPG, or WEBP). Used as the high-impact thumbnail card across all social networks."
      />
    </div>
  </div>

  {{-- Section 4: AI Model Search Discovery & llms.txt Context --}}
  <div class="card card--glass form-card">
    <div class="card__header">
      <div class="card__header-icon">
        <img src="{{ asset('assets/images/3d/3d-lightbulb.png') }}" alt="" width="28" height="28" />
      </div>
      <div>
        <h3 class="card__title">AI Search Discovery &amp; Ingestion Engine (llms.txt)</h3>
        <p class="card__sub">Optional public content summary. No search provider guarantees using llms.txt.</p>
      </div>
    </div>
    <div class="card__body form-grid">
      
      <div class="form-group form-group--half">
        <label class="form-label">
          <span>Welcome AI Crawlers (ChatGPT, Perplexity, Claude)</span>
        </label>
        <label class="form-toggle-pill">
          <input type="hidden" name="seo_welcome_ai_bots" value="0" />
          <input type="checkbox" name="seo_welcome_ai_bots" value="1" {{ ($settings['seo_welcome_ai_bots'] ?? '1') === '1' ? 'checked' : '' }} />
          <span class="form-toggle-pill__slider"></span>
          <span class="form-toggle-pill__text">Legacy AI crawler permissions</span>
        </label>
        <span class="form-hint">Legacy policy includes search and training crawlers. OAI-SearchBot controls ChatGPT search; GPTBot and Google-Extended concern training. Training access is not required for search visibility.</span>
      </div>

      <div class="form-group form-group--half">
        <label class="form-label">
          <span>Search Engine Indexing</span>
        </label>
        <label class="form-toggle-pill">
          <input type="hidden" name="seo_allow_indexing" value="0" />
          <input type="checkbox" name="seo_allow_indexing" value="1" {{ ($settings['seo_allow_indexing'] ?? '1') === '1' ? 'checked' : '' }} />
          <span class="form-toggle-pill__slider"></span>
          <span class="form-toggle-pill__text">Allow Google &amp; Bing Indexation</span>
        </label>
        <span class="form-hint">Emits &lt;meta name="robots" content="index, follow"&gt;. Uncheck only during staging maintenance.</span>
      </div>

      <div class="form-group form-group--full">
        <label for="seo_ai_company_bio" class="form-label">
          <span>Company briefing draft (not published automatically)</span>
          <span class="form-hint-badge">AI LLM Context</span>
        </label>
        <textarea name="seo_ai_company_bio"
                  id="seo_ai_company_bio"
                  class="form-control form-control--textarea"
                  rows="4"
                  placeholder="Record verified company facts here for editorial review.">{{ old('seo_ai_company_bio', $settings['seo_ai_company_bio'] ?? '') }}</textarea>
        <span class="form-hint">Retained as a draft note. Public summaries use visible CMS content, not private instructions or unverified business claims.</span>
      </div>

    </div>
  </div>

  {{-- Save Actions --}}
  <div class="form-actions-bar">
    <div class="form-actions-bar__inner">
      <span class="form-actions-bar__info">Changes affect this installation. Production changes require deployment.</span>
      <button type="submit" class="btn btn--primary btn--lg">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        <span>Save SEO &amp; AI Settings</span>
      </button>
    </div>
  </div>

</form>

<script>
  function empetixSwitchSeoPreview(event, tabKey) {
    document.querySelectorAll('.seo-preview-tab').forEach(function(b) { b.classList.remove('is-active'); });
    event.currentTarget.classList.add('is-active');

    document.getElementById('seo-preview-google-en').classList.add('is-hidden');
    document.getElementById('seo-preview-google-ar').classList.add('is-hidden');
    document.getElementById('seo-preview-ai-chat').classList.add('is-hidden');

    if (tabKey === 'google-en') {
      document.getElementById('seo-preview-google-en').classList.remove('is-hidden');
    } else if (tabKey === 'google-ar') {
      document.getElementById('seo-preview-google-ar').classList.remove('is-hidden');
    } else if (tabKey === 'ai-chat') {
      document.getElementById('seo-preview-ai-chat').classList.remove('is-hidden');
    }
  }
</script>
@endsection
