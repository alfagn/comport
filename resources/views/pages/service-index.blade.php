@extends('layouts.service')
@section('title', 'Services | Empetix')
@section('description', 'Explore published services and discuss your project requirements.')
@section('content')
<section class="service-hero"><div class="wrap"><span class="eyebrow">EMPETIX</span><h1>Start with what your project needs.</h1><p class="lead">A clear scope before execution.</p></div></section>
<section class="service-cards wrap">
@forelse($pages as $edition)<a class="service-card" href="{{ seo_url($edition->publicPath()) }}"><span class="eyebrow">EXPLORE SERVICE ↗</span><h2>{{ $edition->title }}</h2><p>{{ $edition->summary }}</p></a>
@empty<p>No service pages are published yet. Contact us to discuss your project.</p>@endforelse
</section>
@endsection
