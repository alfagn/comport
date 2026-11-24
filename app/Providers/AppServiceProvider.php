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
        // Resilient binary magic-byte MIME guesser so uploads (/tmp/phpXXXX without extension)
        // work reliably even on servers where PHP's ext-fileinfo is disabled (e.g. aaPanel).
        if (class_exists(\Symfony\Component\Mime\MimeTypes::class)) {
            \Symfony\Component\Mime\MimeTypes::getDefault()->registerGuesser(new class implements \Symfony\Component\Mime\MimeTypeGuesserInterface {
                public function isGuesserSupported(): bool
                {
                    return true;
                }

                public function guessMimeType(string $path): ?string
                {
                    if (function_exists('finfo_open') && is_file($path)) {
                        $finfo = @finfo_open(FILEINFO_MIME_TYPE);
                        if ($finfo) {
                            $mime = @finfo_file($finfo, $path);
                            @finfo_close($finfo);
                            if (is_string($mime) && $mime !== '') {
                                return $mime;
                            }
                        }
                    }

                    // 1. Inspect actual binary image headers (works on extensionless /tmp/phpXXXX files without ext-fileinfo)
                    if (is_file($path) && is_readable($path)) {
                        $imgInfo = @getimagesize($path);
                        if (is_array($imgInfo) && ! empty($imgInfo['mime']) && is_string($imgInfo['mime'])) {
                            return $imgInfo['mime'];
                        }

                        // 2. Inspect magic bytes for SVG, PDF, MP4, WebM, OGG, ICO, ZIP/DOCX
                        $fh = @fopen($path, 'rb');
                        if ($fh) {
                            $head = (string) @fread($fh, 512);
                            @fclose($fh);

                            if (str_starts_with($head, '%PDF-')) {
                                return 'application/pdf';
                            }
                            if (str_starts_with($head, "\x00\x00\x01\x00")) {
                                return 'image/x-icon';
                            }
                            if (strlen($head) >= 12 && substr($head, 4, 4) === 'ftyp') {
                                $brand = substr($head, 8, 4);
                                return $brand === 'qt  ' ? 'video/quicktime' : 'video/mp4';
                            }
                            if (str_starts_with($head, "\x1A\x45\xDF\xA3")) {
                                return 'video/webm';
                            }
                            if (str_starts_with($head, 'OggS')) {
                                return 'video/ogg';
                            }
                            if (preg_match('/<svg[\s>]/i', $head)) {
                                return 'image/svg+xml';
                            }
                            if (str_starts_with($head, "PK\x03\x04")) {
                                return 'application/zip';
                            }
                        }
                    }

                    // 3. Fallback to file extension if present
                    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    return match ($ext) {
                        'jpg', 'jpeg' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                        'webp' => 'image/webp',
                        'svg' => 'image/svg+xml',
                        'ico' => 'image/x-icon',
                        'avif' => 'image/avif',
                        'pdf' => 'application/pdf',
                        'doc' => 'application/msword',
                        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'mp4' => 'video/mp4',
                        'webm' => 'video/webm',
                        'ogg', 'ogv' => 'video/ogg',
                        'mov' => 'video/quicktime',
                        default => 'application/octet-stream',
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
