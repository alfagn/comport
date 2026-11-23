<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\MemberData;
use App\DataTables\MemberDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MemberRequest;
use App\Services\MemberService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;

/**
 * Dashboard CRUD for Member.
 *
 * Thin controller: the FormRequest validates, the service does the work,
 * the controller only moves data between them and picks the next screen.
 */
class MemberController extends Controller
{
    public function __construct(private MemberService $service) {}

    /** List records. */
    public function index(MemberDataTable $dataTable): View|JsonResponse
    {
        return $dataTable->handle('admin.members.index');
    }

    /** Show the create form. */
    public function create(): View
    {
        return view('admin.members.form', ['item' => null]);
    }

    /** Store a new record. */
    public function store(MemberRequest $request): RedirectResponse
    {
        $this->service->create(MemberData::fromArray($request->validated()), $request->file('photo'));

        return redirect()->route('admin.members.index')->with('success', 'Member created.');
    }

    /** Show the edit form. */
    public function edit(int $id): View
    {
        return view('admin.members.form', ['item' => $this->service->findForEdit($id)]);
    }

    /** Update a record. */
    public function update(MemberRequest $request, int $id): RedirectResponse
    {
        $this->service->update($id, MemberData::fromArray($request->validated()), $request->file('photo'));

        return redirect()->route('admin.members.index')->with('success', 'Member updated.');
    }

    /** Delete a record. */
    public function destroy(int $id): RedirectResponse
    {
        $this->service->delete($id);

        return redirect()->route('admin.members.index')->with('success', 'Member deleted.');
    }
}
