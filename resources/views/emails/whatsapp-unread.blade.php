<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Unread WhatsApp Message</title>
</head>
<body style="margin:0;padding:28px 14px;background:#F3F4F6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#111827;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px;margin:0 auto;background:#FFFFFF;border-radius:18px;overflow:hidden;border:1px solid #E5E7EB;box-shadow:0 12px 32px rgba(15,23,42,0.08);">
    <tr>
      <td style="padding:22px 26px;background:linear-gradient(135deg,#075E54 0%,#128C7E 100%);color:#FFFFFF;">
        <div style="font-size:11px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;opacity:0.85;margin-bottom:4px;">WhatsApp Business Inbox Alert</div>
        <h1 style="margin:0;font-size:20px;font-weight:800;line-height:1.3;">
          {{ $unreadMessages->count() }} Unread {{ \Illuminate\Support\Str::plural('Message', $unreadMessages->count()) }} from {{ $session->visitorLabel() }}
        </h1>
        @if($session->visitor_phone)
          <div style="margin-top:4px;font-size:13px;opacity:0.9;">Phone: {{ $session->visitor_phone }}</div>
        @endif
      </td>
    </tr>

    <tr>
      <td style="padding:24px 26px;background:#EFEAE2;">
        @foreach($unreadMessages as $msg)
          <div style="background:#FFFFFF;border-radius:12px;border-top-left-radius:3px;padding:12px 15px;margin-bottom:10px;box-shadow:0 2px 6px rgba(0,0,0,0.05);">
            <div style="font-size:12px;font-weight:700;color:#075E54;margin-bottom:4px;">
              {{ $session->visitorLabel() }}
              <span style="font-weight:400;color:#6B7280;">&middot; {{ $msg->created_at?->format('d M Y, H:i') }}</span>
            </div>
            <div dir="auto" style="font-size:14px;line-height:1.55;color:#111827;white-space:pre-line;">{{ $msg->previewLabel() }}</div>
          </div>
        @endforeach
      </td>
    </tr>

    <tr>
      <td style="padding:22px 26px;text-align:center;background:#FFFFFF;border-top:1px solid #E5E7EB;">
        <a href="{{ $inboxUrl }}" style="display:inline-block;padding:12px 26px;border-radius:999px;background:#25D366;color:#FFFFFF;font-size:14px;font-weight:700;text-decoration:none;">
          Open Conversation in WhatsApp Inbox &rarr;
        </a>
        <p style="margin:14px 0 0;font-size:12px;color:#6B7280;">
          You received this email because a new WhatsApp message arrived while this conversation was not open in the dashboard.
        </p>
      </td>
    </tr>
  </table>
</body>
</html>
