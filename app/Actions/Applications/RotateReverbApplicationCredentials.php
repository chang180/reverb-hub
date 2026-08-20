<?php

namespace App\Actions\Applications;

use App\Models\ReverbApplication;

class RotateReverbApplicationCredentials
{
    /**
     * @return array{application: ReverbApplication, credentials: array{app_id: string, key: string, secret: string}}
     */
    public function handle(ReverbApplication $application): array
    {
        $credentials = $application->assignNewCredentials();
        $application->save();

        return [
            'application' => $application,
            'credentials' => $credentials,
        ];
    }
}
