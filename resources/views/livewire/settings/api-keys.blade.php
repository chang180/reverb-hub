<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('API Keys') }}</flux:heading>

    <x-settings.layout :heading="__('API Keys')" :subheading="__('Create keys for programmatic access to the Hub API')">
        @if ($plainToken)
            <flux:callout variant="warning" icon="key" class="mb-6">
                <flux:callout.heading>{{ __('Copy this API key now') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('It will not be shown again.') }}
                    <code class="mt-2 block break-all font-mono text-sm">{{ $plainToken }}</code>
                </flux:callout.text>
                <flux:button size="sm" class="mt-3" wire:click="dismissPlainToken">{{ __('Dismiss') }}</flux:button>
            </flux:callout>
        @endif

        <form wire:submit="create" class="space-y-4">
            <flux:input wire:model="name" :label="__('Name')" required placeholder="deploy-bot" />

            <flux:select wire:model="preset" :label="__('Permissions')">
                @foreach ($presets as $presetOption)
                    <flux:select.option value="{{ $presetOption->value }}">
                        {{ $presetOption->label() }} — {{ $presetOption->description() }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:button variant="primary" type="submit">{{ __('Create API key') }}</flux:button>
        </form>

        <div class="mt-10">
            <flux:heading size="lg">{{ __('Active keys') }}</flux:heading>

            @if ($keys === [])
                <flux:text class="mt-2">{{ __('No API keys yet.') }}</flux:text>
            @else
                <flux:table class="mt-4">
                    <flux:table.columns>
                        <flux:table.column>{{ __('Name') }}</flux:table.column>
                        <flux:table.column>{{ __('Prefix') }}</flux:table.column>
                        <flux:table.column>{{ __('Permissions') }}</flux:table.column>
                        <flux:table.column>{{ __('Last used') }}</flux:table.column>
                        <flux:table.column>{{ __('Created') }}</flux:table.column>
                        <flux:table.column />
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($keys as $key)
                            <flux:table.row wire:key="api-key-{{ $key['id'] }}">
                                <flux:table.cell>{{ $key['name'] }}</flux:table.cell>
                                <flux:table.cell class="font-mono text-sm">{{ $key['prefix'] }}…</flux:table.cell>
                                <flux:table.cell>{{ $key['preset'] }}</flux:table.cell>
                                <flux:table.cell>{{ $key['last_used_at'] ?? __('Never') }}</flux:table.cell>
                                <flux:table.cell>{{ $key['created_at'] }}</flux:table.cell>
                                <flux:table.cell>
                                    @if (! $key['revoked'])
                                        <flux:button
                                            size="xs"
                                            variant="danger"
                                            wire:click="revoke({{ $key['id'] }})"
                                            wire:confirm="{{ __('Revoke this API key? This cannot be undone.') }}"
                                        >
                                            {{ __('Revoke') }}
                                        </flux:button>
                                    @else
                                        <flux:badge color="zinc" size="sm">{{ __('Revoked') }}</flux:badge>
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @endif
        </div>
    </x-settings.layout>
</section>
