<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\JobPostData;
use App\DataTables\JobPostDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\JobPostRequest;
use App\Services\JobPostService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;

/**
 * Dashboard CRUD for JobPost.
 *
 * Thin controller: the FormRequest validates, the service does the work,
 * the controller only moves data between them and picks the next screen.
 */
class JobPostController extends Controller
{
    public function __construct(private JobPostService $service) {}

    /** List records. */
    public function index(JobPostDataTable $dataTable): View|JsonResponse
    {
        return $dataTable->handle('admin.jobs.index');
    }

    /** Show the create form. */
    public function create(): View
    {
        return view('admin.jobs.form', ['item' => null, 'members' => \App\Models\Member::orderBy('name')->get(['id', 'name', 'role'])]);
    }

    /** Store a new record. */
    public function store(JobPostRequest $request): RedirectResponse
    {
        $this->service->create(JobPostData::fromArray($request->validated()));

        return redirect()->route('admin.jobs.index')->with('success', 'JobPost created.');
    }

    /** Show the edit form. */
    public function edit(int $id): View
    {
        return view('admin.jobs.form', [
            'item' => $this->service->findForEdit($id),
            'members' => \App\Models\Member::orderBy('name')->get(['id', 'name', 'role']),
        ]);
    }

    /** Update a record. */
    public function update(JobPostRequest $request, int $id): RedirectResponse
    {
        $this->service->update($id, JobPostData::fromArray($request->validated()));

        return redirect()->route('admin.jobs.index')->with('success', 'JobPost updated.');
    }

    /** Delete a record. */
    public function destroy(int $id): RedirectResponse
    {
        $this->service->delete($id);

        return redirect()->route('admin.jobs.index')->with('success', 'JobPost deleted.');
    }
}
