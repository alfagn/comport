<x-mail::message>
# New Project Brief Received

A new inquiry was submitted through the **{{ $siteName }}** Contact Us form. You can hit **Reply** directly in Outlook or your email client to respond to **{{ $contactMessage->name }}**.

<x-mail::table>
| Field | Details |
| :--- | :--- |
| **Name** | {{ $contactMessage->name }} |
| **Email** | {{ $contactMessage->email }} |
| **Company** | {{ $contactMessage->company ?: '—' }} |
| **Capabilities** | {{ $servicesList }} |
| **Budget Tier** | {{ $contactMessage->budget_tier ?: '—' }} |
| **Timeline** | {{ $contactMessage->timeline ?: '—' }} |
</x-mail::table>

## Project Brief

<x-mail::panel>
{{ $contactMessage->brief }}
</x-mail::panel>

<x-mail::button :url="$inboxUrl">
Open Brief in Dashboard
</x-mail::button>

Regards,<br>
{{ $siteName }} Website
</x-mail::message>
