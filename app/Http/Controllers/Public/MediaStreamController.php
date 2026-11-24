<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PageSection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Streams CMS video files stored on the private local disk (storage/app/private)
 * with native HTTP Range (206 Partial Content) support for browser seeking.
 */
class MediaStreamController extends Controller
{
    public function video(string $path): BinaryFileResponse
    {
        $normalized = ltrim(str_replace('\\', '/', $path), '/');

        abort_if(
            $normalized === ''
            || str_contains($normalized, '..')
            || ! str_starts_with($normalized, 'videos/'),
            404
        );

        $referenced = PageSection::query()
            ->where('value', 'private:'.$normalized)
            ->exists();

        abort_unless($referenced && Storage::disk('local')->exists($normalized), 404);

        $absolutePath = Storage::disk('local')->path($normalized);
        $mime = Storage::disk('local')->mimeType($normalized) ?: 'video/mp4';

        return response()->file($absolutePath, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
