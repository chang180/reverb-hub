<?php

namespace App\Actions\Applications;

use Illuminate\Support\Str;

class ParseAllowedOrigins
{
    /**
     * @return list<string>
     */
    public function __invoke(string $value): array
    {
        $origins = collect(preg_split('/[\s,]+/', $value) ?: [])
            ->map(fn (string $origin): string => trim($origin))
            ->filter()
            ->map(fn (string $origin): string => $this->normalize($origin))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return array_values($origins === [] ? ['*'] : $origins);
    }

    /**
     * Reverb compares connections' Origin header host-only (parse_url(..., PHP_URL_HOST)),
     * so stored origins must be bare hostnames/patterns — not full URLs with a scheme,
     * port, or path — or they will never match.
     */
    private function normalize(string $origin): string
    {
        if ($origin === '*') {
            return '*';
        }

        $host = parse_url(str_contains($origin, '://') ? $origin : '//'.$origin, PHP_URL_HOST);

        return $host !== null && $host !== false ? Str::lower($host) : Str::lower($origin);
    }
}
