<div class="flex w-full flex-col gap-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ $application->name }}</flux:heading>
            <flux:text class="mt-1 font-mono text-sm">
                {{ __('App ID') }}: {{ $application->app_id }} · {{ __('Key') }}: {{ $application->key }}
            </flux:text>
        </div>
        <div class="flex gap-2">
            <flux:button wire:click="refresh" size="sm">{{ __('Refresh') }}</flux:button>
            <flux:button :href="route('applications.index')" wire:navigate size="sm">{{ __('Back') }}</flux:button>
        </div>
    </div>

    @if ($error)
        <flux:callout variant="danger" icon="exclamation-triangle">
            <flux:callout.heading>{{ __('Cannot reach Reverb') }}</flux:callout.heading>
            <flux:callout.text>{{ $error }}</flux:callout.text>
        </flux:callout>
    @endif

    <flux:card>
        <flux:heading class="mb-4">{{ __('Occupied channels') }}</flux:heading>

        @if ($channels === [])
            <flux:text>{{ __('No occupied channels right now.') }}</flux:text>
        @else
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Channel') }}</flux:table.column>
                    <flux:table.column>{{ __('Subscriptions') }}</flux:table.column>
                    <flux:table.column>{{ __('Users') }}</flux:table.column>
                    <flux:table.column />
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($channels as $name => $info)
                        <flux:table.row wire:key="channel-{{ $name }}">
                            <flux:table.cell class="font-mono text-sm">{{ $name }}</flux:table.cell>
                            <flux:table.cell>{{ $info['subscription_count'] ?? '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $info['user_count'] ?? '—' }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:button size="xs" wire:click="inspect({{ \Illuminate\Support\Js::from($name) }})">
                                    {{ __('Inspect') }}
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif
    </flux:card>

    @if ($selectedChannel)
        <flux:card>
            <flux:heading class="mb-4">{{ __('Presence users') }}: {{ $selectedChannel }}</flux:heading>
            @if ($users === [])
                <flux:text>{{ __('No presence users on this channel.') }}</flux:text>
            @else
                <ul class="space-y-1 font-mono text-sm">
                    @foreach ($users as $user)
                        <li>{{ $user['id'] ?? json_encode($user) }}</li>
                    @endforeach
                </ul>
            @endif
        </flux:card>
    @endif
</div>
