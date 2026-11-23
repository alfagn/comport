<?php

namespace App\Repositories\Eloquent;

use App\Core\Repositories\EloquentRepository;
use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;

/**
 * Eloquent-backed repository for Setting records.
 */
class SettingRepository extends EloquentRepository implements SettingRepositoryInterface
{
    protected string $model = Setting::class;
}
