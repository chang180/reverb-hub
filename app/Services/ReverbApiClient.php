<?php

namespace App\Services;

use App\Models\ReverbApplication;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Pusher\ApiErrorException;
use Pusher\Pusher;
use Pusher\PusherException;
use RuntimeException;

class ReverbApiClient
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function channels(ReverbApplication $application): array
    {
        $payload = $this->get($application, '/channels', [
            'info' => 'subscription_count,user_count',
        ]);

        /** @var array<string, array<string, mixed>> */
        return is_array($payload['channels'] ?? null) ? $payload['channels'] : [];
    }

    /**
     * @return array<string, mixed>
     */
    public function channel(ReverbApplication $application, string $channel): array
    {
        /** @var array<string, mixed> */
        return $this->get($application, '/channels/'.rawurlencode($channel), [
            'info' => 'subscription_count,user_count,occupied',
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function channelUsers(ReverbApplication $application, string $channel): array
    {
        $payload = $this->get($application, '/channels/'.rawurlencode($channel).'/users');

        /** @var list<array<string, mixed>> */
        return is_array($payload['users'] ?? null) ? $payload['users'] : [];
    }

    public function ping(): bool
    {
        try {
            Http::timeout(1)->connectTimeout(1)->get((string) config('reverb-hub.api_url'));

            return true;
        } catch (ConnectionException) {
            return false;
        }
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    protected function get(ReverbApplication $application, string $path, array $params = []): array
    {
        try {
            $result = $this->pusher($application)->get($path, $params, true);
        } catch (ApiErrorException|PusherException $exception) {
            throw new RuntimeException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return is_array($result) ? $result : [];
    }

    protected function pusher(ReverbApplication $application): Pusher
    {
        $url = rtrim((string) config('reverb-hub.api_url'), '/');
        $parts = parse_url($url) ?: [];

        return new Pusher(
            $application->key,
            $application->secret,
            $application->app_id,
            [
                'host' => $parts['host'] ?? '127.0.0.1',
                'port' => (int) ($parts['port'] ?? (($parts['scheme'] ?? 'http') === 'https' ? 443 : 80)),
                'scheme' => $parts['scheme'] ?? 'http',
                'useTLS' => ($parts['scheme'] ?? 'http') === 'https',
                'timeout' => 5,
            ],
        );
    }
}
