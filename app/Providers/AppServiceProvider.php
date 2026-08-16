<?php

namespace App\Providers;

use App\Reverb\DatabaseApplicationProvider;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Reverb\ApplicationManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureReverb();
        $this->configurePulse();
    }

    protected function configureReverb(): void
    {
        $this->app->make(ApplicationManager::class)->extend(
            'database',
            fn (): DatabaseApplicationProvider => new DatabaseApplicationProvider,
        );
    }

    protected function configurePulse(): void
    {
        $this->app->booted(function (): void {
            Gate::define('viewPulse', function (?Authenticatable $user): bool {
                return $user !== null;
            });
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
