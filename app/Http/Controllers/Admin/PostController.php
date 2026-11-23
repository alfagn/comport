<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\PostData;
use App\DataTables\PostDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostRequest;
use App\Services\PostService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;

/**
 * Dashboard CRUD for Post.
 *
 * Thin controller: the FormRequest validates, the service does the work,
 * the controller only moves data between them and picks the next screen.
 */
class PostController extends Controller
{
    public function __construct(private PostService $service) {}

    /** List records. */
    public function index(PostDataTable $dataTable): View|JsonResponse
    {
        return $dataTable->handle('admin.posts.index');
    }

    /** Show the create form. */
    public function create(): View
    {
        return view('admin.posts.form', ['item' => null]);
    }

    /** Store a new record. */
    public function store(PostRequest $request): RedirectResponse
    {
        $this->service->create(PostData::fromArray($request->validated()), $request->file('cover_image'));

        return redirect()->route('admin.posts.index')->with('success', 'Post created.');
    }

    /** Show the edit form. */
    public function edit(int $id): View
    {
        return view('admin.posts.form', ['item' => $this->service->findForEdit($id)]);
    }

    /** Update a record. */
    public function update(PostRequest $request, int $id): RedirectResponse
    {
        $this->service->update($id, PostData::fromArray($request->validated()), $request->file('cover_image'));

        return redirect()->route('admin.posts.index')->with('success', 'Post updated.');
    }

    /** Delete a record. */
    public function destroy(int $id): RedirectResponse
    {
        $this->service->delete($id);

        return redirect()->route('admin.posts.index')->with('success', 'Post deleted.');
    }
}
