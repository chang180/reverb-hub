<?php

namespace App\Actions\Applications;

use App\Models\ReverbApplication;

class DeleteReverbApplication
{
    public function handle(ReverbApplication $application): void
    {
        $application->delete();
    }
}
