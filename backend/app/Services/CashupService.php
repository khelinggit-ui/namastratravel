<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CashupService
{
    public function login(): string
    {
        return Cache::remember('cashup.jwt', now()->addMinutes(5), function () {
            $timestamp = (string) round(microtime(true) * 1000);
            $passwordHash = md5((string) config('services.cashup.password'));
            $passHash = hash('sha256', $timestamp . $passwordHash);

            $response = $this->client()->post($this->url('/MmCorePsgsHost/v1/login'), [
                'device_timestamp' => $timestamp,
                'pass_hash' => $passHash,
                'username' => config('services.cashup.username'),
            ]);

            $body = $this->decode($response);
            $token = $body['token'] ?? null;

            if (!$token || ($body['response_code'] ?? null) !== '0000') {
                throw new RuntimeException($body['message'] ?? 'CashUP login gagal.');
            }

            return $token;
        });
    }

    public function generateLink(array $payload): array
    {
        $timestamp = (string) time();
        $payload = array_merge([
            'device_id' => config('services.cashup.device_id'),
            'device_timestamp' => $timestamp,
            'currency' => 'IDR',
            'callback_url' => config('services.cashup.callback_url'),
            'redirect_url' => config('services.cashup.redirect_url'),
        ], $payload);

        $response = $this->authorized()->post($this->url('/MmCoreCzLinkHost/api/v1/generate'), $payload);
        return $this->decode($response);
    }

    public function checkStatus(string $orderId): array
    {
        $response = $this->authorized()->get($this->url('/MmCoreCzLinkHost/internal/payment/status/' . rawurlencode($orderId)));
        return $this->decode($response);
    }

    private function authorized()
    {
        $token = $this->login();
        $scheme = trim((string) config('services.cashup.auth_scheme'));
        $authorization = $scheme ? $scheme . ' ' . $token : $token;

        return $this->client()->withHeaders(['Authorization' => $authorization]);
    }

    private function client()
    {
        return Http::acceptJson()->asJson()->timeout(30);
    }

    private function url(string $path): string
    {
        return rtrim((string) config('services.cashup.base_url'), '/') . $path;
    }

    private function decode($response): array
    {
        if ($response->failed()) {
            throw new RuntimeException('CashUP mengembalikan HTTP ' . $response->status() . '.');
        }

        $body = $response->json();
        if (!is_array($body)) {
            throw new RuntimeException('Response CashUP bukan JSON yang valid.');
        }

        return $body;
    }
}
