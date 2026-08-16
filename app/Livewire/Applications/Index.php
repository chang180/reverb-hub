<?php

namespace App\Livewire\Applications;

use App\Models\ReverbApplication;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Applications')]
class Index extends Component
{
    public string $name = '';

    public string $allowedOrigins = '*';

    public function create(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'allowedOrigins' => ['required', 'string', 'max:2000'],
        ]);

        $application = new ReverbApplication;
        $credentials = $application->assignNewCredentials();
        $application->name = $validated['name'];
        $application->allowed_origins = $this->parseOrigins($validated['allowedOrigins']);
        $application->enabled = true;
        $application->save();

        session()->flash('plain_secret', $credentials['secret']);
        session()->flash('created_app_id', $application->id);

        $this->reset('name', 'allowedOrigins');
        $this->allowedOrigins = '*';

        Flux::toast(variant: 'success', text: __('Application created. Copy the secret now — it will not be shown again.'));
    }

    public function toggle(int $id): void
    {
        $application = ReverbApplication::query()->findOrFail($id);
        $application->enabled = ! $application->enabled;
        $application->save();

        Flux::toast(text: $application->enabled ? __('Application enabled.') : __('Application disabled.'));
    }

    public function rotate(int $id): void
    {
        $application = ReverbApplication::query()->findOrFail($id);
        $credentials = $application->assignNewCredentials();
        $application->save();

        session()->flash('plain_secret', $credentials['secret']);
        session()->flash('created_app_id', $application->id);

        Flux::toast(variant: 'warning', text: __('Credentials rotated. Update client apps, then restart Reverb.'));
    }

    public function delete(int $id): void
    {
        ReverbApplication::query()->findOrFail($id)->delete();

        Flux::toast(text: __('Application deleted.'));
    }

    public function render(): View
    {
        return view('livewire.applications.index', [
            'applications' => ReverbApplication::query()->latest()->get(),
        ]);
    }

    /**
     * @return list<string>
     */
    protected function parseOrigins(string $value): array
    {
        $origins = collect(preg_split('/[\s,]+/', $value) ?: [])
            ->map(fn (string $origin): string => trim($origin))
            ->filter()
            ->values()
            ->all();

        return array_values($origins === [] ? ['*'] : $origins);
    }
}
