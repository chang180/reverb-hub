<?php

namespace App\Actions\Applications;

use App\Models\ReverbApplication;

class CreateReverbApplication
{
    public function __construct(private ParseAllowedOrigins $parseAllowedOrigins) {}

    /**
     * @return array{application: ReverbApplication, credentials: array{app_id: string, key: string, secret: string}}
     */
    public function handle(string $name, string $allowedOrigins): array
    {
        $application = new ReverbApplication;
        $credentials = $application->assignNewCredentials();
        $application->name = $name;
        $application->allowed_origins = ($this->parseAllowedOrigins)($allowedOrigins);
        $application->enabled = true;
        $application->save();

        return [
            'application' => $application,
            'credentials' => $credentials,
        ];
    }
}
