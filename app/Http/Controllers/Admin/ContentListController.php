<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\ContentListItemData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContentListItemRequest;
use App\Services\ContentListItemService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

/**
 * Dashboard CRUD for repeating content list cards.
 *
 * Thin controller: the FormRequest validates, the service does the work,
 * the controller only moves data between them and picks the next screen.
 */
class ContentListController extends Controller
{
    public function __construct(private ContentListItemService $service) {}

    /** List cards across all lists. */
    public function index(\App\DataTables\ContentListItemDataTable $dataTable): View|JsonResponse
    {
        return $dataTable->handle('admin.content-lists.index', [
            'groups' => ContentListItemService::GROUPS,
        ]);
    }

    /** Show the create form. */
    public function create(): View
    {
        return view('admin.content-lists.form', ['item' => null, 'groups' => ContentListItemService::GROUPS]);
    }

    /** Store a new card. */
    public function store(ContentListItemRequest $request): RedirectResponse
    {
        $this->service->create(ContentListItemData::fromArray($request->validated()));

        return redirect()->route('admin.content-lists.index')->with('success', 'Card created.');
    }

    /** Show the edit form. */
    public function edit(int $id): View
    {
        return view('admin.content-lists.form', [
            'item' => $this->service->findForEdit($id),
            'groups' => ContentListItemService::GROUPS,
        ]);
    }

    /** Update a card. */
    public function update(ContentListItemRequest $request, int $id): RedirectResponse
    {
        $this->service->update($id, ContentListItemData::fromArray($request->validated()));

        return redirect()->route('admin.content-lists.index')->with('success', 'Card updated.');
    }

    /** Delete a card. */
    public function destroy(int $id): RedirectResponse
    {
        $this->service->delete($id);

        return redirect()->route('admin.content-lists.index')->with('success', 'Card deleted.');
    }
}
