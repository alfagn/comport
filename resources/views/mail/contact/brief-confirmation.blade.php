<x-mail::message>
# Thank you, {{ $contactMessage->name }}.

Your project brief has been delivered to our co-founders at **{{ $siteName }}**. A partner will review your requirements and reply within **24 business hours**.

@if($servicesList || $contactMessage->budget_tier || $contactMessage->timeline)
<x-mail::panel>
@if($servicesList)
**Capabilities:** {{ $servicesList }}<br>
@endif
@if($contactMessage->budget_tier)
**Budget Tier:** {{ $contactMessage->budget_tier }}<br>
@endif
@if($contactMessage->timeline)
**Target Timeline:** {{ $contactMessage->timeline }}
@endif
</x-mail::panel>
@endif

If you would like to share additional documents or context before our call, simply reply directly to this email.

<x-mail::button :url="$websiteUrl">
Explore Selected Work
</x-mail::button>

Warm regards,<br>
**{{ $siteName }}**
</x-mail::message>
