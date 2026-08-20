<?php

namespace App\Livewire\Applications;

use App\Actions\Applications\CreateReverbApplication;
use App\Actions\Applications\DeleteReverbApplication;
use App\Actions\Applications\RotateReverbApplicationCredentials;
use App\Actions\Applications\ToggleReverbApplication;
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

    public function create(CreateReverbApplication $createReverbApplication): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'allowedOrigins' => ['required', 'string', 'max:2000'],
        ]);

        $result = $createReverbApplication->handle(
            $validated['name'],
            $validated['allowedOrigins'],
        );

        session()->flash('plain_secret', $result['credentials']['secret']);
        session()->flash('created_app_id', $result['application']->id);

        $this->reset('name', 'allowedOrigins');
        $this->allowedOrigins = '*';

        Flux::toast(variant: 'success', text: __('Application created. Copy the secret now — it will not be shown again.'));
    }

    public function toggle(int $id, ToggleReverbApplication $toggleReverbApplication): void
    {
        $application = ReverbApplication::query()->findOrFail($id);
        $toggleReverbApplication->handle($application);

        Flux::toast(text: $application->refresh()->enabled ? __('Application enabled.') : __('Application disabled.'));
    }

    public function rotate(int $id, RotateReverbApplicationCredentials $rotateReverbApplicationCredentials): void
    {
        $application = ReverbApplication::query()->findOrFail($id);
        $result = $rotateReverbApplicationCredentials->handle($application);

        session()->flash('plain_secret', $result['credentials']['secret']);
        session()->flash('created_app_id', $application->id);

        Flux::toast(variant: 'warning', text: __('Credentials rotated. Update client apps, then restart Reverb.'));
    }

    public function delete(int $id, DeleteReverbApplication $deleteReverbApplication): void
    {
        $application = ReverbApplication::query()->findOrFail($id);
        $deleteReverbApplication->handle($application);

        Flux::toast(text: __('Application deleted.'));
    }

    public function render(): View
    {
        return view('livewire.applications.index', [
            'applications' => ReverbApplication::query()->latest()->get(),
        ]);
    }
}
