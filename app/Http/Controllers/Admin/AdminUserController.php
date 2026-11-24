<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\AdminUserDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUserRequest;
use App\Services\AdminUserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

/**
 * Dashboard CRUD for admin accounts.
 *
 * Thin controller: the FormRequest validates, the service holds the
 * ownership safety rules, the repository persists.
 */
class AdminUserController extends Controller
{
    public function __construct(private AdminUserService $service) {}

    /** List admin accounts. */
    public function index(AdminUserDataTable $dataTable): View|JsonResponse
    {
        return $dataTable->handle('admin.admins.index');
    }

    /** Show the create form. */
    public function create(): View
    {
        return view('admin.admins.form', [
            'item' => null,
            'allRoles' => $this->service->allRoles(),
            'availableMembers' => $this->service->availableTeamMembers(),
        ]);
    }

    /** Store a new admin account. */
    public function store(AdminUserRequest $request): RedirectResponse
    {
        $this->service->create($request->safe()->except('roles'), $request->validated('roles'), $request->user());

        return redirect()->route('admin.admins.index')->with('success', 'Admin access granted.');
    }

    /** Show the edit form. */
    public function edit(int $admin): View
    {
        return view('admin.admins.form', [
            'item' => \App\Models\User::with(['roles', 'member'])->findOrFail($admin),
            'allRoles' => $this->service->allRoles(),
            'availableMembers' => collect(),
        ]);
    }

    /** Update an admin account. */
    public function update(AdminUserRequest $request, int $admin): RedirectResponse
    {
        $this->service->update($admin, $request->safe()->except('roles'), $request->validated('roles'), $request->user());

        return redirect()->route('admin.admins.index')->with('success', 'Admin updated.');
    }

    /** Delete an admin account. */
    public function destroy(int $admin): RedirectResponse
    {
        $this->service->delete($admin, request()->user());

        return redirect()->route('admin.admins.index')->with('success', 'Admin deleted.');
    }
}
