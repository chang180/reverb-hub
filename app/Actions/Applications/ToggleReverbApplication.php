<?php

namespace App\Actions\Applications;

use App\Models\ReverbApplication;

class ToggleReverbApplication
{
    public function handle(ReverbApplication $application): ReverbApplication
    {
        $application->enabled = ! $application->enabled;
        $application->save();

        return $application;
    }
}
