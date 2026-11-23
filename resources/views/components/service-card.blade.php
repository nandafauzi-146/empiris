@props([
    'title',
    'description',
    'number',
    'href' => null,
    'icon' => 'map',
])

<article class="service-card">
    <span class="service-card-icon" aria-hidden="true">
        @switch($icon)
            @case('calendar')
                <svg viewBox="0 0 24 24"><path d="M7 2v3M17 2v3M3.5 9h17M5 4h14a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/></svg>
                @break
            @case('gamepad')
                <svg viewBox="0 0 24 24"><path d="M7 16H5.5A3.5 3.5 0 0 1 2 12.5v-1A3.5 3.5 0 0 1 5.5 8h13A3.5 3.5 0 0 1 22 11.5v1a3.5 3.5 0 0 1-3.5 3.5H17l-2 3h-6l-2-3ZM7 11v4M5 13h4M16.5 12h.01M18.5 14h.01"/></svg>
                @break
            @case('bus')
                <svg viewBox="0 0 24 24"><path d="M5 16V6a3 3 0 0 1 3-3h8a3 3 0 0 1 3 3v10M3 16h18M5 16v3M19 16v3M7 19h10M7 7h10v5H7z"/></svg>
                @break
            @case('users')
                <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                @break
            @default
                <svg viewBox="0 0 24 24"><path d="M3 6l6-3 6 3 6-3v15l-6 3-6-3-6 3V6ZM9 3v15M15 6v15"/></svg>
        @endswitch
    </span>
    <span class="service-card-number" aria-hidden="true">{{ str_pad((string) $number, 2, '0', STR_PAD_LEFT) }}</span>
    <h3>{{ $title }}</h3>
    <p>{{ $description }}</p>
    @if ($href)
        <a class="text-link" href="{{ $href }}">Pelajari layanan <span aria-hidden="true">→</span></a>
    @endif
</article>
