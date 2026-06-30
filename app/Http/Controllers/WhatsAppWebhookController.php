<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    protected WhatsAppService $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * استقبال Webhook من Faalwa
     */
    public function handle(Request $request)
    {
        try {
            $payload = $request->all();

            Log::info('Faalwa Webhook Received', [
                'payload' => $payload,
            ]);

            $this->whatsappService->handleFaalwaWebhook($payload);

            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('Faalwa Webhook Handler Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response('Error', 500);
        }
    }
}
