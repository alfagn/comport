<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\IndustryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndustryRequest;
use App\Services\IndustryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;

/**
 * Dashboard CRUD for Industry.
 *
 * Thin controller: the FormRequest validates, the service does the work,
 * the controller only moves data between them and picks the next screen.
 */
class IndustryController extends Controller
{
    public function __construct(private IndustryService $service) {}

    /** List records. */
    public function index(): View
    {
        return view('admin.industries.index', ['items' => $this->service->paginateForAdmin()]);
    }

    /** Show the create form. */
    public function create(): View
    {
        return view('admin.industries.form', ['item' => null]);
    }

    /** Store a new record. */
    public function store(IndustryRequest $request): RedirectResponse
    {
        $this->service->create(IndustryData::fromArray($request->validated()));

        return redirect()->route('admin.industries.index')->with('success', 'Industry created.');
    }

    /** Show the edit form. */
    public function edit(int $id): View
    {
        return view('admin.industries.form', ['item' => $this->service->findForEdit($id)]);
    }

    /** Update a record. */
    public function update(IndustryRequest $request, int $id): RedirectResponse
    {
        $this->service->update($id, IndustryData::fromArray($request->validated()));

        return redirect()->route('admin.industries.index')->with('success', 'Industry updated.');
    }

    /** Delete a record. */
    public function destroy(int $id): RedirectResponse
    {
        $this->service->delete($id);

        return redirect()->route('admin.industries.index')->with('success', 'Industry deleted.');
    }
}
