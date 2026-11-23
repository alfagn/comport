<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\NavItemData;
use App\DataTables\NavItemDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NavItemRequest;
use App\Services\NavItemService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

/**
 * Dashboard CRUD for navigation links.
 *
 * Thin controller: the FormRequest validates, the service does the work,
 * the controller only moves data between them and picks the next screen.
 */
class NavItemController extends Controller
{
    public function __construct(private NavItemService $service) {}

    /** List links grouped by menu. */
    public function index(NavItemDataTable $dataTable): View|JsonResponse
    {
        return $dataTable->handle('admin.navigation.index');
    }

    /** Show the create form. */
    public function create(): View
    {
        return view('admin.navigation.form', ['item' => null, 'columns' => $this->service->megaColumns()]);
    }

    /** Store a new link. */
    public function store(NavItemRequest $request): RedirectResponse
    {
        $this->service->create(NavItemData::fromArray($request->validated()));

        return redirect()->route('admin.navigation.index')->with('success', 'Link created.');
    }

    /** Show the edit form. */
    public function edit(int $id): View
    {
        return view('admin.navigation.form', ['item' => $this->service->findForEdit($id), 'columns' => $this->service->megaColumns()]);
    }

    /** Update a link. */
    public function update(NavItemRequest $request, int $id): RedirectResponse
    {
        $this->service->update($id, NavItemData::fromArray($request->validated()));

        return redirect()->route('admin.navigation.index')->with('success', 'Link updated.');
    }

    /** Delete a link. */
    public function destroy(int $id): RedirectResponse
    {
        $this->service->delete($id);

        return redirect()->route('admin.navigation.index')->with('success', 'Link deleted.');
    }
}
