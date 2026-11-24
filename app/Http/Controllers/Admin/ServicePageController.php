<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\ServicePageData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServicePageRequest;
use App\Models\ServicePage;
use App\Services\ServicePageService;

class ServicePageController extends Controller
{
    public function __construct(private ServicePageService $pages) {}

    public function index()
    {
        return view('admin.service-pages.index', ['pages' => $this->pages->all()]);
    }

    public function create()
    {
        return view('admin.service-pages.create', [
            'item' => new ServicePage(['locale' => 'en', 'is_published' => false]),
        ]);
    }

    public function store(ServicePageRequest $request)
    {
        $page = $this->pages->create(ServicePageData::fromArray($request->validated()));

        return redirect()
            ->route('admin.service-pages.index')
            ->with('success', "Service page \"{$page->title}\" created.");
    }

    public function edit(int $id)
    {
        return view('admin.service-pages.edit', ['item' => $this->pages->find($id)]);
    }

    public function update(ServicePageRequest $request, int $id)
    {
        $this->pages->save($id, ServicePageData::fromArray($request->validated()));

        return back()->with('success', 'Service page saved.');
    }

    public function destroy(int $id)
    {
        $this->pages->delete($id);

        return redirect()
            ->route('admin.service-pages.index')
            ->with('success', 'Service page deleted.');
    }

    public function preview(int $id)
    {
        $page = $this->pages->find($id);

        return response()
            ->view('pages.service-detail', [
                'page' => $page,
                'locale' => $page->locale,
                'alternates' => collect(),
                'preview' => true,
            ])
            ->header('X-Robots-Tag', 'noindex, nofollow')
            ->header('Cache-Control', 'private, no-store');
    }
}
