<?php

/*
|--------------------------------------------------------------------------
| Empetix AI Assistants
|--------------------------------------------------------------------------
|
| Two agents built on the Laravel AI SDK (config/ai.php holds the provider
| credentials):
|
|  - dashboard: the admin copilot that answers questions about the CMS and
|    performs tasks through permission-scoped tools (destructive or
|    public-facing actions always pause for a human approval).
|  - support:   the public customer-support chatbot embedded on the site.
|    It can only read published content and hand conversations off to the
|    team through the Inbox.
|
| Leave provider / model empty to fall back to `ai.default` and that
| provider's default text model.
|
*/

return [

    'failover' => [
        // Automatically fail over to backup Gemini & emergency providers on 503 / 429 / timeouts.
        'enabled' => (bool) env('AI_FAILOVER_ENABLED', true),

        // Ordered emergency provider chain (only providers with configured API keys are armed).
        'chain' => (string) env('AI_FAILOVER_CHAIN', 'gemini-fallback,deepseek,openai,anthropic,xai'),
    ],

    'dashboard' => [
        'provider' => env('AI_DASHBOARD_PROVIDER') ?: null,
        'model' => env('AI_DASHBOARD_MODEL') ?: null,

        // Messages of history re-sent to the model on every turn.
        'history' => (int) env('AI_DASHBOARD_HISTORY', 40),

        // Prompts per admin per minute.
        'per_minute' => (int) env('AI_DASHBOARD_PER_MINUTE', 20),
    ],

    'support' => [
        // Master switch; the dashboard toggle (setting ai_support_enabled) must also be on.
        'enabled' => (bool) env('AI_SUPPORT_ENABLED', true),

        'provider' => env('AI_SUPPORT_PROVIDER') ?: null,
        'model' => env('AI_SUPPORT_MODEL') ?: null,

        'history' => (int) env('AI_SUPPORT_HISTORY', 30),

        'max_message_length' => (int) env('AI_SUPPORT_MAX_MESSAGE_LENGTH', 1500),

        // Abuse limits: per-IP throttles and a hard cap per chat session.
        'per_minute' => (int) env('AI_SUPPORT_PER_MINUTE', 8),
        'per_day' => (int) env('AI_SUPPORT_PER_DAY', 150),
        'max_messages_per_session' => (int) env('AI_SUPPORT_MAX_MESSAGES_PER_SESSION', 60),
    ],

    'whatsapp' => [
        'enabled' => (bool) env('WHATSAPP_ENABLED', true),
        'public_number' => env('WHATSAPP_PUBLIC_NUMBER'),
        'prefill_message' => env('WHATSAPP_PREFILL_MESSAGE', 'مرحباً Empetix، أريد الاستفسار عن خدماتكم.'),
        'base_url' => rtrim((string) env('WHATSAPP_BASE_URL', 'https://graph.facebook.com'), '/'),
        'api_version' => env('WHATSAPP_API_VERSION', 'v22.0'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'business_account_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),
        'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
        'verify_token' => env('WHATSAPP_VERIFY_TOKEN'),
        'app_secret' => env('WHATSAPP_APP_SECRET'),

        // Set true in production when running `php artisan queue:work`.
        'queue_webhooks' => (bool) env('WHATSAPP_QUEUE_WEBHOOKS', false),
        'mark_as_read' => (bool) env('WHATSAPP_MARK_AS_READ', true),

        // Email notifications when a new WhatsApp message arrives and goes unseen.
        'notify_unread_email' => (bool) env('WHATSAPP_NOTIFY_UNREAD_EMAIL', true),
        'notify_email_to' => env('WHATSAPP_NOTIFY_EMAIL_TO'),
        'unread_delay_seconds' => (int) env('WHATSAPP_UNREAD_DELAY_SECONDS', 90),
    ],

];
