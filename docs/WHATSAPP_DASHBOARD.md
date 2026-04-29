# WhatsApp Dashboard

## Overview

This module adds a multi-agent WhatsApp dashboard inside the admin panel at `ye/admin/whatsapp`.

It includes:

- Conversations inbox with assignment and status management
- Polling-based real-time refresh every 3 seconds
- WhatsApp-like message thread UI
- Template management at `ye/admin/whatsapp/templates`
- Faalwa webhook endpoint at `POST /webhook/faalwa`

## Environment Variables

Add these values to your environment configuration:

```env
FAALWA_BASE_URL=https://api.faalwa.com
FAALWA_API_TOKEN=your_api_token
FAALWA_CHANNEL_ID=your_channel_id
FAALWA_TEXT_ENDPOINT=/messages/text
FAALWA_TEMPLATE_ENDPOINT=/messages/template
FAALWA_WEBHOOK_TOKEN=your_webhook_secret
```

## Migrations

Run:

```bash
php artisan migrate
php artisan route:clear
php artisan view:clear
```

The route clear step is important if route caching was enabled before deploying this feature.

## Example Webhook Payload

The webhook controller accepts payloads similar to this:

```json
{
  "token": "your_webhook_secret",
  "contact": {
    "name": "Ahmad Ali"
  },
  "message": {
    "id": "wamid.HBgL...",
    "from": "966500000000",
    "text": {
      "body": "Hello, I need help with my booking"
    }
  }
}
```

## Example Faalwa Request

Text message payload sent by `App\Services\FaalwaService`:

```json
{
  "to": "966500000000",
  "type": "text",
  "text": {
    "body": "Hello from support"
  }
}
```

Template message payload sent by `App\Services\FaalwaService`:

```json
{
  "to": "966500000000",
  "type": "template",
  "template": {
    "name": "welcome_template",
    "type": "utility",
    "body": "مرحبا بك في خدمة العملاء"
  }
}
```

## Permission

Grant `manage_whatsapp` to any admin or agent who should access the dashboard.

## Notes

- Agents can be created using the `agent` role.
- The dashboard uses AJAX and the file `public/js/chat.js`.
- The UI styles are appended to `public/css/style.css`.