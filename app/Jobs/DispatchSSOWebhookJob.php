<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchSSOWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

    protected $webhookUrl;

    protected $secret;

    protected $payload;

    /**
     * Create a new job instance.
     */
    public function __construct(string $webhookUrl, ?string $secret, array $payload)
    {
        $this->webhookUrl = $webhookUrl;
        $this->secret = $secret;
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $timestamp = time();
        $payloadWithMeta = array_merge($this->payload, [
            'timestamp' => $timestamp,
        ]);

        $jsonPayload = json_encode($payloadWithMeta);

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        if (! empty($this->secret)) {
            // Generate HMAC-SHA256 signature using the shared secret
            $signature = hash_hmac('sha256', $jsonPayload, $this->secret);
            $headers['X-SSO-Signature'] = $signature;
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout(5)
                ->post($this->webhookUrl, $payloadWithMeta);

            if ($response->failed()) {
                Log::warning("SSO Webhook failed to {$this->webhookUrl}. Status: {$response->status()}. Response: {$response->body()}");
                $this->fail(new \Exception("Webhook target returned non-2xx status code: {$response->status()}"));
            }
        } catch (\Exception $e) {
            Log::error("SSO Webhook request error to {$this->webhookUrl}: ".$e->getMessage());
            throw $e;
        }
    }
}
