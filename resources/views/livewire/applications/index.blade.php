<div class="flex w-full flex-col gap-8">
    <div>
        <flux:heading size="xl">{{ __('Applications') }}</flux:heading>
        <flux:text class="mt-1">{{ __('Issue isolated Reverb credentials for each client project.') }}</flux:text>
    </div>

    @if (session('plain_secret'))
        <flux:callout variant="warning" icon="key">
            <flux:callout.heading>{{ __('Copy this secret now') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('It is stored encrypted and will not be shown again.') }}
                <code class="mt-2 block break-all font-mono text-sm">{{ session('plain_secret') }}</code>
            </flux:callout.text>
        </flux:callout>
    @endif

    <flux:card>
        <flux:heading class="mb-4">{{ __('Create application') }}</flux:heading>
        <form wire:submit="create" class="grid gap-4 md:grid-cols-2">
            <flux:input wire:model="name" :label="__('Name')" required placeholder="shop.example.com" />
            <flux:input
                wire:model="allowedOrigins"
                :label="__('Allowed origins')"
                :description="__('Comma-separated hosts, or * for any origin.')"
                placeholder="https://example.com, *"
            />
            <div class="md:col-span-2">
                <flux:button type="submit" variant="primary">{{ __('Create') }}</flux:button>
            </div>
        </form>
    </flux:card>

    <flux:card>
        @if ($applications->isEmpty())
            <div class="rounded-xl border border-dashed border-zinc-300 px-6 py-10 text-center dark:border-zinc-700">
                <p class="font-medium">{{ __('Get started with your first application') }}</p>
                <p class="mt-1 text-sm text-zinc-500">{{ __('No applications yet.') }}</p>
            </div>
        @else
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Name') }}</flux:table.column>
                    <flux:table.column>{{ __('App ID') }}</flux:table.column>
                    <flux:table.column>{{ __('Key') }}</flux:table.column>
                    <flux:table.column>{{ __('Origins') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column />
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($applications as $application)
                        <flux:table.row wire:key="app-{{ $application->id }}">
                            <flux:table.cell>
                                <flux:link :href="route('applications.show', $application)" wire:navigate>
                                    {{ $application->name }}
                                </flux:link>
                            </flux:table.cell>
                            <flux:table.cell class="font-mono text-sm">{{ $application->app_id }}</flux:table.cell>
                            <flux:table.cell class="font-mono text-sm">{{ $application->key }}</flux:table.cell>
                            <flux:table.cell class="text-sm">{{ implode(', ', $application->allowed_origins) }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$application->enabled ? 'green' : 'zinc'" size="sm">
                                    {{ $application->enabled ? __('Enabled') : __('Disabled') }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-wrap justify-end gap-2">
                                    <flux:button size="xs" wire:click="toggle({{ $application->id }})">
                                        {{ $application->enabled ? __('Disable') : __('Enable') }}
                                    </flux:button>
                                    <flux:button size="xs" wire:click="rotate({{ $application->id }})" wire:confirm="{{ __('Rotate credentials? Client apps must be updated.') }}">
                                        {{ __('Rotate keys') }}
                                    </flux:button>
                                    <flux:button size="xs" variant="danger" wire:click="delete({{ $application->id }})" wire:confirm="{{ __('Delete this application?') }}">
                                        {{ __('Delete') }}
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif
    </flux:card>
</div>
