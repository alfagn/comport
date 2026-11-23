<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\ServiceData;
use App\DataTables\ServiceDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Services\ServiceService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;

/**
 * Dashboard CRUD for Service.
 *
 * Thin controller: the FormRequest validates, the service does the work,
 * the controller only moves data between them and picks the next screen.
 */
class ServiceController extends Controller
{
    public function __construct(private ServiceService $service) {}

    /** List records. */
    public function index(ServiceDataTable $dataTable): View|JsonResponse
    {
        return $dataTable->handle('admin.services.index');
    }

    /** Show the create form. */
    public function create(): View
    {
        return view('admin.services.form', ['item' => null]);
    }

    /** Store a new record. */
    public function store(ServiceRequest $request): RedirectResponse
    {
        $this->service->create(ServiceData::fromArray($request->validated()), $request->file('image'));

        return redirect()->route('admin.services.index')->with('success', 'Service created.');
    }

    /** Show the edit form. */
    public function edit(int $id): View
    {
        return view('admin.services.form', ['item' => $this->service->findForEdit($id)]);
    }

    /** Update a record. */
    public function update(ServiceRequest $request, int $id): RedirectResponse
    {
        $this->service->update($id, ServiceData::fromArray($request->validated()), $request->file('image'));

        return redirect()->route('admin.services.index')->with('success', 'Service updated.');
    }

    /** Delete a record. */
    public function destroy(int $id): RedirectResponse
    {
        $this->service->delete($id);

        return redirect()->route('admin.services.index')->with('success', 'Service deleted.');
    }
}
