<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-svh bg-slate-950 text-slate-100 antialiased">
        <div class="grid min-h-svh lg:grid-cols-2">
            <aside class="relative hidden overflow-hidden bg-slate-950 lg:flex lg:flex-col lg:justify-between p-10">
                <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(34,211,238,0.18),transparent_40%),radial-gradient(circle_at_80%_80%,rgba(99,102,241,0.16),transparent_42%)]"></div>
                <div class="pointer-events-none absolute inset-0 opacity-30 [background-image:linear-gradient(rgba(148,163,184,0.12)_1px,transparent_1px),linear-gradient(90deg,rgba(148,163,184,0.12)_1px,transparent_1px)] [background-size:48px_48px]"></div>

                <a href="{{ route('home') }}" class="relative z-10 flex items-center gap-3" wire:navigate>
                    <span class="flex size-10 items-center justify-center rounded-xl bg-cyan-400 text-slate-950">
                        <x-app-logo-icon class="size-6" />
                    </span>
                    <span class="text-lg font-semibold tracking-tight">{{ config('app.name') }}</span>
                </a>

                <div class="relative z-10 max-w-md space-y-4">
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">{{ __('A private Reverb control plane') }}</p>
                    <h1 class="text-4xl font-semibold tracking-tight text-white">{{ __('Broadcast infrastructure for Laravel apps') }}</h1>
                    <p class="text-base leading-relaxed text-slate-300">{{ __('One Reverb host. Many applications. Credentials, channels, and occupancy in one place — without running WebSockets on shared hosting.') }}</p>
                </div>

                <p class="relative z-10 text-sm text-slate-500">{{ __('Credentials, channels, occupancy.') }}</p>
            </aside>

            <div class="relative flex min-h-svh flex-col bg-slate-900">
                <div class="flex items-center justify-between px-6 py-5 lg:justify-end">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 lg:hidden" wire:navigate>
                        <span class="flex size-9 items-center justify-center rounded-lg bg-cyan-400 text-slate-950">
                            <x-app-logo-icon class="size-5" />
                        </span>
                        <span class="font-semibold">{{ config('app.name') }}</span>
                    </a>
                    <x-locale-switcher />
                </div>

                <div class="flex flex-1 items-center justify-center px-6 pb-12">
                    <div class="w-full max-w-md">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
