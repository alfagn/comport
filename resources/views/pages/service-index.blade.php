@extends('layouts.service')
@section('title', ($locale === 'ar' ? 'الخدمات' : 'Services').' | Empetix')
@section('description', $locale === 'ar' ? 'تعرف على الخدمات المنشورة وتواصل لمناقشة متطلبات مشروعك.' : 'Explore published services and discuss your project requirements.')
@section('content')
<section class="service-hero"><div class="wrap"><span class="eyebrow">EMPETIX</span><h1>{{ $locale === 'ar' ? 'ابدأ بما يحتاجه مشروعك.' : 'Start with what your project needs.' }}</h1><p class="lead">{{ $locale === 'ar' ? 'نطاق واضح قبل التنفيذ.' : 'A clear scope before execution.' }}</p></div></section>
<section class="service-cards wrap">
@forelse($pages as $edition)<a class="service-card" href="{{ seo_url($edition->publicPath()) }}"><span class="eyebrow">{{ $locale === 'ar' ? 'تعرف على الخدمة' : 'EXPLORE SERVICE' }} ↗</span><h2>{{ $edition->title }}</h2><p>{{ $edition->summary }}</p></a>
@empty<p>{{ $locale === 'ar' ? 'لا توجد صفحات خدمات منشورة بهذه اللغة بعد. تواصل معنا لمناقشة مشروعك.' : 'No service pages are published in this language yet. Contact us to discuss your project.' }}</p>@endforelse
</section>
@endsection
