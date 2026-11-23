@props([
    'eyebrow',
    'title',
    'subtitle' => null,
    'align' => 'center',
])

<div {{ $attributes->class(['section-heading', 'section-heading-left' => $align === 'left']) }}>
    <p class="section-eyebrow">{{ $eyebrow }}</p>
    <h2>{{ $title }}</h2>
    @if ($subtitle)
        <p class="section-subtitle">{{ $subtitle }}</p>
    @endif
</div>
