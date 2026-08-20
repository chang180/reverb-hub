<?php

namespace App\Actions\Applications;

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
            ->values()
            ->all();

        return array_values($origins === [] ? ['*'] : $origins);
    }
}
