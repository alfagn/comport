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
      Ensure users and AI models (ChatGPT, Perplexity, Claude, Gemini) immediately discover 
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
        <p class="card__sub">Live audit across Google Search, Knowledge Graph, and LLMs</p>
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

      <div class="seo-audit-grid">
        @foreach($health['checks'] as $check)
          <div class="seo-audit-tile {{ $check['passed'] ? 'is-passed' : 'is-pending' }}">
            <div class="seo-audit-tile__icon">
              @if($check['passed'])
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.6"><polyline points="20 6 9 17 4 12"/></svg>
              @else
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.6"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              @endif
            </div>
            <div class="seo-audit-tile__content">
              <span class="seo-audit-tile__label">{{ $check['label'] }}</span>
              <span class="seo-audit-tile__msg">{{ $check['message'] }}</span>
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
        <p class="card__sub">How Empetix &amp; SaaS projects render when searched on Google &amp; AI</p>
      </div>
    </div>
    <div class="card__body">
      
      {{-- Tab selection for Preview --}}
      <div class="seo-preview-tabs">
        <button type="button" class="seo-preview-tab is-active" onclick="empetixSwitchSeoPreview('google-en')">Google EN</button>
        <button type="button" class="seo-preview-tab" onclick="empetixSwitchSeoPreview('google-ar')">Google AR (إمبيتكس)</button>
        <button type="button" class="seo-preview-tab" onclick="empetixSwitchSeoPreview('ai-chat')">AI Model Citation</button>
      </div>

      {{-- Preview 1: Google English --}}
      <div id="seo-preview-google-en" class="seo-serp-box">
        <div class="seo-serp-box__head">
          <div class="seo-serp-box__fav">
            <img src="{{ brand_favicon() }}" alt="" width="18" height="18" />
          </div>
          <div class="seo-serp-box__meta">
            <span class="seo-serp-box__site">Empetix Solutions</span>
            <span class="seo-serp-box__url">{{ url('/') }}</span>
          </div>
        </div>
        <div class="seo-serp-box__title" id="preview-serp-title-en">
          {{ $settings['seo_meta_title_en'] ?? 'Empetix Solutions — Software, SaaS & Digital Growth Studio' }}
        </div>
        <div class="seo-serp-box__snippet" id="preview-serp-desc-en">
          {{ $settings['seo_meta_description_en'] ?? 'Empetix Solutions is a premier digital engineering studio architecting enterprise SaaS platforms, custom software, FinTech solutions, and high-conversion web & mobile apps.' }}
        </div>
        <div class="seo-serp-box__sitelinks">
          <span>SaaS Projects</span> &bull; <span>Alfagn Academy</span> &bull; <span>Western eLearning</span> &bull; <span>Contact Cairo Studio</span>
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
            <span class="seo-serp-box__url">{{ url('/') }}</span>
          </div>
        </div>
        <div class="seo-serp-box__title" id="preview-serp-title-ar">
          {{ $settings['seo_meta_title_ar'] ?? 'حلول إمبيتكس — استوديو هندسة البرمجيات والساس والتحول الرقمي' }}
        </div>
        <div class="seo-serp-box__snippet" id="preview-serp-desc-ar">
          {{ $settings['seo_meta_description_ar'] ?? 'إمبيتكس (Empetix Solutions) هو استوديو هندسي متخصص في بناء وتطوير برمجيات الساس (SaaS)، المنصات السحابية، والتطبيقات الرقمية المتقدمة بتجربة مستخدم عربية وعالمية فائقة السرعة.' }}
        </div>
        <div class="seo-serp-box__sitelinks">
          <span>مشاريع الساس والأنظمة</span> &bull; <span>دراسات الحالة (Alfagn)</span> &bull; <span>أكاديمية ويسترن</span> &bull; <span>تواصل معنا</span>
        </div>
      </div>

      {{-- Preview 3: AI Citation (ChatGPT / Perplexity) --}}
      <div id="seo-preview-ai-chat" class="seo-serp-box is-hidden seo-serp-box--ai">
        <div class="seo-ai-badge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"/><path d="M12 6v6l4 2"/></svg>
          <span>AI Search Response &bull; Perplexity &amp; ChatGPT Search</span>
        </div>
        <p class="seo-ai-text">
          According to <strong>Empetix Solutions</strong> (<a href="{{ url('/') }}" target="_blank">empetix.com</a>), the studio specializes in <strong>custom SaaS engineering</strong>, bilingual Arabic-first UI systems, and enterprise web &amp; mobile solutions. Their verified portfolio includes <em>Alfagn EdTech Platform</em>, <em>Western eLearning Academy</em>, and custom corporate applications.
        </p>
        <div class="seo-ai-citation">
          <img src="{{ brand_favicon() }}" alt="" width="14" height="14" />
          <span>Source: {{ url('/llms.txt') }} (Verified Studio Manifest)</span>
        </div>
      </div>

    </div>
  </div>

