<?php

namespace App\Ai\Tools\Support;

use App\Models\Industry;
use App\Models\Service;
use App\Models\ServicePage;
use App\Models\Setting;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Provides public company identity, active capabilities/services, industries,
 * and contact channels from the live CMS.
 */
class GetCompanyOverview implements Tool
{
    public function description(): Stringable|string
    {
        return 'Fetch official Empetix company information, contact details (email, phone, headquarters), active services/capabilities, and industries served.';
    }

    public function handle(Request $request): Stringable|string
    {
        $publishedServicePages = page_active('services')
            ? ServicePage::query()->where('locale', 'en')->where('is_published', true)->pluck('slug')->flip()
            : collect();

        $services = Service::active()->get()->map(function (Service $service) use ($publishedServicePages): array {
            $url = ($service->slug && $publishedServicePages->has($service->slug))
                ? url('/services/'.$service->slug)
                : url('/#solutions-hub');

            return [
                'title' => $service->title,
                'heading' => $service->heading,
                'description' => $service->description,
                'deliverables' => $service->deliverables ?? [],
                'url' => $url,
            ];
        })->all();

        $industries = Industry::active()->get()->map(fn (Industry $industry): array => [
            'title' => $industry->title,
            'subtitle' => $industry->subtitle,
        ])->all();

        $payload = [
            'company' => [
                'name' => Setting::getValue('site_name', 'Empetix Solutions'),
                'tagline' => Setting::getValue('tagline', 'Enterprise software engineering, SaaS, cloud & AI studio.'),
                'website' => site_domain(),
                'contact_page' => page_active('contact') ? route('contact') : null,
                'email' => Setting::getValue('email', 'hello@empetix.com'),
                'partners_email' => Setting::getValue('partners_email'),
                'talent_email' => Setting::getValue('talent_email'),
                'phone' => Setting::getValue('phone'),
                'address' => Setting::getValue('address'),
            ],
            'services' => $services,
            'industries' => $industries,
        ];

        return (string) json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
