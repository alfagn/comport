<?php

namespace App\Providers;

use App\Models\User;
use App\Repositories\Contracts;
use App\Repositories\Eloquent;
use App\Support\Permissions;
use Illuminate\Support\Facades\Gate;
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
        Contracts\ServicePageRepositoryInterface::class => Eloquent\ServicePageRepository::class,
        Contracts\IndustryRepositoryInterface::class => Eloquent\IndustryRepository::class,
        Contracts\ProjectRepositoryInterface::class => Eloquent\ProjectRepository::class,
        Contracts\MemberRepositoryInterface::class => Eloquent\MemberRepository::class,
        Contracts\JobPostRepositoryInterface::class => Eloquent\JobPostRepository::class,
        Contracts\PostRepositoryInterface::class => Eloquent\PostRepository::class,
        Contracts\PartnerRepositoryInterface::class => Eloquent\PartnerRepository::class,
        Contracts\ContactMessageRepositoryInterface::class => Eloquent\ContactMessageRepository::class,
        Contracts\JobApplicationRepositoryInterface::class => Eloquent\JobApplicationRepository::class,
        Contracts\SettingRepositoryInterface::class => Eloquent\SettingRepository::class,
        Contracts\NewsletterSubscriberRepositoryInterface::class => Eloquent\NewsletterSubscriberRepository::class,
        Contracts\PageSectionRepositoryInterface::class => Eloquent\PageSectionRepository::class,
        Contracts\NavItemRepositoryInterface::class => Eloquent\NavItemRepository::class,
        Contracts\ContentListItemRepositoryInterface::class => Eloquent\ContentListItemRepository::class,
        Contracts\AdminUserRepositoryInterface::class => Eloquent\AdminUserRepository::class,
    ];

    /** Register any application services. */
    public function register(): void
    {
        //
    }

    /** Bootstrap any application services. */
    public function boot(): void
    {
        // Fallback MIME guesser so Symfony never throws LogicException if fileinfo is temporarily inactive
        if (class_exists(\Symfony\Component\Mime\MimeTypes::class)) {
            \Symfony\Component\Mime\MimeTypes::getDefault()->registerGuesser(new class implements \Symfony\Component\Mime\MimeTypeGuesserInterface {
                public function isGuesserSupported(): bool
                {
                    return true;
                }

                public function guessMimeType(string $path): ?string
                {
                    if (function_exists('finfo_open')) {
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        if ($finfo) {
                            $mime = finfo_file($finfo, $path);
                            finfo_close($finfo);
                            if ($mime) {
                                return $mime;
                            }
                        }
                    }

                    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    return match ($ext) {
                        'jpg', 'jpeg' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                        'webp' => 'image/webp',
                        'svg' => 'image/svg+xml',
                        'pdf' => 'application/pdf',
                        'mp4' => 'video/mp4',
                        'avif' => 'image/avif',
                        default => null,
                    };
                }
            });
        }

        // RBAC: the owner role implicitly holds every permission. All other
        // roles fall through to their granted permissions only.
        Gate::before(function (User $user, string $ability) {
            return $user->hasRole(Permissions::OWNER_ROLE) ? true : null;
        });
    }
}
