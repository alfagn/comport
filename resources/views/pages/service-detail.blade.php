@extends('layouts.service')
@section('title', $page->meta_title ?? $page->title.' | Empetix')
@section('description', $page->meta_description ?? $page->summary)
@section('content')
<section class="service-hero"><div class="wrap hero-grid"><div>
  <a class="eyebrow" href="{{ $locale === 'ar' ? url('/ar/services') : url('/services') }}">{{ $locale === 'ar' ? 'الخدمات / Empetix' : 'EMPETIX / SERVICES' }}</a>
  <h1>{{ $page->title }}</h1><p class="lead">{{ $page->summary }}</p>
  <a class="button" href="{{ url('/contact') }}">{{ $locale === 'ar' ? 'ناقش مشروعك' : 'Discuss your project' }} <span aria-hidden="true">↗</span></a>
</div><div class="hero-art" aria-hidden="true"><div class="orbit"></div><div class="art-card"><span>&lt;/&gt;</span><div class="art-line"></div><div class="art-line short"></div><div class="art-square">↗</div></div><span class="art-label">EMPETIX</span></div></div></section>
<section class="service-editorial wrap" aria-label="{{ $locale === 'ar' ? 'تفاصيل الخدمة' : 'Service details' }}">
  <aside><span class="eyebrow">{{ $locale === 'ar' ? 'من الفكرة إلى نطاق واضح' : 'FROM IDEA TO CLEAR SCOPE' }}</span><h2>{{ $locale === 'ar' ? 'أسئلة أفضل. بداية أوضح.' : 'Better questions. A clearer starting point.' }}</h2><p>{{ $locale === 'ar' ? 'النطاق والمدة والتكلفة تتحدد بعد فهم المتطلبات، وليس من وعود عامة.' : 'Scope, timing and cost depend on the requirements, not on generic promises.' }}</p></aside>
  <div class="editorial-body">
  @foreach(preg_split('/\R{2,}/u', $page->body) as $i => $section)
    @php($lines = preg_split('/\R/u', trim($section), 2))
    @if(trim($section) !== '')<article><span class="section-num">{{ str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) }}</span><h2>{{ $lines[0] }}</h2>@if(isset($lines[1]))<p>{{ $lines[1] }}</p>@endif</article>@endif
  @endforeach
  </div>
</section>
<section class="service-contact"><div class="wrap"><span class="eyebrow">{{ $locale === 'ar' ? 'الخطوة التالية' : 'NEXT STEP' }}</span><h2>{{ $locale === 'ar' ? 'ما الذي تريد بناءه أو تحسينه؟' : 'What do you want to build or improve?' }}</h2><p>{{ $locale === 'ar' ? 'شارك الفكرة والأولويات والأنظمة الحالية لبدء النقاش.' : 'Share the idea, priorities and existing systems to start the discussion.' }}</p><a class="button dark" href="{{ url('/contact') }}">{{ $locale === 'ar' ? 'أرسل تفاصيل مشروعك' : 'Send a project brief' }} ↗</a></div></section>
@endsection
