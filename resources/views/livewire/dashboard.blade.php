<div class="flex w-full flex-col gap-8">
    <div class="overflow-hidden rounded-3xl border border-zinc-200 bg-linear-to-br from-slate-950 via-slate-900 to-cyan-950 p-6 text-white dark:border-zinc-700 md:p-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-medium uppercase tracking-[0.2em] text-cyan-300">{{ __('Overview') }}</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight">{{ __('Reverb Hub') }}</h1>
                <p class="mt-2 max-w-xl text-sm text-slate-300">{{ __('Manage broadcast credentials and watch live channels.') }}</p>
            </div>
            <span class="rounded-full px-3 py-1 text-sm font-medium {{ $reverbOnline ? 'bg-emerald-400/15 text-emerald-300' : 'bg-rose-400/15 text-rose-300' }}">
                {{ $reverbOnline ? __('Reverb online') : __('Reverb unreachable') }}
            </span>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm text-zinc-500">{{ __('Applications') }}</p>
            <p class="mt-2 text-3xl font-semibold">{{ $applications->count() }}</p>
        </div>
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm text-zinc-500">{{ __('Enabled') }}</p>
            <p class="mt-2 text-3xl font-semibold text-emerald-500">{{ $applications->where('enabled', true)->count() }}</p>
        </div>
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm text-zinc-500">{{ __('Disabled') }}</p>
            <p class="mt-2 text-3xl font-semibold">{{ $applications->where('enabled', false)->count() }}</p>
        </div>
    </div>

    <div class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="mb-5 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold">{{ __('Recent applications') }}</h2>
                <p class="text-sm text-zinc-500">{{ __('Issue isolated Reverb credentials for each client project.') }}</p>
            </div>
            <flux:button :href="route('applications.index')" wire:navigate size="sm" variant="primary">
                {{ __('Manage apps') }}
            </flux:button>
        </div>

        @if ($applications->isEmpty())
            <div class="rounded-xl border border-dashed border-zinc-300 px-6 py-12 text-center dark:border-zinc-700">
                <p class="font-medium">{{ __('Get started with your first application') }}</p>
                <p class="mt-1 text-sm text-zinc-500">{{ __('No applications yet. Create one to issue App ID, Key, and Secret.') }}</p>
                <flux:button class="mt-4" :href="route('applications.index')" wire:navigate variant="primary">
                    {{ __('Create an app') }}
                </flux:button>
            </div>
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
    </div>
</div>
