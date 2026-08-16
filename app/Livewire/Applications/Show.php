<?php

namespace App\Livewire\Applications;

use App\Models\ReverbApplication;
use App\Services\ReverbApiClient;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use RuntimeException;

#[Title('Application channels')]
class Show extends Component
{
    public ReverbApplication $application;

    public ?string $selectedChannel = null;

    /** @var array<string, array<string, mixed>> */
    public array $channels = [];

    /** @var list<array<string, mixed>> */
    public array $users = [];

    public ?string $error = null;

    public function mount(ReverbApplication $application, ReverbApiClient $reverb): void
    {
        $this->application = $application;
        $this->refresh($reverb);
    }

    public function refresh(ReverbApiClient $reverb): void
    {
        $this->error = null;

        try {
            $this->channels = $reverb->channels($this->application);

            if ($this->selectedChannel) {
                $this->users = $reverb->channelUsers($this->application, $this->selectedChannel);
            }
        } catch (RuntimeException $exception) {
            $this->error = $exception->getMessage();
            $this->channels = [];
            $this->users = [];
        }
    }

    public function inspect(string $channel, ReverbApiClient $reverb): void
    {
        $this->selectedChannel = $channel;
        $this->refresh($reverb);
    }

    public function render(): View
    {
        return view('livewire.applications.show');
    }
}
