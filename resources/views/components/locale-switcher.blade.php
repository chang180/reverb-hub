@php
    $locales = config('app.available_locales');
    $current = app()->getLocale();
@endphp

<div {{ $attributes->class('inline-flex rounded-full border border-white/15 bg-black/20 p-0.5 text-xs font-medium backdrop-blur-sm') }}>
    @foreach ($locales as $locale => $label)
        <form method="POST" action="{{ route('locale.update', $locale) }}">
            @csrf
            <button
                type="submit"
                class="rounded-full px-3 py-1.5 transition {{ $current === $locale ? 'bg-cyan-400 text-slate-950 shadow-sm' : 'text-slate-300 hover:text-white' }}"
            >
                {{ $locale === 'zh_TW' ? '繁中' : 'EN' }}
            </button>
        </form>
    @endforeach
</div>
