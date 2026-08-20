<?php

namespace App\Livewire\Settings;

use App\Actions\HubApiKeys\CreateHubApiKey;
use App\Actions\HubApiKeys\RevokeHubApiKey;
use App\Enums\HubApiKeyPreset;
use App\Models\HubApiKey;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('API Keys')]
class ApiKeys extends Component
{
    public string $name = '';

    public string $preset = 'basic';

    #[Locked]
    public ?string $plainToken = null;

    public function create(CreateHubApiKey $createHubApiKey): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'preset' => ['required', 'in:basic,read,manage'],
        ]);

        $result = $createHubApiKey->handle(
            Auth::user(),
            $validated['name'],
            HubApiKeyPreset::from($validated['preset']),
        );

        $this->plainToken = $result['plain'];
        $this->reset('name');
        $this->preset = 'basic';

        Flux::toast(variant: 'success', text: __('API key created. Copy it now — it will not be shown again.'));
    }

    public function revoke(int $id, RevokeHubApiKey $revokeHubApiKey): void
    {
        $apiKey = Auth::user()->hubApiKeys()->findOrFail($id);

        if ($apiKey->isRevoked()) {
            return;
        }

        $revokeHubApiKey->handle($apiKey);

        Flux::toast(text: __('API key revoked.'));
    }

    public function dismissPlainToken(): void
    {
        $this->plainToken = null;
    }

    /**
     * @return list<array{id: int, name: string, prefix: string, preset: string, last_used_at: string|null, created_at: string, revoked: bool}>
     */
    public function apiKeys(): array
    {
        return Auth::user()
            ->hubApiKeys()
            ->latest()
            ->get()
            ->map(function (HubApiKey $apiKey): array {
                $preset = collect(HubApiKeyPreset::cases())
                    ->first(fn (HubApiKeyPreset $candidate): bool => $candidate->abilities() === $apiKey->abilities);

                return [
                    'id' => $apiKey->id,
                    'name' => $apiKey->name,
                    'prefix' => $apiKey->prefix,
                    'preset' => $preset?->label() ?? __('Custom'),
                    'last_used_at' => $apiKey->last_used_at?->diffForHumans(),
                    'created_at' => $apiKey->created_at->diffForHumans(),
                    'revoked' => $apiKey->isRevoked(),
                ];
            })
            ->all();
    }

    public function render(): View
    {
        return view('livewire.settings.api-keys', [
            'keys' => $this->apiKeys(),
            'presets' => HubApiKeyPreset::cases(),
        ]);
    }
}
