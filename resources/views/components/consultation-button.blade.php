@props([
    'href' => '#kontak',
    'label' => 'Mulai Konsultasi',
    'compact' => false,
    'showArrow' => true,
])

<a {{ $attributes->merge([
    'href' => $href,
    'class' => 'consultation-button focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white',
]) }} aria-label="{{ $label }}">
    @if ($compact)
        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M20 11.5a7.5 7.5 0 0 1-7.5 7.5H6l-2 2v-6a7.5 7.5 0 1 1 16-3.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M8 11.5h8m-8 3h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        <span class="sr-only">{{ $label }}</span>
    @else
        {{ $label }}
        @if ($showArrow)
            <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        @endif
    @endif
</a>
