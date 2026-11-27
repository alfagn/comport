<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingsRequest;
use App\Http\Requests\Admin\SupportChatSettingsRequest;
use App\Models\Setting;
use App\Services\AiRuntimeGuard;
use App\Services\SettingService;
use App\Services\WhatsappCloudApiService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * Unified System Settings Hub (/admin/settings):
 * Presents interactive Cards for:
 *  1. General Settings (Domain, Brand Logos, Contact Desks, Socials & Cookie Consent)
 *  2. WhatsApp Cloud API (Meta Credentials, Webhook Endpoint & Live Subscription)
 *  3. AI Chatbot & Copilot (Website Concierge Greeting, Shared FAQ & LLM Routing)
 */
class SettingsController extends Controller
{
    public function __construct(
        private SettingService $settings,
        private WhatsappCloudApiService $whatsappApi,
        private AiRuntimeGuard $guard,
    ) {}

    /**
     * Render the System Settings Cards Hub and its section forms.
     */
    public function edit(Request $request): View
    {
        $requestedSection = (string) $request->query('section', '');
        $activeSection = in_array($requestedSection, ['general', 'whatsapp', 'chatbot'], true)
            ? $requestedSection
            : null;

        $provider = Setting::getValue('ai_support_provider')
            ?: config('assistant.support.provider')
            ?: config('ai.default', 'openai');

        return view('admin.settings.edit', [
            'activeSection' => $activeSection,
            'groups' => $this->settings->grouped(),
            'whatsappConfigured' => $this->whatsappApi->isConfigured(),
            'whatsappEnabled' => $this->whatsappApi->isEnabled(),
            'whatsappVerifyToken' => $this->whatsappApi->verifyToken(),
            'whatsappWebhookUrl' => url('/api/webhooks/whatsapp'),
            'providerReady' => $this->guard->isReady($provider),
            'providerName' => $this->guard->resolvedProvider($provider),
            'providerHint' => $this->guard->missingCredentialsHint($provider),
        ]);
    }

    /**
     * Persist General Settings & Brand Assets.
     */
    public function update(SettingsRequest $request): RedirectResponse
    {
        $this->settings->saveGroup($request->validated('settings'));
        $this->settings->saveBrandAssets(
            $request->safe()->only(['brand_favicon', 'brand_logo', 'brand_logo_dark']) + $request->allFiles()
        );

        return redirect()
            ->route('admin.settings.edit', ['section' => 'general'])
            ->with('success', 'Settings saved.');
    }

    /**
     * Persist Meta WhatsApp Business Cloud API Settings.
     */
    public function updateWhatsapp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'whatsapp_enabled' => ['nullable', 'boolean'],
            'whatsapp_public_number' => ['nullable', 'string', 'max:40'],
            'whatsapp_prefill_message' => ['nullable', 'string', 'max:250'],
            'whatsapp_phone_number_id' => ['nullable', 'string', 'max:80'],
            'whatsapp_business_account_id' => ['nullable', 'string', 'max:80'],
            'whatsapp_access_token' => ['nullable', 'string', 'max:1000'],
            'whatsapp_verify_token' => ['required', 'string', 'min:6', 'max:160'],
            'whatsapp_app_secret' => ['nullable', 'string', 'max:160'],
            'whatsapp_api_version' => ['nullable', 'string', 'regex:/^v\d+\.\d+$/'],
        ]);

        Setting::setValue('whatsapp_enabled', ! empty($validated['whatsapp_enabled']) ? '1' : '0', 'whatsapp');
        Setting::setValue('whatsapp_public_number', $validated['whatsapp_public_number'] ?? null, 'whatsapp');
        Setting::setValue('whatsapp_prefill_message', $validated['whatsapp_prefill_message'] ?? null, 'whatsapp');
        Setting::setValue('whatsapp_phone_number_id', $validated['whatsapp_phone_number_id'] ?? null, 'whatsapp');
        Setting::setValue('whatsapp_business_account_id', $validated['whatsapp_business_account_id'] ?? null, 'whatsapp');
        Setting::setValue('whatsapp_verify_token', $validated['whatsapp_verify_token'], 'whatsapp');
        Setting::setValue('whatsapp_api_version', $validated['whatsapp_api_version'] ?: 'v22.0', 'whatsapp');

        if (filled($validated['whatsapp_access_token'] ?? null)) {
            Setting::setValue('whatsapp_access_token', trim((string) $validated['whatsapp_access_token']), 'whatsapp');
        }
        if (filled($validated['whatsapp_app_secret'] ?? null)) {
            Setting::setValue('whatsapp_app_secret', trim((string) $validated['whatsapp_app_secret']), 'whatsapp');
        }

        return redirect()
            ->route('admin.settings.edit', ['section' => 'whatsapp'])
            ->with('success', 'WhatsApp Cloud API configuration saved.');
    }

    /**
     * Subscribe the Meta WABA to live webhook events from System Settings.
     */
    public function subscribeWhatsappWebhook(): RedirectResponse
    {
        try {
            $this->whatsappApi->subscribeAppToWaba();
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('admin.settings.edit', ['section' => 'whatsapp'])
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('admin.settings.edit', ['section' => 'whatsapp'])
            ->with('success', 'WhatsApp Business Account successfully subscribed to live webhook events!');
    }

    /**
     * Persist Website AI Chatbot & Dashboard Copilot provider/behavior settings.
     */
    public function updateChatbot(SupportChatSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Setting::setValue('ai_support_enabled', ! empty($validated['ai_support_enabled']) ? '1' : '0', 'ai');
        Setting::setValue('ai_support_greeting', $validated['ai_support_greeting'], 'ai');
        Setting::setValue('ai_support_knowledge', $validated['ai_support_knowledge'] ?? '', 'ai');
        Setting::setValue('ai_support_provider', $validated['ai_support_provider'] ?? null, 'ai');
        Setting::setValue('ai_support_model', $validated['ai_support_model'] ?? null, 'ai');
        Setting::setValue('ai_dashboard_provider', $validated['ai_dashboard_provider'] ?? null, 'ai');
        Setting::setValue('ai_dashboard_model', $validated['ai_dashboard_model'] ?? null, 'ai');

        return redirect()
            ->route('admin.settings.edit', ['section' => 'chatbot'])
            ->with('success', 'Chatbot behavior and AI provider settings saved.');
    }
}
