<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\HeroSlideData;
use App\DataTables\HeroSlideDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HeroSlideRequest;
use App\Services\HeroSlideService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;

/**
 * Dashboard CRUD for HeroSlide.
 *
 * Thin controller: the FormRequest validates, the service does the work,
 * the controller only moves data between them and picks the next screen.
 */
class HeroSlideController extends Controller
{
    public function __construct(private HeroSlideService $service) {}

    /** List records. */
    public function index(HeroSlideDataTable $dataTable): View|JsonResponse
    {
        return $dataTable->handle('admin.hero-slides.index');
    }

    /** Show the create form. */
    public function create(): View
    {
        return view('admin.hero-slides.form', ['item' => null]);
    }

    /** Store a new record. */
    public function store(HeroSlideRequest $request): RedirectResponse
    {
        $this->service->create(HeroSlideData::fromArray($request->validated()), $request->file('image'));

        return redirect()->route('admin.hero-slides.index')->with('success', 'HeroSlide created.');
    }

    /** Show the edit form. */
    public function edit(int $id): View
    {
        return view('admin.hero-slides.form', ['item' => $this->service->findForEdit($id)]);
    }

    /** Update a record. */
    public function update(HeroSlideRequest $request, int $id): RedirectResponse
    {
        $this->service->update($id, HeroSlideData::fromArray($request->validated()), $request->file('image'));

        return redirect()->route('admin.hero-slides.index')->with('success', 'HeroSlide updated.');
    }

    /** Delete a record. */
    public function destroy(int $id): RedirectResponse
    {
        $this->service->delete($id);

        return redirect()->route('admin.hero-slides.index')->with('success', 'HeroSlide deleted.');
    }
}
