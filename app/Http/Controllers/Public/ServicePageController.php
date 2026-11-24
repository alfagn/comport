<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\ServicePageService;

class ServicePageController extends Controller
{
    public function __construct(private ServicePageService $pages) {}

    public function index()
    {
        return view('pages.service-index', ['pages' => $this->pages->published('en'), 'locale' => 'en']);
    }

    public function show(string $slug)
    {
        $page = $this->pages->publicPage($slug, 'en');

        return view('pages.service-detail', ['page' => $page, 'locale' => 'en']);
    }
}
