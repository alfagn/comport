<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\ProjectData;
use App\DataTables\ProjectDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectRequest;
use App\Services\ProjectService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

/**
 * Dashboard CRUD for Project.
 *
 * Thin controller: the FormRequest validates, the service does the work,
 * the controller only moves data between them and picks the next screen.
 */
class ProjectController extends Controller
{
    public function __construct(private ProjectService $service) {}

    /** List records. */
    public function index(ProjectDataTable $dataTable): View|JsonResponse
    {
        return $dataTable->handle('admin.projects.index');
    }

    /** Show the create form. */
    public function create(): View
    {
        return view('admin.projects.form', ['item' => null]);
    }

    /** Store a new record. */
    public function store(ProjectRequest $request): RedirectResponse
    {
        $galleryFiles = $request->file('gallery_files', []);
        if (!is_array($galleryFiles)) {
            $galleryFiles = array_filter([$galleryFiles]);
        }

        $this->service->create(
            ProjectData::fromArray($request->validated()),
            $request->file('image'),
            $galleryFiles
        );

        return redirect()->route('admin.projects.index')->with('success', 'Project created.');
    }

    /** Show the edit form. */
    public function edit(int $id): View
    {
        return view('admin.projects.form', ['item' => $this->service->findForEdit($id)]);
    }

    /** Update a record. */
    public function update(ProjectRequest $request, int $id): RedirectResponse
    {
        $galleryFiles = $request->file('gallery_files', []);
        if (!is_array($galleryFiles)) {
            $galleryFiles = array_filter([$galleryFiles]);
        }

        $this->service->update(
            $id,
            ProjectData::fromArray($request->validated()),
            $request->file('image'),
            $galleryFiles
        );

        return redirect()->route('admin.projects.index')->with('success', 'Project updated.');
    }

    /** Delete a record. */
    public function destroy(int $id): RedirectResponse
    {
        $this->service->delete($id);

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted.');
    }
}
