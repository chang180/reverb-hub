<?php

namespace App\Livewire;

use App\Models\ReverbApplication;
use App\Services\ReverbApiClient;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render(ReverbApiClient $reverb): View
    {
        return view('livewire.dashboard', [
            'applications' => ReverbApplication::query()->latest()->get(),
            'reverbOnline' => $reverb->ping(),
        ]);
    }
}
