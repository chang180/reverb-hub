<div class="flex w-full flex-col gap-6">
    <div class="flex items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Reverb Hub') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Manage broadcast credentials and watch live channels.') }}</flux:text>
        </div>
        <flux:badge :color="$reverbOnline ? 'green' : 'red'" size="sm">
            {{ $reverbOnline ? __('Reverb online') : __('Reverb unreachable') }}
        </flux:badge>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <flux:card>
            <flux:heading size="lg">{{ $applications->count() }}</flux:heading>
            <flux:text>{{ __('Applications') }}</flux:text>
        </flux:card>
        <flux:card>
            <flux:heading size="lg">{{ $applications->where('enabled', true)->count() }}</flux:heading>
            <flux:text>{{ __('Enabled') }}</flux:text>
        </flux:card>
        <flux:card>
            <flux:heading size="lg">{{ $applications->where('enabled', false)->count() }}</flux:heading>
            <flux:text>{{ __('Disabled') }}</flux:text>
        </flux:card>
    </div>

    <flux:card>
        <div class="mb-4 flex items-center justify-between">
            <flux:heading>{{ __('Recent applications') }}</flux:heading>
            <flux:button :href="route('applications.index')" wire:navigate size="sm" variant="primary">
                {{ __('Manage apps') }}
            </flux:button>
        </div>

        @if ($applications->isEmpty())
            <flux:text>{{ __('No applications yet. Create one to issue App ID, Key, and Secret.') }}</flux:text>
        @else
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Name') }}</flux:table.column>
                    <flux:table.column>{{ __('App ID') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($applications->take(8) as $application)
                        <flux:table.row>
                            <flux:table.cell>
                                <flux:link :href="route('applications.show', $application)" wire:navigate>
                                    {{ $application->name }}
                                </flux:link>
                            </flux:table.cell>
                            <flux:table.cell class="font-mono text-sm">{{ $application->app_id }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$application->enabled ? 'green' : 'zinc'" size="sm">
                                    {{ $application->enabled ? __('Enabled') : __('Disabled') }}
                                </flux:badge>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif
    </flux:card>
</div>
