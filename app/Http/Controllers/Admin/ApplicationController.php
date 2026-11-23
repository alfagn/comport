<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\JobApplicationDataTable;
use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Services\JobApplicationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Dashboard review of job applications. CV downloads are only reachable
 * through this authenticated controller; the files live on the private disk.
 */
class ApplicationController extends Controller
{
    public function __construct(private JobApplicationService $applications) {}

    /** List applications, newest first. */
    public function index(JobApplicationDataTable $dataTable): View|JsonResponse
    {
        return $dataTable->handle('admin.applications.index');
    }

    /** Open one application (marks it reviewed). */
    public function show(int $id): View
    {
        return view('admin.applications.show', [
            'application' => $this->applications->openAndMarkReviewed($id),
        ]);
    }

    /** Stream the private CV to an authenticated admin. */
    public function cv(JobApplication $application): StreamedResponse
    {
        return $this->applications->cvDownload($application);
    }

    /** Delete an application and its CV. */
    public function destroy(int $id): RedirectResponse
    {
        $this->applications->delete($id);

        return redirect()->route('admin.applications.index')->with('success', 'Application deleted.');
    }
}
