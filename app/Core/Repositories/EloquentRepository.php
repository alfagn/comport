<?php

namespace App\Core\Repositories;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Default Eloquent-backed implementation of RepositoryInterface.
 *
 * Concrete repositories extend this class, declare their model class once,
 * and add only the query methods that are specific to their entity.
 */
abstract class EloquentRepository implements RepositoryInterface
{
    /**
     * The Eloquent model class this repository manages.
     *
     * @var class-string<Model>
     */
    protected string $model;

    /** {@inheritdoc} */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()->latest()->paginate($perPage);
    }

    /** {@inheritdoc} */
    public function all(): Collection
    {
        return $this->query()->get();
    }

    /** {@inheritdoc} */
    public function find(int $id): Model
    {
        return $this->query()->findOrFail($id);
    }

    /** {@inheritdoc} */
    public function findBySlug(string $slug): Model
    {
        return $this->query()->where('slug', $slug)->firstOrFail();
    }

    /** {@inheritdoc} */
    public function create(array $attributes): Model
    {
        return $this->query()->create($attributes);
    }

    /** {@inheritdoc} */
    public function update(int $id, array $attributes): Model
    {
        $record = $this->find($id);
        $record->update($attributes);

        return $record;
    }

    /** {@inheritdoc} */
    public function delete(int $id): bool
    {
        return (bool) $this->find($id)->delete();
    }

    /** {@inheritdoc} */
    public function count(): int
    {
        return $this->query()->count();
    }

    /** {@inheritdoc} */
    public function listingQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->query();
    }

    /** {@inheritdoc} */
    public function dailyCreatedCounts(int $days): array
    {
        $since = now()->subDays($days - 1)->startOfDay();

        return $this->query()
            ->where('created_at', '>=', $since)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day')
            ->map(fn ($total) => (int) $total)
            ->all();
    }

    /** {@inheritdoc} */
    public function countCreatedBetween(\DateTimeInterface $from, \DateTimeInterface $to): int
    {
        return $this->query()->where('created_at', '>=', $from)->where('created_at', '<', $to)->count();
    }

    /**
     * A fresh query builder for the managed model. Protected so concrete
     * repositories can compose their own constraints on top of it.
     */
    protected function query(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->model::query();
    }
}