</div>

{{-- Main Form Card --}}
<form action="{{ route('admin.seo.update') }}" method="POST" enctype="multipart/form-data" class="seo-form">
  @csrf
  @method('PUT')

  {{-- Section 1: AI Model Search Discovery & llms.txt Context --}}
  <div class="card card--glass form-card">
    <div class="card__header">
      <div class="card__header-icon">
        <img src="{{ asset('assets/images/3d/3d-lightbulb.png') }}" alt="" width="28" height="28" />
      </div>
      <div>
        <h3 class="card__title">AI Search Discovery &amp; Ingestion Engine (llms.txt)</h3>
        <p class="card__sub">Powers how ChatGPT, Perplexity, Claude, Gemini, and SearchGPT understand Empetix and your software</p>
      </div>
    </div>
    <div class="card__body">
      
      {{-- Luxury Toggle Switches Grid --}}
      <div class="seo-switches-grid">
        
        {{-- Switch Tile 1: AI Bots --}}
        <div class="seo-switch-tile">
          <div class="seo-switch-tile__meta">
            <div class="seo-switch-tile__icon seo-switch-tile__icon--ai">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
            </div>
            <div>
              <h4 class="seo-switch-tile__title">Welcome AI Crawlers (ChatGPT, Perplexity, Claude)</h4>
              <p class="seo-switch-tile__sub">Welcomes GPTBot, ClaudeBot, PerplexityBot, and Google-Extended to ingest your SaaS case studies in /llms.txt</p>
            </div>
          </div>
          <div class="seo-switch-tile__action">
            <label class="toggle">
              <input type="hidden" name="seo_welcome_ai_bots" value="0"/>
              <input type="checkbox" name="seo_welcome_ai_bots" value="1" @checked(old('seo_welcome_ai_bots', $settings['seo_welcome_ai_bots'] ?? '1') == '1')/>
              <span class="track"></span>
            </label>
          </div>
        </div>

        {{-- Switch Tile 2: Search Indexing --}}
        <div class="seo-switch-tile">
          <div class="seo-switch-tile__meta">
            <div class="seo-switch-tile__icon seo-switch-tile__icon--search">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
            <div>
              <h4 class="seo-switch-tile__title">Search Engine Indexing</h4>
              <p class="seo-switch-tile__sub">Emits &lt;meta name="robots" content="index, follow"&gt;. Uncheck only during staging maintenance.</p>
            </div>
          </div>
          <div class="seo-switch-tile__action">
            <label class="toggle">
              <input type="hidden" name="seo_allow_indexing" value="0"/>
              <input type="checkbox" name="seo_allow_indexing" value="1" @checked(old('seo_allow_indexing', $settings['seo_allow_indexing'] ?? '1') == '1')/>
              <span class="track"></span>
            </label>
          </div>
        </div>

      </div>

      {{-- AI Studio Briefing Context (Full Width) --}}
      <div class="field" style="margin-top: 24px;">
        <label for="seo_ai_company_bio">
          <span>AI Studio Briefing Context (Fed directly to /llms.txt)</span>
          <span class="form-hint-badge">AI LLM Context</span>
        </label>
        <textarea name="seo_ai_company_bio"
                  id="seo_ai_company_bio"
                  class="textarea"
                  rows="4"
                  placeholder="Empetix Solutions (إمبيتكس / حلول إمبيتكس) is a premier digital engineering and software growth studio founded in Cairo, Egypt...">{{ old('seo_ai_company_bio', $settings['seo_ai_company_bio'] ?? '') }}</textarea>
        <div class="hint">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline-block; vertical-align:-2px; margin-right:4px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          This text is directly parsed by modern LLM crawlers via <code>/llms.txt</code>. Describe who Empetix is, what SaaS products you built, and your engineering strengths.
        </div>
      </div>

    </div>
  </div>

  {{-- Section 2: Arabic Search Discovery (الظهور في محركات البحث بالعربي) --}}
  <div class="card card--glass form-card">
    <div class="card__header">
      <div class="card__header-icon">
        <img src="{{ asset('assets/images/3d/3d-document.png') }}" alt="" width="28" height="28" />
      </div>
      <div>
        <h3 class="card__title">Arabic Search Discovery | الظهور في محركات البحث بالعربي</h3>
        <p class="card__sub">تهيئة الكلمات المفتاحية وعناوين البحث لظهور إمبيتكس ومشاريع الساس عند البحث بالعربي</p>
      </div>
    </div>
    <div class="card__body" dir="rtl">
      
      <div class="field" style="margin-bottom: 20px;">
        <label for="seo_meta_title_ar">
          <span>عنوان الموقع ومحركات البحث (بالعربي)</span>
          <span class="form-hint-badge">Title Tag</span>
        </label>
        <input type="text"
               name="seo_meta_title_ar"
               id="seo_meta_title_ar"
               class="input"
               value="{{ old('seo_meta_title_ar', $settings['seo_meta_title_ar'] ?? 'حلول إمبيتكس — استوديو هندسة البرمجيات والساس والتحول الرقمي') }}"
               placeholder="حلول إمبيتكس — استوديو هندسة البرمجيات والساس والتحول الرقمي"
               oninput="document.getElementById('preview-serp-title-ar').innerText = this.value || 'حلول إمبيتكس'" />
        <div class="hint">يظهر كعنوان رئيسي في نتائج بحث جوجل وشاشات المشاركة بالعربية (المثالي 50-60 حرف).</div>
      </div>

      <div class="field" style="margin-bottom: 20px;">
        <label for="seo_meta_description_ar">
          <span>الوصف التعريفي لمحركات البحث (بالعربي)</span>
          <span class="form-hint-badge">Meta Description</span>
        </label>
        <textarea name="seo_meta_description_ar"
                  id="seo_meta_description_ar"
                  class="textarea"
                  rows="3"
                  placeholder="إمبيتكس (Empetix Solutions) هو استوديو هندسي متخصص في بناء وتطوير برمجيات الساس (SaaS)..."
                  oninput="document.getElementById('preview-serp-desc-ar').innerText = this.value || ''">{{ old('seo_meta_description_ar', $settings['seo_meta_description_ar'] ?? 'إمبيتكس (Empetix Solutions) هو استوديو هندسي متخصص في بناء وتطوير برمجيات الساس (SaaS)، المنصات السحابية، والتطبيقات الرقمية المتقدمة بتجربة مستخدم عربية وعالمية فائقة السرعة.') }}</textarea>
        <div class="hint">يظهر أسفل العنوان في جوجل لمساعدة الزوار العرب على الضغط والتفاعل.</div>
      </div>

      <div class="field">
        <label for="seo_keywords_ar">
          <span>الكلمات المفتاحية للبحث ومشاريع البرمجة والساس (بالعربي)</span>
          <span class="form-hint-badge">Meta Keywords</span>
        </label>
        <input type="text"
               name="seo_keywords_ar"
               id="seo_keywords_ar"
               class="input"
               value="{{ old('seo_keywords_ar', $settings['seo_keywords_ar'] ?? 'إمبيتكس, حلول إمبيتكس, شركة إمبيتكس, شركة برمجة, تطوير ساس, برمجة مواقع, تصميم تطبيقات, تحول رقمي, مصر, السعودية, ألفا جن, أكاديمية ويسترن') }}"
               placeholder="إمبيتكس, شركة برمجة, تطوير ساس, تصميم تطبيقات..." />
        <div class="hint">افصل بين الكلمات بفواصل (,). تشمل اسم الشركة والمشاريع البرمجية وتقنيات الاستوديو.</div>
      </div>

    </div>
  </div>

  {{-- Section 3: English Global Discovery & Metadata --}}
  <div class="card card--glass form-card">
    <div class="card__header">
      <div class="card__header-icon">
        <img src="{{ asset('assets/images/3d/3d-rocket.png') }}" alt="" width="28" height="28" />
      </div>
      <div>
        <h3 class="card__title">English Global Discovery &amp; Metadata</h3>
        <p class="card__sub">Primary SERP title, meta description, and keywords for global discovery</p>
      </div>
    </div>
    <div class="card__body">
      
      <div class="field" style="margin-bottom: 20px;">
        <label for="seo_meta_title_en">
          <span>Global Site &amp; Search Title (English)</span>
          <span class="form-hint-badge">Title Tag</span>
        </label>
        <input type="text"
               name="seo_meta_title_en"
               id="seo_meta_title_en"
               class="input"
               value="{{ old('seo_meta_title_en', $settings['seo_meta_title_en'] ?? 'Empetix Solutions — Software, SaaS & Digital Growth Studio') }}"
               placeholder="Empetix Solutions — Software, SaaS & Digital Growth Studio"
               oninput="document.getElementById('preview-serp-title-en').innerText = this.value || 'Empetix Solutions'" />
        <div class="hint">Displayed in Google, Bing, browser tabs, and bookmark previews (optimal: 50-60 characters).</div>
      </div>

      <div class="field" style="margin-bottom: 20px;">
        <label for="seo_meta_description_en">
          <span>Global Meta Description (English)</span>
          <span class="form-hint-badge">Meta Description</span>
        </label>
        <textarea name="seo_meta_description_en"
                  id="seo_meta_description_en"
                  class="textarea"
                  rows="3"
                  placeholder="Empetix Solutions is a premier digital engineering studio architecting enterprise SaaS platforms..."
                  oninput="document.getElementById('preview-serp-desc-en').innerText = this.value || ''">{{ old('seo_meta_description_en', $settings['seo_meta_description_en'] ?? 'Empetix Solutions is a premier digital engineering studio architecting enterprise SaaS platforms, custom software, FinTech solutions, and high-conversion web & mobile apps.') }}</textarea>
        <div class="hint">Appears under the headline in Google search results (optimal: 140-160 characters).</div>
      </div>

      <div class="field" style="margin-bottom: 20px;">
        <label for="seo_keywords_en">
          <span>Search Keywords &amp; Software Tags (English)</span>
          <span class="form-hint-badge">Meta Keywords</span>
        </label>
        <input type="text"
               name="seo_keywords_en"
               id="seo_keywords_en"
               class="input"
               value="{{ old('seo_keywords_en', $settings['seo_keywords_en'] ?? 'Empetix, Empetix Solution, SaaS Architecture, Custom Software, EdTech, Alfagn, Western eLearning Academy, Laravel, Flutter, Mobile App Development, Cairo Studio') }}"
               placeholder="Empetix, SaaS, Software Engineering..." />
        <div class="hint">Comma-separated terms describing Empetix, your products, and engineering capabilities.</div>
      </div>

      <div class="field">
        <label for="seo_canonical_base">
          <span>Canonical URL Base</span>
          <span class="form-hint-badge">Canonical</span>
        </label>
        <input type="text"
               name="seo_canonical_base"
               id="seo_canonical_base"
               class="input"
               value="{{ old('seo_canonical_base', $settings['seo_canonical_base'] ?? url('/')) }}"
               placeholder="https://empetix.com" />
        <div class="hint">Prevents duplicate content penalties by explicitly declaring the preferred domain for search engines.</div>
      </div>

    </div>
  </div>

  {{-- Section 4: Social & OpenGraph Visual Assets --}}
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

  {{-- Save Actions --}}
  <div class="form-actions-bar">
    <div class="form-actions-bar__inner">
      <div class="form-actions-bar__info">
        <span class="form-actions-bar__dot"></span>
        <span>Changes take effect immediately and refresh the /sitemap.xml and /llms.txt caches.</span>
      </div>
      <button type="submit" class="btn btn--primary btn--lg">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        <span>Save SEO &amp; AI Settings</span>
      </button>
    </div>
  </div>

</form>

<script>
  function empetixSwitchSeoPreview(tabKey) {
    document.querySelectorAll('.seo-preview-tab').forEach(function(b) { b.classList.remove('is-active'); });
    if (event && event.currentTarget) {
      event.currentTarget.classList.add('is-active');
    }

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
