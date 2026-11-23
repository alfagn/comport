<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\PartnerData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PartnerRequest;
use App\Services\PartnerService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;

/**
 * Dashboard CRUD for Partner.
 *
 * Thin controller: the FormRequest validates, the service does the work,
 * the controller only moves data between them and picks the next screen.
 */
class PartnerController extends Controller
{
    public function __construct(private PartnerService $service) {}

    /** List records. */
    public function index(): View
    {
        return view('admin.partners.index', ['items' => $this->service->paginateForAdmin()]);
    }

    /** Show the create form. */
    public function create(): View
    {
        return view('admin.partners.form', ['item' => null]);
    }

    /** Store a new record. */
    public function store(PartnerRequest $request): RedirectResponse
    {
        $this->service->create(PartnerData::fromArray($request->validated()), $request->file('logo'));

        return redirect()->route('admin.partners.index')->with('success', 'Partner created.');
    }

    /** Show the edit form. */
    public function edit(int $id): View
    {
        return view('admin.partners.form', ['item' => $this->service->findForEdit($id)]);
    }

    /** Update a record. */
    public function update(PartnerRequest $request, int $id): RedirectResponse
    {
        $this->service->update($id, PartnerData::fromArray($request->validated()), $request->file('logo'));

        return redirect()->route('admin.partners.index')->with('success', 'Partner updated.');
    }

    /** Delete a record. */
    public function destroy(int $id): RedirectResponse
    {
        $this->service->delete($id);

        return redirect()->route('admin.partners.index')->with('success', 'Partner deleted.');
    }
}
