<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\RoleDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Services\RoleService;
use App\Support\Permissions;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;

/**
 * Dashboard CRUD for roles and their permission sets.
 */
class RoleController extends Controller
{
    public function __construct(private RoleService $service) {}

    /** List roles. */
    public function index(RoleDataTable $dataTable): View|JsonResponse
    {
        return $dataTable->handle('admin.roles.index');
    }

    /** Show the create form. */
    public function create(): View
    {
        return view('admin.roles.form', ['item' => null, 'catalog' => Permissions::all()]);
    }

    /** Store a new role. */
    public function store(RoleRequest $request): RedirectResponse
    {
        $this->service->create($request->safe()->except('permissions'), $request->validated('permissions'));

        return redirect()->route('admin.roles.index')->with('success', 'Role created.');
    }

    /** Show the edit form. */
    public function edit(int $role): View
    {
        return view('admin.roles.form', [
            'item' => Role::with('permissions')->findOrFail($role),
            'catalog' => Permissions::all(),
        ]);
    }

    /** Update a role. */
    public function update(RoleRequest $request, int $role): RedirectResponse
    {
        $this->service->update($role, $request->safe()->except('permissions'), $request->validated('permissions'));

        return redirect()->route('admin.roles.index')->with('success', 'Role updated.');
    }

    /** Delete a role. */
    public function destroy(int $role): RedirectResponse
    {
        $this->service->delete($role);

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted.');
    }
}
