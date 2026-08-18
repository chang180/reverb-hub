<x-layouts::marketing>
    <div class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(34,211,238,0.16),transparent_32%),radial-gradient(circle_at_bottom_right,rgba(99,102,241,0.14),transparent_36%)]"></div>
        <div class="pointer-events-none absolute inset-0 opacity-25 [background-image:linear-gradient(rgba(148,163,184,0.1)_1px,transparent_1px),linear-gradient(90deg,rgba(148,163,184,0.1)_1px,transparent_1px)] [background-size:56px_56px]"></div>

        <header class="relative z-10 mx-auto flex max-w-6xl items-center justify-between px-6 py-6">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-xl bg-cyan-400 text-slate-950">
                    <x-app-logo-icon class="size-6" />
                </span>
                <span class="text-lg font-semibold tracking-tight">{{ config('app.name') }}</span>
            </a>

            <div class="flex items-center gap-3">
                <x-locale-switcher />
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-full bg-white px-4 py-2 text-sm font-medium text-slate-950 hover:bg-cyan-300" wire:navigate>
                        {{ __('Dashboard') }}
                    </a>
                @endauth
            </div>
        </header>

        <main class="relative z-10 mx-auto max-w-6xl px-6 pb-24 pt-10 lg:pt-20">
            <div class="max-w-3xl space-y-6">
                <p class="text-sm font-medium uppercase tracking-[0.22em] text-cyan-300">{{ __('A private Reverb control plane') }}</p>
                <h1 class="text-4xl font-semibold tracking-tight text-white sm:text-6xl sm:leading-[1.05]">
                    {{ __('Broadcast infrastructure for Laravel apps') }}
                </h1>
                <p class="max-w-2xl text-lg leading-relaxed text-slate-300">
                    {{ __('One Reverb host. Many applications. Credentials, channels, and occupancy in one place — without running WebSockets on shared hosting.') }}
                </p>
                @auth
                    <div class="flex flex-wrap gap-3 pt-2">
                        <a href="{{ route('dashboard') }}" class="rounded-full bg-cyan-400 px-5 py-2.5 text-sm font-semibold text-slate-950 hover:bg-cyan-300" wire:navigate>
                            {{ __('Open the console') }}
                        </a>
                    </div>
                @endauth
            </div>

            <section class="mt-20 grid gap-4 md:grid-cols-3">
                <article class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-sm">
                    <h2 class="text-lg font-semibold text-white">{{ __('Isolated apps') }}</h2>
                    <p class="mt-2 text-sm leading-relaxed text-slate-300">{{ __('Each client project gets its own App ID, Key, and Secret, with origin allowlists.') }}</p>
                </article>
                <article class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-sm">
                    <h2 class="text-lg font-semibold text-white">{{ __('Live occupancy') }}</h2>
                    <p class="mt-2 text-sm leading-relaxed text-slate-300">{{ __('See occupied channels, subscription counts, and presence users as they happen.') }}</p>
                </article>
                <article class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-sm">
                    <h2 class="text-lg font-semibold text-white">{{ __('Shared hosting friendly') }}</h2>
                    <p class="mt-2 text-sm leading-relaxed text-slate-300">{{ __('Keep Laravel on Hostinger. Point Echo and broadcasting at this VPS.') }}</p>
                </article>
            </section>
        </main>
    </div>
</x-layouts::marketing>
