<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageSectionRequest;
use App\Services\SectionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

/**
 * Dashboard editor for page content sections (the "every word and image"
 * layer of the CMS).
 */
class PageSectionController extends Controller
{
    public function __construct(private SectionService $sections) {}

    /** List pages with their editable sections. */
    public function index(): View
    {
        return view('admin.pages.index', ['pages' => $this->sections->groupedByPage()]);
    }

    /** Edit one page's sections. */
    public function edit(string $page): View
    {
        return view('admin.pages.edit', [
            'page' => $page,
            'sections' => $this->sections->forPage($page),
        ]);
    }

    /**
     * Persist one page's sections. Image-typed sections accept uploads;
     * anything else is stored as text.
     */
    public function update(PageSectionRequest $request, string $page): RedirectResponse
    {
        $values = $request->validated('sections');

        // Image sections come in as files, not text values.
        foreach ($request->file('images', []) as $key => $file) {
            if ($file) {
                $values[$key] = $file->store('uploads/pages', 'public');
            }
        }

        $this->sections->savePage($page, $values);

        return back()->with('success', 'Page content saved.');
    }
}
