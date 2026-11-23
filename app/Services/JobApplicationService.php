<?php

namespace App\Services;

use App\Core\Services\BaseService;
use App\DTOs\JobApplicationData;
use App\Models\JobApplication;
use App\Repositories\Contracts\JobApplicationRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Business logic for job applications: storing public submissions with
 * their CV on the PRIVATE disk, review state, and cleanup on delete.
 */
class JobApplicationService extends BaseService
{
    /** Disk that keeps CVs out of the public web root. */
    public const CV_DISK = 'local';

    public function __construct(JobApplicationRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /** Store an application submitted through the public careers form. */
    public function storeFromPublic(JobApplicationData $data, UploadedFile $cv): Model
    {
        $data->cv_path = $cv->store('cvs', self::CV_DISK);
        $data->status = 'new';

        return $this->repository->create($data->toArray());
    }

    /** Open an application and mark it reviewed in one step. */
    public function openAndMarkReviewed(int $id): Model
    {
        $application = $this->repository->find($id);
        $application->markReviewed();

        return $application;
    }

    /** Absolute path of an application's CV for the authenticated download. */
    public function cvDownload(JobApplication $application): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $extension = pathinfo($application->cv_path, PATHINFO_EXTENSION);
        $filename = 'cv-'.$application->full_name.'.'.$extension;

        return Storage::disk(self::CV_DISK)->download($application->cv_path, $filename);
    }

    /** Delete an application and its CV file. */
    public function delete(int $id): bool
    {
        $application = $this->repository->find($id);
        Storage::disk(self::CV_DISK)->delete($application->cv_path);

        return $this->repository->delete($id);
    }
}
