<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.leminai.api_key', env('LEMINAI_API_KEY', 'lmn_sk_RyTaE032nOFU2qDchxtpnNSPSocOKZJLPhyeqOd5'));
        $this->baseUrl = rtrim(config('services.leminai.base_url', env('LEMINAI_BASE_URL', 'https://app.leminai.com')), '/');
    }

    /**
     * Normalize phone number to standard international format without '+' or special characters.
     */
    public function normalizePhone(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        // If local Bangladeshi number (e.g. 01628398056), convert to 8801628398056
        if (str_starts_with($cleaned, '01') && strlen($cleaned) === 11) {
            $cleaned = '88' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Ensure contact is registered in LeminAI.
     */
    public function ensureContact(string $phone, string $name = 'Client'): ?array
    {
        $phone = $this->normalizePhone($phone);
        if (! $phone || ! $this->apiKey) {
            return null;
        }

        try {
            $res = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Accept' => 'application/json',
            ])->post("{$this->baseUrl}/api/v1/contacts", [
                'name' => $name,
                'phone' => $phone,
            ]);

            return $res->json();
        } catch (\Throwable $e) {
            Log::warning("LeminAI ensureContact failed for {$phone}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Send WhatsApp message via LeminAI Wrapper API.
     */
    public function sendMessage(string $phone, string $message, string $name = 'Client'): array
    {
        $phone = $this->normalizePhone($phone);

        if (! $phone) {
            return ['status' => 'error', 'message' => 'Invalid phone number'];
        }

        if (! $this->apiKey) {
            return ['status' => 'error', 'message' => 'LeminAI API Key missing'];
        }

        // 1. Ensure contact is registered
        $this->ensureContact($phone, $name);

        // 2. Attempt sending service message
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post("{$this->baseUrl}/api/v1/messages/service", [
                'to' => $phone,
                'type' => 'text',
                'text' => [
                    'body' => $message,
                ],
            ]);

            $body = $response->json();

            $waMeUrl = $this->getDirectChatUrl($phone, $message);

            if ($response->successful()) {
                Log::info("LeminAI WhatsApp sent successfully to {$phone}", $body ?? []);
                return [
                    'status' => 'success',
                    'gateway' => 'leminai',
                    'data' => $body,
                    'wa_me_url' => $waMeUrl,
                ];
            }

            Log::warning("LeminAI WhatsApp dispatch response for {$phone}:", [
                'status' => $response->status(),
                'response' => $body,
            ]);

            return [
                'status' => 'failed',
                'gateway' => 'leminai',
                'http_status' => $response->status(),
                'error' => $body['error'] ?? 'unknown_error',
                'message' => $body['message'] ?? $response->body(),
                'wa_me_url' => $waMeUrl,
                'explanation' => ($body['error'] ?? '') === 'outside_session_window'
                    ? 'Recipient has not messaged the business WhatsApp within the last 24 hours. A template or user inbound message is required by Meta Cloud API.'
                    : null,
            ];
        } catch (\Throwable $e) {
            Log::error("LeminAI WhatsApp exception for {$phone}: " . $e->getMessage());
            return [
                'status' => 'error',
                'gateway' => 'leminai',
                'message' => $e->getMessage(),
                'wa_me_url' => $this->getDirectChatUrl($phone, $message),
            ];
        }
    }

    /**
     * Generate 1-click wa.me direct chat link with pre-filled message text.
     */
    public function getDirectChatUrl(string $phone, string $message): string
    {
        $normalized = $this->normalizePhone($phone);
        return 'https://wa.me/' . $normalized . '?text=' . rawurlencode($message);
    }

    /**
     * Optional dispatch via Evolution API (Baileys WhatsApp Web engine)
     * when Evolution container is active.
     */
    public function sendViaEvolution(string $phone, string $message): array
    {
        $phone = $this->normalizePhone($phone);
        $baseUrl = rtrim(env('EVOLUTION_API_URL', 'http://evolution_api:8080'), '/');
        $apiKey = env('EVOLUTION_API_KEY', 'vv_evolution_token_2026');
        $instance = env('EVOLUTION_INSTANCE_NAME', 'venture_visionary_main');

        try {
            $response = Http::withHeaders([
                'apikey' => $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(5)->post("{$baseUrl}/message/sendText/{$instance}", [
                'number' => $phone,
                'options' => [
                    'delay' => 1200,
                    'presence' => 'composing',
                ],
                'textMessage' => [
                    'text' => $message,
                ],
            ]);

            return [
                'status' => $response->successful() ? 'success' : 'failed',
                'gateway' => 'evolution',
                'http_status' => $response->status(),
                'data' => $response->json(),
                'wa_me_url' => $this->getDirectChatUrl($phone, $message),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'offline',
                'gateway' => 'evolution',
                'message' => $e->getMessage(),
                'wa_me_url' => $this->getDirectChatUrl($phone, $message),
            ];
        }
    }
}
