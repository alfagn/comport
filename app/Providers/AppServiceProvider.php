<?php

namespace App\Providers;

use App\Repositories\Contracts;
use App\Repositories\Eloquent;
use Illuminate\Support\ServiceProvider;

/**
 * Application service provider.
 *
 * Binds every repository contract to its Eloquent implementation. Services
 * depend on the contracts only, so swapping the persistence layer is a
 * one-line change here (Dependency Inversion / Interface Segregation).
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Contract => implementation map.
     */
    public array $bindings = [
        Contracts\HeroSlideRepositoryInterface::class => Eloquent\HeroSlideRepository::class,
        Contracts\ServiceRepositoryInterface::class => Eloquent\ServiceRepository::class,
        Contracts\IndustryRepositoryInterface::class => Eloquent\IndustryRepository::class,
        Contracts\ProjectRepositoryInterface::class => Eloquent\ProjectRepository::class,
        Contracts\MemberRepositoryInterface::class => Eloquent\MemberRepository::class,
        Contracts\JobPostRepositoryInterface::class => Eloquent\JobPostRepository::class,
        Contracts\PostRepositoryInterface::class => Eloquent\PostRepository::class,
        Contracts\PartnerRepositoryInterface::class => Eloquent\PartnerRepository::class,
        Contracts\ContactMessageRepositoryInterface::class => Eloquent\ContactMessageRepository::class,
        Contracts\SettingRepositoryInterface::class => Eloquent\SettingRepository::class,
        Contracts\NewsletterSubscriberRepositoryInterface::class => Eloquent\NewsletterSubscriberRepository::class,
        Contracts\PageSectionRepositoryInterface::class => Eloquent\PageSectionRepository::class,
        Contracts\NavItemRepositoryInterface::class => Eloquent\NavItemRepository::class,
    ];

    /** Register any application services. */
    public function register(): void
    {
        //
    }

    /** Bootstrap any application services. */
    public function boot(): void
    {
        //
    }
}
