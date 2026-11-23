<?php

namespace App\Services;

use App\Core\Services\BaseService;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Business logic for site settings: grouped key/value pairs edited
 * from one dashboard screen.
 */
class SettingService extends BaseService
{
    public function __construct(SettingRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /** All settings grouped by their group column for the edit form. */
    public function grouped(): Collection
    {
        return $this->repository->all()->groupBy('group');
    }

    /**
     * Persist a settings form submission: every submitted key is upserted;
     * keys absent from the submission are left untouched.
     */
    public function saveGroup(array $values): void
    {
        foreach ($values as $key => $value) {
            \App\Models\Setting::setValue($key, $value);
        }
    }
}
