<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\ServicePageService;

class ServicePageController extends Controller
{
    public function __construct(private ServicePageService $pages) {}

    public function index(string $locale = 'en')
    {
        return view('pages.service-index', ['pages' => $this->pages->published($locale), 'locale' => $locale]);
    }

    public function show(string $slug, string $locale = 'en')
    {
        $page = $this->pages->publicPage($slug, $locale);
        $alternates = collect(['en', 'ar'])->flatMap(fn ($lang) => $this->pages->published($lang)->where('slug', $slug));

        return view('pages.service-detail', compact('page', 'locale', 'alternates'));
    }
}
