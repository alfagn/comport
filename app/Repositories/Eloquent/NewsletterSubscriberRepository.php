<?php

namespace App\Repositories\Eloquent;

use App\Core\Repositories\EloquentRepository;
use App\Models\NewsletterSubscriber;
use App\Repositories\Contracts\NewsletterSubscriberRepositoryInterface;

/**
 * Eloquent-backed repository for NewsletterSubscriber records.
 */
class NewsletterSubscriberRepository extends EloquentRepository implements NewsletterSubscriberRepositoryInterface
{
    protected string $model = NewsletterSubscriber::class;
}
