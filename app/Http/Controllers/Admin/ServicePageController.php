<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\ServicePageData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServicePageRequest;
use App\Services\ServicePageService;

class ServicePageController extends Controller
{
    public function __construct(private ServicePageService $pages) {}

    public function index()
    {
        return view('admin.service-pages.index', ['pages' => $this->pages->all()]);
    }

    public function edit(int $id)
    {
        return view('admin.service-pages.edit', ['item' => $this->pages->find($id)]);
    }

    public function update(ServicePageRequest $request, int $id)
    {
        $this->pages->save($id, ServicePageData::fromArray($request->validated()));

        return back()->with('success', 'English service page saved. Publication affects this installation only.');
    }

    public function preview(int $id)
    {
        $page = $this->pages->find($id);

        return response()->view('pages.service-detail', ['page' => $page, 'locale' => $page->locale, 'alternates' => collect(), 'preview' => true])->header('X-Robots-Tag', 'noindex, nofollow')->header('Cache-Control', 'private, no-store');
    }
}
