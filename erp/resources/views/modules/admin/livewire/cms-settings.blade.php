<div class="space-y-6 max-w-7xl mx-auto font-sans">
    <!-- Top Executive Studio Banner -->
    <div class="bg-gradient-to-r from-[#050C1A] via-[#08142C] to-[#0D1E3D] rounded-2xl p-6 sm:p-8 text-white shadow-card border border-white/10 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#FF4D1C 1px, transparent 1px); background-size: 24px 24px;"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-accent-500/20 text-accent-500 border border-accent-500/30 text-[11px] font-mono font-bold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-accent-500 animate-pulse"></span>
                    {{ app()->getLocale() === 'ar' ? 'تحكم شامل 100% بكل صفحات وأقسام الموقع' : '100% Full Website Page & Section Control' }}
                </div>
                <h2 class="text-2xl sm:text-3xl font-heading font-black tracking-tight">
                    {{ app()->getLocale() === 'ar' ? 'استوديو إدارة محتوى الموقع والصفحات (7 أقسام رئيسية)' : 'Website Pages, Hero & Sections Control Studio' }}
                </h2>
                <p class="text-slate-300 text-xs sm:text-sm max-w-3xl leading-relaxed">
                    {{ app()->getLocale() === 'ar'
                        ? 'عدّل أي نص أو عنوان أو شريحة سلايدر أو فيديو أو ركيزة حلول أو إحصائية أو بيانات صفحة من التبويبات السبعة بالأسفل، وستظهر التعديلات فوراً على الموقع العام.'
                        : 'Edit any headline, 4-slide hero carousel, video overview, 5-pillar solutions hub, metrics, timeline, or inner page copy across the 7 tabs below.' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('home') }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-3 bg-white/10 hover:bg-white/20 text-white border border-white/15 rounded-xl text-xs font-bold transition-all">
                    <span>{{ app()->getLocale() === 'ar' ? 'معاينة الموقع الحي' : 'Preview Live Site' }}</span>
                    <span class="font-mono">↗</span>
                </a>
                <button wire:click="save"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-accent-500 to-[#ff6b42] hover:from-accent-600 hover:to-accent-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-glow transition-all cursor-pointer">
                    <span>{{ app()->getLocale() === 'ar' ? 'حفظ ونشر كافة التعديلات' : 'Save & Publish All Changes' }}</span>
                    <span class="font-mono text-sm">✓</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 7-Tab Navigation Bar -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-2 shadow-subtle overflow-x-auto">
        <div class="flex items-center gap-1.5 min-w-max text-xs font-bold">
            @php
                $tabs = [
                    'brand' => ['icon' => '🏛️', 'ar' => '1. الهوية والهيدر والفوتر', 'en' => '1. Brand, Header & Footer'],
                    'hero' => ['icon' => '🎬', 'ar' => '2. السلايدر الرئيسي (4 شرائح)', 'en' => '2. Hero Carousel (4 Slides)'],
                    'overview' => ['icon' => '📽️', 'ar' => '3. فيديو من نحن والركائز', 'en' => '3. Who We Are Film & Pillars'],
                    'solutions' => ['icon' => '⚡', 'ar' => '4. مركز الحلول (5 قدرات)', 'en' => '4. Solutions Hub (5 Pillars)'],
                    'stats' => ['icon' => '📊', 'ar' => '5. الإحصائيات وبانر التوظيف', 'en' => '5. Metrics & Home Sections'],
                    'about' => ['icon' => '🌍', 'ar' => '6. عن الشركة والفروع الدولية', 'en' => '6. About, Timeline & Offices'],
                    'pages' => ['icon' => '📄', 'ar' => '7. إعدادات باقي الصفحات', 'en' => '7. Work, Blog, Careers & Contact'],
                ];
            @endphp

            @foreach($tabs as $key => $tab)
                <button type="button"
                        wire:click="setTab('{{ $key }}')"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl transition-all cursor-pointer {{ $activeTab === $key ? 'bg-ink-900 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100 hover:text-ink-900' }}">
                    <span>{{ $tab['icon'] }}</span>
                    <span>{{ app()->getLocale() === 'ar' ? $tab['ar'] : $tab['en'] }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Main Form -->
    <form wire:submit.prevent="save" class="space-y-6">

        <!-- =================================================================
             TAB 1: BRAND, HEADER, MEGA MENU & FOOTER
             ================================================================= -->
        @if($activeTab === 'brand')
            <div class="space-y-6">
                <!-- General Identity & Logos -->
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-subtle p-6 sm:p-8 space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-accent-50 text-accent-500 flex items-center justify-center font-bold">01</span>
                            <div>
                                <h3 class="text-base font-heading font-extrabold text-ink-900">
                                    {{ app()->getLocale() === 'ar' ? 'الهوية الأساسية والشعارات وشريط الإعلانات' : 'Core Brand Identity, Logos & Top Ticker' }}
                                </h3>
                                <p class="text-xs text-slate-500">{{ app()->getLocale() === 'ar' ? 'تتحكم في عنوان الموقع والشعار في الهيدر والفوتر وشريط الإعلانات المتحرك.' : 'Controls site title, header/footer logos, and the scrolling capabilities ticker.' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ app()->getLocale() === 'ar' ? 'اسم الشركة / الاستوديو' : 'Studio Brand Name' }}</label>
                            <input type="text" wire:model="form.site_name" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl focus:border-accent-500 focus:ring-2 focus:ring-accent-500/15 outline-none" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ app()->getLocale() === 'ar' ? 'الوصف التعريفي (Tagline & SEO Description)' : 'Brand Tagline & Meta Description' }}</label>
                            <input type="text" wire:model="form.site_tagline" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl focus:border-accent-500 focus:ring-2 focus:ring-accent-500/15 outline-none" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ app()->getLocale() === 'ar' ? 'مسار الشعار الداكن (Header Logo)' : 'Header Dark Logo Path' }}</label>
                            <input type="text" wire:model="form.logo_dark" class="w-full px-3.5 py-2.5 text-xs font-mono border border-slate-200 rounded-xl focus:border-accent-500 outline-none" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ app()->getLocale() === 'ar' ? 'مسار الشعار الفاتح (Footer & Preloader Logo)' : 'Footer & Preloader Light Logo Path' }}</label>
                            <input type="text" wire:model="form.logo_light" class="w-full px-3.5 py-2.5 text-xs font-mono border border-slate-200 rounded-xl focus:border-accent-500 outline-none" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ app()->getLocale() === 'ar' ? 'النص الرئيسي في الشريط المتحرك (Main Ticker Announcement)' : 'Primary Ticker Announcement Text' }}</label>
                            <input type="text" wire:model="form.announcement_text" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl focus:border-accent-500 outline-none" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 pt-2">
                        @for($i = 1; $i <= 5; $i++)
                            <div>
                                <label class="block text-[11px] font-mono font-bold text-slate-500 mb-1">Ticker Tag #{{ $i }}</label>
                                <input type="text" wire:model="form.ticker_item_{{ $i }}" class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-xl focus:border-accent-500 outline-none" />
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- Mega Menu & Footer Banner Control -->
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-subtle p-6 sm:p-8 space-y-5">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                        <span class="w-8 h-8 rounded-lg bg-accent-50 text-accent-500 flex items-center justify-center font-bold">02</span>
                        <div>
                            <h3 class="text-base font-heading font-extrabold text-ink-900">
                                {{ app()->getLocale() === 'ar' ? 'القائمة المنسدلة (Mega Menu) وبانر الفوتر والسوشيال ميديا' : 'Mega Menu Copy, Footer Executive Banner & Social Links' }}
                            </h3>
                            <p class="text-xs text-slate-500">{{ app()->getLocale() === 'ar' ? 'تظهر في جميع صفحات الموقع بأعلى وأسفل كل صفحة.' : 'Displayed globally across the site header mega-menu and site footer.' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Mega Menu (Capabilities Title)</label>
                            <input type="text" wire:model="form.mega_cap_title" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Mega Menu (Capabilities Description)</label>
                            <input type="text" wire:model="form.mega_cap_desc" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ app()->getLocale() === 'ar' ? 'عنوان بانر الفوتر (Footer CTA Title)' : 'Footer CTA Banner Title' }}</label>
                            <input type="text" wire:model="form.footer_banner_title" class="w-full px-3.5 py-2.5 text-xs font-bold border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ app()->getLocale() === 'ar' ? 'وصف بانر الفوتر (Footer CTA Subtitle)' : 'Footer CTA Banner Subtitle' }}</label>
                            <input type="text" wire:model="form.footer_banner_sub" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ app()->getLocale() === 'ar' ? 'نص زر الفوتر الأول' : 'Footer Primary CTA Button' }}</label>
                            <input type="text" wire:model="form.footer_cta_primary_text" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ app()->getLocale() === 'ar' ? 'نص زر الفوتر الثاني' : 'Footer Secondary CTA Button' }}</label>
                            <input type="text" wire:model="form.footer_cta_secondary_text" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ app()->getLocale() === 'ar' ? 'نبذة الشركة في الفوتر (Footer Brand Summary)' : 'Footer Brand Summary Paragraph' }}</label>
                            <textarea rows="2" wire:model="form.footer_summary" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ app()->getLocale() === 'ar' ? 'عنوان النشرة البريدية بالفوتر' : 'Footer Newsletter Heading' }}</label>
                            <input type="text" wire:model="form.footer_newsletter_title" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ app()->getLocale() === 'ar' ? 'حقوق الملكية أسفل الفوتر (Copyright)' : 'Footer Copyright Text' }}</label>
                            <input type="text" wire:model="form.footer_copyright" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl" />
                        </div>
                    </div>

                    <!-- Social Links -->
                    <div class="pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-[11px] font-mono font-bold text-slate-600 mb-1">LinkedIn URL</label>
                            <input type="url" wire:model="form.social_linkedin" class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-mono font-bold text-slate-600 mb-1">GitHub URL</label>
                            <input type="url" wire:model="form.social_github" class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-mono font-bold text-slate-600 mb-1">YouTube URL</label>
                            <input type="url" wire:model="form.social_youtube" class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-mono font-bold text-slate-600 mb-1">X (Twitter) URL</label>
                            <input type="url" wire:model="form.social_twitter" class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-mono font-bold text-slate-600 mb-1">Instagram URL</label>
                            <input type="url" wire:model="form.social_instagram" class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- =================================================================
             TAB 2: HOMEPAGE 4-SLIDE INTERACTIVE HERO CAROUSEL
             ================================================================= -->
        @if($activeTab === 'hero')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @php
                    $slides = [
                        1 => ['prefix' => 'hero', 'tab' => 'hero_tab1', 'label_ar' => 'الشريحة الأولى (Overview Slide 01)', 'label_en' => 'Slide 01: Studio Overview'],
                        2 => ['prefix' => 'hero2', 'tab' => 'hero_tab2', 'label_ar' => 'الشريحة الثانية (Marketing Slide 02)', 'label_en' => 'Slide 02: Performance Marketing'],
                        3 => ['prefix' => 'hero3', 'tab' => 'hero_tab3', 'label_ar' => 'الشريحة الثالثة (Case Study Slide 03)', 'label_en' => 'Slide 03: Flagship Case Study'],
                        4 => ['prefix' => 'hero4', 'tab' => 'hero_tab4', 'label_ar' => 'الشريحة الرابعة (Video Sprint Slide 04)', 'label_en' => 'Slide 04: Video Documentary'],
                    ];
                @endphp

                @foreach($slides as $num => $s)
                    @php $p = $s['prefix']; $t = $s['tab']; @endphp
                    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-subtle p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2.5">
                                <span class="px-2.5 py-1 rounded-lg bg-ink-900 text-white font-mono text-xs font-bold">0{{ $num }}</span>
                                <h3 class="text-sm font-heading font-extrabold text-ink-900">
                                    {{ app()->getLocale() === 'ar' ? $s['label_ar'] : $s['label_en'] }}
                                </h3>
                            </div>
                            <span class="text-[10px] font-mono text-accent-500 font-bold uppercase">Hero Carousel</span>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Top Tag Badge</label>
                                <input type="text" wire:model="form.{{ $p }}_tag" class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Kicker / Location</label>
                                <input type="text" wire:model="form.{{ $p }}_kicker" class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">{{ app()->getLocale() === 'ar' ? 'العنوان الرئيسي للشريحة' : 'Slide Main Headline' }}</label>
                            <input type="text" wire:model="form.{{ $p }}_headline" class="w-full px-3.5 py-2.5 text-xs font-bold text-ink-900 border border-slate-200 rounded-xl" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">{{ app()->getLocale() === 'ar' ? 'الوصف التفصيلي للشريحة' : 'Slide Description' }}</label>
                            <textarea rows="2" wire:model="form.{{ $p }}_subheadline" class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl"></textarea>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">{{ app()->getLocale() === 'ar' ? 'مسار صورة خلفية الشريحة' : 'Background Image Path' }}</label>
                            <input type="text" wire:model="form.{{ $p }}_bg" class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Primary Button Text</label>
                                <input type="text" wire:model="form.{{ $p }}_cta_primary_text" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Primary Button URL</label>
                                <input type="text" wire:model="form.{{ $p }}_cta_primary_url" class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Secondary Button Text</label>
                                <input type="text" wire:model="form.{{ $p }}_cta_secondary_text" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Secondary Button URL</label>
                                <input type="text" wire:model="form.{{ $p }}_cta_secondary_url" class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                            </div>
                        </div>

                        <!-- Bottom Navigation Dock Tab Labels -->
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/70 space-y-2">
                            <span class="block text-[10px] font-mono font-bold uppercase text-slate-400">Bottom Slider Dock Tab #{{ $num }}</span>
                            <div class="grid grid-cols-3 gap-2">
                                <input type="text" wire:model="form.{{ $t }}_num" placeholder="Category" class="px-2.5 py-1.5 text-xs font-mono border border-slate-200 rounded-lg bg-white" />
                                <input type="text" wire:model="form.{{ $t }}_title" placeholder="Tab Title" class="px-2.5 py-1.5 text-xs font-bold border border-slate-200 rounded-lg bg-white" />
                                <input type="text" wire:model="form.{{ $t }}_sub" placeholder="Tab Subtitle" class="px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg bg-white" />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- =================================================================
             TAB 3: WHO WE ARE OVERVIEW FILM & 3 CORE PILLARS
             ================================================================= -->
        @if($activeTab === 'overview')
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-subtle p-6 sm:p-8 space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-base font-heading font-extrabold text-ink-900">
                        {{ app()->getLocale() === 'ar' ? 'قسم فيديو تعريف الشركة (Who We Are Film) والركائز الثلاث' : 'Company Overview Film ("Who We Are") & 3 Core Pillars' }}
                    </h3>
                    <p class="text-xs text-slate-500">{{ app()->getLocale() === 'ar' ? 'يظهر في الصفحة الرئيسية وصفحة عن الشركة (About).' : 'Rendered on both the Homepage and the About page.' }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ app()->getLocale() === 'ar' ? 'عنوان القسم' : 'Section Heading' }}</label>
                        <input type="text" wire:model="form.overview_title" class="w-full px-3.5 py-2.5 text-xs font-bold border border-slate-200 rounded-xl" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ app()->getLocale() === 'ar' ? 'مسار ملف الفيديو (MP4 Video Path)' : 'Video MP4 File Path / URL' }}</label>
                        <input type="text" wire:model="form.video_reel_url" class="w-full px-3.5 py-2.5 text-xs font-mono border border-slate-200 rounded-xl" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ app()->getLocale() === 'ar' ? 'النص التقديمي أعلى الفيديو' : 'Overview Lead Paragraph' }}</label>
                        <textarea rows="2" wire:model="form.overview_lead" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl"></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ app()->getLocale() === 'ar' ? 'صورة غلاف الفيديو (Video Poster Image)' : 'Video Poster Cover Image Path' }}</label>
                        <input type="text" wire:model="form.video_poster_url" class="w-full px-3.5 py-2.5 text-xs font-mono border border-slate-200 rounded-xl" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-4 border-t border-slate-100">
                    @for($i = 1; $i <= 3; $i++)
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                            <span class="text-[10px] font-mono font-bold uppercase text-accent-500">Core Pillar 0{{ $i }}</span>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Pillar Title</label>
                                <input type="text" wire:model="form.pillar{{ $i }}_title" class="w-full px-3 py-2 text-xs font-bold border border-slate-200 rounded-lg bg-white" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Pillar Description</label>
                                <textarea rows="3" wire:model="form.pillar{{ $i }}_desc" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg bg-white"></textarea>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        @endif

        <!-- =================================================================
             TAB 4: INTERACTIVE SOLUTIONS & CAPABILITIES HUB (5 PILLARS)
             ================================================================= -->
        @if($activeTab === 'solutions')
            <div class="space-y-6">
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-subtle p-6 space-y-4">
                    <h3 class="text-base font-heading font-extrabold text-ink-900">
                        {{ app()->getLocale() === 'ar' ? 'عنوان مركز الحلول والقدرات في الصفحة الرئيسية' : 'Solutions & Capabilities Hub Header' }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Section Title</label>
                            <input type="text" wire:model="form.solutions_heading" class="w-full px-3.5 py-2 text-xs font-bold border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Section Lead</label>
                            <input type="text" wire:model="form.solutions_lead" class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Action Link Text</label>
                            <input type="text" wire:model="form.solutions_cta_text" class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    @for($i = 1; $i <= 5; $i++)
                        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-subtle p-6 space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <div class="flex items-center gap-3">
                                    <span class="px-2.5 py-1 rounded-lg bg-accent-500 text-white font-mono text-xs font-bold">Pillar 0{{ $i }}</span>
                                    <h4 class="font-heading font-extrabold text-ink-900 text-sm">{{ $form["sol{$i}_tab_title"] ?? "Solution Pillar {$i}" }}</h4>
                                </div>
                                <span class="text-xs font-mono text-slate-400">{{ $form["sol{$i}_tab_tag"] ?? '' }}</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Selector Tab Title</label>
                                    <input type="text" wire:model="form.sol{{ $i }}_tab_title" class="w-full px-3 py-2 text-xs font-bold border border-slate-200 rounded-xl" />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Selector Tab Tag</label>
                                    <input type="text" wire:model="form.sol{{ $i }}_tab_tag" class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Panel Kicker</label>
                                    <input type="text" wire:model="form.sol{{ $i }}_kicker" class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Panel Headline</label>
                                        <input type="text" wire:model="form.sol{{ $i }}_title" class="w-full px-3.5 py-2 text-xs font-bold border border-slate-200 rounded-xl" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Panel Description</label>
                                        <textarea rows="3" wire:model="form.sol{{ $i }}_desc" class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Deliverables List (One item per line)</label>
                                        <textarea rows="4" wire:model="form.sol{{ $i }}_bullets" class="w-full px-3.5 py-2 text-xs font-mono border border-slate-200 rounded-xl"></textarea>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Panel Image Path</label>
                                        <input type="text" wire:model="form.sol{{ $i }}_image" class="w-full px-3.5 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Metric Badge Value</label>
                                            <input type="text" wire:model="form.sol{{ $i }}_metric_num" class="w-full px-3.5 py-2 text-sm font-mono font-bold text-accent-500 border border-slate-200 rounded-xl" />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">CTA Button Label</label>
                                            <input type="text" wire:model="form.sol{{ $i }}_cta_text" class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl" />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Metric Badge Explanation</label>
                                        <input type="text" wire:model="form.sol{{ $i }}_metric_text" class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        @endif

        <!-- =================================================================
             TAB 5: IMPACT STATS, SECTION HEADINGS & CAREERS BANNER
             ================================================================= -->
        @if($activeTab === 'stats')
            <div class="space-y-6">
                <!-- 4 Key Impact Counters -->
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-subtle p-6 sm:p-8 space-y-5">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="text-base font-heading font-extrabold text-ink-900">
                            {{ app()->getLocale() === 'ar' ? 'أرقام وإحصائيات الإنجاز (Impact Metric Counters)' : 'Measurable Impact Counters (4 Key Metrics)' }}
                        </h3>
                        <p class="text-xs text-slate-500">{{ app()->getLocale() === 'ar' ? 'الأرقام الكبيرة ووصف كل رقم في القسم الداكن بالصفحة الرئيسية.' : 'Controls the 4 animated counters and their labels on the Homepage.' }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Impact Section Title</label>
                        <input type="text" wire:model="form.home_impact_heading" class="w-full px-3.5 py-2.5 text-xs font-bold border border-slate-200 rounded-xl" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <label class="block text-[10px] font-mono font-bold uppercase text-accent-500">Metric 01 (Shipped)</label>
                            <input type="text" wire:model="form.stats_delivered" class="w-full px-3 py-2 text-lg font-mono font-black text-ink-900 border border-slate-200 rounded-lg bg-white" />
                            <input type="text" wire:model="form.stats_delivered_label" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg bg-white" />
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <label class="block text-[10px] font-mono font-bold uppercase text-accent-500">Metric 02 (ROAS / Revenue)</label>
                            <input type="text" wire:model="form.stats_ad_spend" class="w-full px-3 py-2 text-lg font-mono font-black text-accent-500 border border-slate-200 rounded-lg bg-white" />
                            <input type="text" wire:model="form.stats_ad_spend_label" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg bg-white" />
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <label class="block text-[10px] font-mono font-bold uppercase text-accent-500">Metric 03 (Uptime SLA)</label>
                            <input type="text" wire:model="form.stats_uptime" class="w-full px-3 py-2 text-lg font-mono font-black text-ink-900 border border-slate-200 rounded-lg bg-white" />
                            <input type="text" wire:model="form.stats_uptime_label" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg bg-white" />
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <label class="block text-[10px] font-mono font-bold uppercase text-accent-500">Metric 04 (Client Rating)</label>
                            <input type="text" wire:model="form.stats_rating" class="w-full px-3 py-2 text-lg font-mono font-black text-ink-900 border border-slate-200 rounded-lg bg-white" />
                            <input type="text" wire:model="form.stats_rating_label" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg bg-white" />
                        </div>
                    </div>
                </div>

                <!-- Homepage Section Headings & Careers CTA Band -->
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-subtle p-6 sm:p-8 space-y-5">
                    <h3 class="text-base font-heading font-extrabold text-ink-900 border-b border-slate-100 pb-3">
                        {{ app()->getLocale() === 'ar' ? 'عناوين أقسام الصفحة الرئيسية وبانر التوظيف السفلي' : 'Homepage Section Titles & Careers CTA Banner' }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Case Studies Section Heading</label>
                            <input type="text" wire:model="form.home_work_heading" class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Partners Section Heading</label>
                            <input type="text" wire:model="form.home_partners_heading" class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Founders Section Title</label>
                            <input type="text" wire:model="form.home_founders_heading" class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Founders Section Lead</label>
                            <input type="text" wire:model="form.home_founders_lead" class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Testimonials Section Heading</label>
                            <input type="text" wire:model="form.home_testimonials_heading" class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Blog / Journal Section Heading</label>
                            <input type="text" wire:model="form.home_blog_heading" class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl" />
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Careers CTA Banner Eyebrow</label>
                            <input type="text" wire:model="form.home_careers_eyebrow" class="w-full px-3.5 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Careers CTA Button Text</label>
                            <input type="text" wire:model="form.home_careers_btn" class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Careers CTA Banner Heading</label>
                            <input type="text" wire:model="form.home_careers_title" class="w-full px-3.5 py-2 text-xs font-bold border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Careers CTA Banner Description</label>
                            <input type="text" wire:model="form.home_careers_desc" class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl" />
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- =================================================================
             TAB 6: ABOUT PAGE, PRINCIPLES, TIMELINE & GLOBAL OFFICES
             ================================================================= -->
        @if($activeTab === 'about')
            <div class="space-y-6">
                <!-- About Hero & Global Offices -->
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-subtle p-6 sm:p-8 space-y-5">
                    <h3 class="text-base font-heading font-extrabold text-ink-900 border-b border-slate-100 pb-3">
                        {{ app()->getLocale() === 'ar' ? 'واجهة صفحة عن الشركة (About Hero) والفروع الدولية' : 'About Page Hero & Global Offices (Cairo, Riyadh, London)' }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">About Hero Eyebrow</label>
                            <input type="text" wire:model="form.about_hero_eyebrow" class="w-full px-3.5 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">About Hero Aside Badge</label>
                            <input type="text" wire:model="form.about_hero_badge" class="w-full px-3.5 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">About Hero Main Title</label>
                            <input type="text" wire:model="form.about_hero_title" class="w-full px-3.5 py-2.5 text-xs font-bold border border-slate-200 rounded-xl" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">About Hero Lead Paragraph</label>
                            <textarea rows="2" wire:model="form.about_hero_lead" class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl"></textarea>
                        </div>
                    </div>

                    <!-- Global Contact & Offices -->
                    <div class="pt-4 border-t border-slate-100 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني الرسمي' : 'Official Email' }}</label>
                            <input type="email" wire:model="form.contact_email" class="w-full px-3.5 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">{{ app()->getLocale() === 'ar' ? 'رقم الهاتف الرسمي' : 'Official Phone' }}</label>
                            <input type="text" wire:model="form.contact_phone" class="w-full px-3.5 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">{{ app()->getLocale() === 'ar' ? 'عنوان الفوتر الرئيسي' : 'Primary HQ Footer Address' }}</label>
                            <input type="text" wire:model="form.contact_address" class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Cairo HQ Address</label>
                            <input type="text" wire:model="form.office_cairo" class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Riyadh KSA Office Address</label>
                            <input type="text" wire:model="form.office_riyadh" class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">London UK Office Address</label>
                            <input type="text" wire:model="form.office_london" class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl" />
                        </div>
                    </div>
                </div>

                <!-- 4 Operating Principles & 4 Timeline Milestones -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-subtle p-6 space-y-4">
                        <h3 class="text-sm font-heading font-extrabold text-ink-900 border-b border-slate-100 pb-3">
                            {{ app()->getLocale() === 'ar' ? 'المبادئ التشغيلية الأربعة (4 Operating Principles)' : '4 Operating Principles (About Page)' }}
                        </h3>
                        <input type="text" wire:model="form.about_principles_heading" class="w-full px-3.5 py-2 text-xs font-bold border border-slate-200 rounded-xl" />

                        @for($i = 1; $i <= 4; $i++)
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80 space-y-2">
                                <span class="text-[10px] font-mono font-bold text-accent-500">Principle 0{{ $i }}</span>
                                <input type="text" wire:model="form.about_p{{ $i }}_title" class="w-full px-3 py-1.5 text-xs font-bold border border-slate-200 rounded-lg bg-white" />
                                <textarea rows="2" wire:model="form.about_p{{ $i }}_text" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg bg-white"></textarea>
                            </div>
                        @endfor
                    </div>

                    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-subtle p-6 space-y-4">
                        <h3 class="text-sm font-heading font-extrabold text-ink-900 border-b border-slate-100 pb-3">
                            {{ app()->getLocale() === 'ar' ? 'المسيرة الزمنية للشركة (Studio Timeline Milestones)' : 'Studio Timeline Milestones (About Page)' }}
                        </h3>
                        <input type="text" wire:model="form.about_timeline_heading" class="w-full px-3.5 py-2 text-xs font-bold border border-slate-200 rounded-xl" />

                        @for($i = 1; $i <= 4; $i++)
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80 space-y-2">
                                <div class="grid grid-cols-4 gap-2">
                                    <input type="text" wire:model="form.about_t{{ $i }}_year" class="px-2.5 py-1.5 text-xs font-mono font-bold text-accent-500 border border-slate-200 rounded-lg bg-white" />
                                    <input type="text" wire:model="form.about_t{{ $i }}_title" class="col-span-3 px-3 py-1.5 text-xs font-bold border border-slate-200 rounded-lg bg-white" />
                                </div>
                                <textarea rows="2" wire:model="form.about_t{{ $i }}_text" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg bg-white"></textarea>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        @endif

        <!-- =================================================================
             TAB 7: INNER PAGES (WORK, BLOG, TEAM, CAREERS, PARTNERS, CONTACT)
             ================================================================= -->
        @if($activeTab === 'pages')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Work & Blog Heroes -->
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-subtle p-6 space-y-4">
                    <h3 class="text-sm font-heading font-extrabold text-ink-900 border-b border-slate-100 pb-3">
                        {{ app()->getLocale() === 'ar' ? 'صفحة الأعمال (Work) وصفحة المدونة (Blog)' : 'Work Page & Blog Page Headers' }}
                    </h3>
                    <div class="space-y-3">
                        <span class="text-[10px] font-mono font-bold uppercase text-accent-500">Work Page (/work)</span>
                        <input type="text" wire:model="form.work_hero_eyebrow" class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                        <input type="text" wire:model="form.work_hero_title" class="w-full px-3 py-2 text-xs font-bold border border-slate-200 rounded-xl" />
                        <textarea rows="2" wire:model="form.work_hero_lead" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl"></textarea>
                    </div>
                    <div class="space-y-3 pt-3 border-t border-slate-100">
                        <span class="text-[10px] font-mono font-bold uppercase text-accent-500">Blog Page (/blog)</span>
                        <input type="text" wire:model="form.blog_hero_eyebrow" class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                        <input type="text" wire:model="form.blog_hero_title" class="w-full px-3 py-2 text-xs font-bold border border-slate-200 rounded-xl" />
                        <textarea rows="2" wire:model="form.blog_hero_lead" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl"></textarea>
                    </div>
                </div>

                <!-- Team & Partners Pages -->
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-subtle p-6 space-y-4">
                    <h3 class="text-sm font-heading font-extrabold text-ink-900 border-b border-slate-100 pb-3">
                        {{ app()->getLocale() === 'ar' ? 'صفحة الفريق (Team) وصفحة الشركاء (Partners)' : 'Team Page & Partners Page Headers' }}
                    </h3>
                    <div class="space-y-3">
                        <span class="text-[10px] font-mono font-bold uppercase text-accent-500">Team Page (/team)</span>
                        <input type="text" wire:model="form.team_hero_title" class="w-full px-3 py-2 text-xs font-bold border border-slate-200 rounded-xl" />
                        <textarea rows="2" wire:model="form.team_hero_lead" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl"></textarea>
                        <input type="text" wire:model="form.team_hero_photo" class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                    </div>
                    <div class="space-y-3 pt-3 border-t border-slate-100">
                        <span class="text-[10px] font-mono font-bold uppercase text-accent-500">Partners Page (/partners)</span>
                        <input type="text" wire:model="form.partners_hero_eyebrow" class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                        <input type="text" wire:model="form.partners_hero_title" class="w-full px-3 py-2 text-xs font-bold border border-slate-200 rounded-xl" />
                        <textarea rows="2" wire:model="form.partners_hero_lead" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl"></textarea>
                    </div>
                </div>

                <!-- Careers Portal & Benefits -->
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-subtle p-6 space-y-4">
                    <h3 class="text-sm font-heading font-extrabold text-ink-900 border-b border-slate-100 pb-3">
                        {{ app()->getLocale() === 'ar' ? 'بوابة التوظيف والمزايا (Careers Portal & Perks)' : 'Careers Portal Hero & Benefits Cards (/careers)' }}
                    </h3>
                    <div class="space-y-3">
                        <input type="text" wire:model="form.careers_hero_eyebrow" class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                        <input type="text" wire:model="form.careers_hero_title" class="w-full px-3 py-2 text-xs font-bold border border-slate-200 rounded-xl" />
                        <textarea rows="2" wire:model="form.careers_hero_lead" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl"></textarea>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-mono font-bold text-slate-500 mb-1">Offer Turnaround Stat</label>
                                <input type="text" wire:model="form.careers_stat_turnaround" class="w-full px-3 py-1.5 text-xs font-mono font-bold border border-slate-200 rounded-lg" />
                            </div>
                            <div>
                                <label class="block text-[10px] font-mono font-bold text-slate-500 mb-1">Paid Annual Leave Stat</label>
                                <input type="text" wire:model="form.careers_stat_leave" class="w-full px-3 py-1.5 text-xs font-mono font-bold border border-slate-200 rounded-lg" />
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3 border-t border-slate-100">
                        @for($i = 1; $i <= 4; $i++)
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/70 space-y-1.5">
                                <input type="text" wire:model="form.careers_ben{{ $i }}_title" class="w-full px-2.5 py-1 text-xs font-bold border border-slate-200 rounded-lg bg-white" />
                                <textarea rows="2" wire:model="form.careers_ben{{ $i }}_desc" class="w-full px-2.5 py-1 text-[11px] border border-slate-200 rounded-lg bg-white"></textarea>
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- Contact Page & Desks -->
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-subtle p-6 space-y-4">
                    <h3 class="text-sm font-heading font-extrabold text-ink-900 border-b border-slate-100 pb-3">
                        {{ app()->getLocale() === 'ar' ? 'صفحة تواصل معنا ومكاتب الأقسام (Contact Us)' : 'Contact Page Hero & Practice Desks (/contact)' }}
                    </h3>
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <input type="text" wire:model="form.contact_hero_eyebrow" class="px-3 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                            <input type="text" wire:model="form.contact_hero_badge" class="px-3 py-2 text-xs font-mono border border-slate-200 rounded-xl" />
                        </div>
                        <input type="text" wire:model="form.contact_hero_title" class="w-full px-3 py-2 text-xs font-bold border border-slate-200 rounded-xl" />
                        <textarea rows="2" wire:model="form.contact_hero_lead" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl"></textarea>
                    </div>
                    <div class="space-y-3 pt-3 border-t border-slate-100">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/70 space-y-1.5">
                            <div class="grid grid-cols-2 gap-2">
                                <input type="text" wire:model="form.contact_desk1_title" class="px-2.5 py-1 text-xs font-bold border border-slate-200 rounded-lg bg-white" />
                                <input type="text" wire:model="form.contact_desk1_email" class="px-2.5 py-1 text-xs font-mono border border-slate-200 rounded-lg bg-white" />
                            </div>
                            <input type="text" wire:model="form.contact_desk1_desc" class="w-full px-2.5 py-1 text-[11px] border border-slate-200 rounded-lg bg-white" />
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/70 space-y-1.5">
                            <div class="grid grid-cols-2 gap-2">
                                <input type="text" wire:model="form.contact_desk2_title" class="px-2.5 py-1 text-xs font-bold border border-slate-200 rounded-lg bg-white" />
                                <input type="text" wire:model="form.contact_desk2_email" class="px-2.5 py-1 text-xs font-mono border border-slate-200 rounded-lg bg-white" />
                            </div>
                            <input type="text" wire:model="form.contact_desk2_desc" class="w-full px-2.5 py-1 text-[11px] border border-slate-200 rounded-lg bg-white" />
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Floating / Bottom Sticky Save Bar -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-card flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>{{ app()->getLocale() === 'ar' ? 'جميع التغييرات تنعكس مباشرة على الموقع العام بدون إعادة تشغيل.' : 'All changes reflect immediately on the live public website.' }}</span>
            </div>
            <button type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-accent-500 to-[#ff6b42] hover:from-accent-600 hover:to-accent-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-glow transition-all cursor-pointer">
                {{ app()->getLocale() === 'ar' ? 'حفظ وتحديث الموقع الحي الآن ✓' : 'Save & Publish to Live Website ✓' }}
            </button>
        </div>
    </form>
</div>
