<?php

namespace App\Ai\Tools\Dashboard;

use App\Models\ContactMessage;
use App\Models\HeroSlide;
use App\Models\Industry;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\Member;
use App\Models\NavItem;
use App\Models\PageSection;
use App\Models\Partner;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\ServicePage;
use App\Models\Setting;
use App\Models\SupportChatSession;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Laravel\Ai\Tools\Request;
use Stringable;
use Throwable;

/**
 * Empowers the Dashboard AI Copilot to answer any technical question about the
 * Empetix website, backend architecture, routes, database schema, Eloquent models,
 * AI/WhatsApp pipelines, SEO/discovery endpoints, and runtime environment.
 */
class InspectTechnicalArchitecture extends DashboardTool
{
    public function permission(): ?string
    {
        return null;
    }

    public function description(): Stringable|string
    {
        return 'Inspect the technical architecture of the Empetix website and CMS: live routes, controllers, database schema & table columns, Eloquent models, AI & WhatsApp integration internals, SEO/llms.txt/sitemap engine, frontend stack, and runtime environment diagnostics.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'topic' => $schema->string()
                ->enum(['overview', 'routes', 'database', 'models', 'ai_and_whatsapp', 'seo_and_discovery', 'frontend', 'environment'])
                ->description('Which technical subsystem to inspect. Use "overview" for a full-stack summary.')
                ->nullable(),
            'filter' => $schema->string()
                ->description('Optional keyword or table/route name to filter routes, tables, or models.')
                ->nullable(),
        ];
    }

    protected function run(Request $request): string
    {
        $topic = (string) ($request['topic'] ?? 'overview');
        $filter = trim((string) ($request['filter'] ?? ''));

        return match ($topic) {
            'routes' => $this->inspectRoutes($filter),
            'database' => $this->inspectDatabase($filter),
            'models' => $this->inspectModels($filter),
            'ai_and_whatsapp' => $this->inspectAiAndWhatsapp(),
            'seo_and_discovery' => $this->inspectSeoAndDiscovery(),
            'frontend' => $this->inspectFrontend(),
            'environment' => $this->inspectEnvironment(),
            default => $this->inspectOverview(),
        };
    }

    protected function inspectOverview(): string
    {
        return $this->json([
            'application' => [
                'name' => config('app.name', 'Empetix Solutions'),
                'framework' => 'Laravel '.app()->version(),
                'php_version' => PHP_VERSION,
                'environment' => app()->environment(),
                'url' => config('app.url'),
            ],
            'architecture_stack' => [
                'backend' => 'Laravel 13 / PHP 8.4 with Repository + Service layer (app/Services, app/Repositories), Spatie Permission RBAC, Yajra DataTables, and Livewire 4.',
                'ai_engine' => 'Laravel AI SDK (laravel/ai v1.4) with two distinct agents: DashboardAssistantAgent (authenticated admin copilot with tool approvals, file/image attachments, and technical codebase inspection) and CustomerSupportAgent (public Website + Meta WhatsApp Cloud API concierge).',
                'realtime_engine' => 'Live runtime presence & delta-sync engine (/admin/support-chats/{id}/live, /admin/support-chats/live-pulse, /support-chat/typing) delivering real-time typing indicators and instant message streaming for Website and WhatsApp threads.',
                'seo_and_ai_discovery' => 'Dynamic /sitemap.xml, /robots.txt, /llms.txt, and /llms-full.txt powered by SeoService & SeoDiscoveryController.',
                'frontend' => 'Modular Blade layouts (layouts/public.blade.php, layouts/admin.blade.php), hand-crafted CSS tokens, and ES module / deferred scripts under public/assets/.',
            ],
            'key_directories' => [
                'app/Ai/Agents' => 'DashboardAssistantAgent & CustomerSupportAgent',
                'app/Ai/Tools/Dashboard' => 'Permission-gated operational & technical inspection tools for the Dashboard Copilot',
                'app/Ai/Tools/Support' => 'Public CMS search & lead handoff tools for the customer chatbot',
                'app/Http/Controllers/Admin' => 'CMS dashboard controllers (AssistantController, SupportChatController, SeoController, etc.)',
                'app/Http/Controllers/Public' => 'Public site & support chat controllers',
                'app/Http/Controllers/Api' => 'WhatsappWebhookController (/api/webhooks/whatsapp)',
                'app/Services' => 'Business logic services (DashboardAssistantService, CustomerSupportChatService, WhatsappCloudApiService, WhatsappCustomerSupportService, SeoService, etc.)',
                'resources/views/admin' => 'Dashboard Blade views including assistant/ and support-chats/',
                'public/assets' => 'Frontend & admin CSS/JS modules',
            ],
        ]);
    }

    protected function inspectRoutes(string $filter): string
    {
        $routes = collect(Route::getRoutes()->getRoutes())
            ->map(function ($route): array {
                $methods = array_values(array_diff($route->methods(), ['HEAD']));

                return [
                    'methods' => implode('|', $methods),
                    'uri' => '/'.ltrim($route->uri(), '/'),
                    'name' => $route->getName(),
                    'action' => $route->getActionName(),
                    'middleware' => $route->gatherMiddleware(),
                ];
            })
            ->filter(function (array $r) use ($filter): bool {
                if ($filter === '') {
                    return ! str_starts_with($r['uri'], '/_');
                }

                $haystack = strtolower($r['uri'].' '.($r['name'] ?? '').' '.$r['action']);

                return str_contains($haystack, strtolower($filter));
            })
            ->take(80)
            ->values()
            ->all();

        return $this->json([
            'filter' => $filter ?: null,
            'count_returned' => count($routes),
            'routes' => $routes,
        ]);
    }

    protected function inspectDatabase(string $filter): string
    {
        $coreTables = [
            'users', 'roles', 'permissions', 'members', 'hero_slides',
            'services', 'service_pages', 'industries', 'projects', 'posts',
            'job_posts', 'job_applications', 'partners', 'contact_messages',
            'newsletter_subscribers', 'settings', 'page_sections', 'nav_items',
            'content_lists', 'agent_conversations', 'agent_conversation_messages',
            'support_chat_sessions',
        ];

        $tables = [];
        foreach ($coreTables as $table) {
            if ($filter !== '' && ! str_contains($table, strtolower($filter))) {
                continue;
            }

            if (! Schema::hasTable($table)) {
                continue;
            }

            $count = 0;
            try {
                $count = DB::table($table)->count();
            } catch (Throwable) {
            }

            $tables[$table] = [
                'rows' => $count,
                'columns' => Schema::getColumnListing($table),
            ];
        }

        return $this->json([
            'connection' => config('database.default'),
            'tables' => $tables,
        ]);
    }

    protected function inspectModels(string $filter): string
    {
        $models = [
            User::class => 'Authenticated admin/team user with Spatie HasRoles and HasConversations.',
            Member::class => 'Team members & founders linked to users and public /team & /founders/{slug} profiles.',
            Service::class => 'Core service offerings displayed on Home & Services pages.',
            ServicePage::class => 'Dedicated English/Arabic landing pages under /services/{slug}.',
            Industry::class => 'Target industry verticals served by the studio.',
            Project::class => 'Portfolio case studies under /work & /work/{slug}.',
            Post::class => 'Blog articles and insights under /blog & /blog/{slug}.',
            JobPost::class => 'Open career positions under /careers & /careers/{slug}.',
            JobApplication::class => 'Candidate applications with private CV storage.',
            ContactMessage::class => 'Client project briefs in Admin Inbox (from /contact form or AI chatbot/WhatsApp lead handoff).',
            SupportChatSession::class => 'Customer support threads across Website widget (channel=web) and Meta WhatsApp Cloud API (channel=whatsapp).',
            Setting::class => 'Key-value site, SEO, AI provider, and WhatsApp configuration store.',
            PageSection::class => 'Editable copy/media blocks for public pages.',
            HeroSlide::class => 'Homepage hero carousel slides.',
            Partner::class => 'Client & technology partner logos.',
            NavItem::class => 'Dynamic header/footer navigation items.',
        ];

        $result = [];
        foreach ($models as $class => $purpose) {
            if ($filter !== '' && ! str_contains(strtolower($class.' '.$purpose), strtolower($filter))) {
                continue;
            }

            /** @var Model $instance */
            $instance = new $class;
            $result[class_basename($class)] = [
                'class' => $class,
                'table' => $instance->getTable(),
                'fillable' => $instance->getFillable(),
                'purpose' => $purpose,
            ];
        }

        return $this->json(['models' => $result]);
    }

    protected function inspectAiAndWhatsapp(): string
    {
        return $this->json([
            'dashboard_copilot' => [
                'agent' => 'App\\Ai\\Agents\\DashboardAssistantAgent',
                'service' => 'App\\Services\\DashboardAssistantService',
                'controller' => 'App\\Http\\Controllers\\Admin\\AssistantController',
                'provider' => Setting::getValue('ai_dashboard_provider') ?: config('assistant.dashboard.provider') ?: config('ai.default'),
                'model' => Setting::getValue('ai_dashboard_model') ?: config('assistant.dashboard.model'),
                'attachments_storage' => 'storage/app/private/copilot-attachments/{conversation_id} (automatically purged when the session is deleted)',
                'human_in_the_loop' => 'Destructive and public-facing tools implement Laravel\\Ai\\Contracts\\Approvable and pause for admin approval.',
            ],
            'customer_support_concierge' => [
                'agent' => 'App\\Ai\\Agents\\CustomerSupportAgent',
                'channels' => ['web' => '/support-chat/message', 'whatsapp' => '/api/webhooks/whatsapp'],
                'provider' => Setting::getValue('ai_support_provider') ?: config('assistant.support.provider') ?: config('ai.default'),
                'model' => Setting::getValue('ai_support_model') ?: config('assistant.support.model'),
                'realtime_presence' => 'Live typing heartbeats (/support-chat/typing & /admin/support-chats/{id}/typing) and delta polling (/admin/support-chats/{id}/live) sync customer, AI, and admin typing states and messages in real time.',
                'lead_handoff' => 'SubmitProjectInquiry tool creates/updates a ContactMessage in Admin Inbox and marks the SupportChatSession as handed_off.',
            ],
            'whatsapp_cloud_api' => [
                'service' => 'App\\Services\\WhatsappCloudApiService',
                'webhook_controller' => 'App\\Http\\Controllers\\Api\\WhatsappWebhookController',
                'webhook_url' => url('/api/webhooks/whatsapp'),
                'enabled' => Setting::getValue('whatsapp_enabled', '1') !== '0',
                'phone_number_id_configured' => filled(Setting::getValue('whatsapp_phone_number_id') ?: config('assistant.whatsapp.phone_number_id')),
                'public_click_to_chat_number' => Setting::getValue('whatsapp_public_number') ?: config('assistant.whatsapp.public_number'),
            ],
        ]);
    }

    protected function inspectSeoAndDiscovery(): string
    {
        return $this->json([
            'service' => 'App\\Services\\SeoService',
            'controller' => 'App\\Http\\Controllers\\Public\\SeoDiscoveryController',
            'endpoints' => [
                'sitemap_xml' => url('/sitemap.xml'),
                'robots_txt' => url('/robots.txt'),
                'llms_txt' => url('/llms.txt'),
                'llms_full_txt' => url('/llms-full.txt'),
            ],
            'settings' => [
                'seo_allow_indexing' => Setting::getValue('seo_allow_indexing', '1'),
                'seo_welcome_ai_bots' => Setting::getValue('seo_welcome_ai_bots', '1'),
                'sitemap_last_generated_at' => Setting::getValue('sitemap_last_generated_at'),
            ],
        ]);
    }

    protected function inspectFrontend(): string
    {
        return $this->json([
            'public_layout' => 'resources/views/layouts/public.blade.php',
            'admin_layout' => 'resources/views/layouts/admin.blade.php',
            'public_floating_dock' => [
                'partial' => 'resources/views/partials/support-chatbot.blade.php',
                'css' => 'public/assets/css/components/support-chatbot.css',
                'js' => 'public/assets/js/modules/support-chatbot.js',
                'description' => 'Vertically stacked circular floating dock: top animated AI Chatbot concierge button + bottom official WhatsApp Click-to-Chat button.',
            ],
            'admin_copilot_assets' => [
                'workspace_view' => 'resources/views/admin/assistant/index.blade.php',
                'drawer_partial' => 'resources/views/admin/_partials/copilot-drawer.blade.php',
                'css' => 'public/assets/admin/assistant.css',
                'js' => 'public/assets/admin/assistant.js',
            ],
        ]);
    }

    protected function inspectEnvironment(): string
    {
        return $this->json([
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'app_env' => config('app.env'),
            'app_debug' => (bool) config('app.debug'),
            'app_url' => config('app.url'),
            'timezone' => config('app.timezone'),
            'drivers' => [
                'database' => config('database.default'),
                'cache' => config('cache.default'),
                'queue' => config('queue.default'),
                'session' => config('session.driver'),
                'filesystem' => config('filesystems.default'),
                'ai_default' => config('ai.default'),
            ],
            'storage_public_linked' => file_exists(public_path('storage')),
            'server_os' => PHP_OS_FAMILY,
        ]);
    }
}
