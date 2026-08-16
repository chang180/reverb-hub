<?php

namespace App\Reverb;

use App\Models\ReverbApplication;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Laravel\Reverb\Application;
use Laravel\Reverb\Contracts\ApplicationProvider;
use Laravel\Reverb\Exceptions\InvalidApplication;

class DatabaseApplicationProvider implements ApplicationProvider
{
    /**
     * @return Collection<int, Application>
     */
    public function all(): Collection
    {
        /** @var Collection<int, Application> */
        return Cache::remember('reverb.apps.all', 30, function () {
            return ReverbApplication::query()
                ->enabled()
                ->get()
                ->map(fn (ReverbApplication $application): Application => $application->toReverbApplication())
                ->values();
        });
    }

    public function findById(string $id): Application
    {
        $application = ReverbApplication::query()
            ->enabled()
            ->where('app_id', $id)
            ->first();

        if (! $application) {
            throw new InvalidApplication;
        }

        return $application->toReverbApplication();
    }

    public function findByKey(string $key): Application
    {
        $application = ReverbApplication::query()
            ->enabled()
            ->where('key', $key)
            ->first();

        if (! $application) {
            throw new InvalidApplication;
        }

        return $application->toReverbApplication();
    }
}
