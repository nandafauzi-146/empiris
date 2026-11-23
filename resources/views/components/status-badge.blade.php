@props([
    'label',
    'variant' => 'open',
])

<span {{ $attributes->class([
    'status-badge inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold backdrop-blur-md',
    'status-badge-open' => $variant === 'open',
    'status-badge-featured' => $variant === 'featured',
    'status-badge-limited' => $variant === 'limited',
]) }}>
    <span class="size-1.5 rounded-full bg-current" aria-hidden="true"></span>
    {{ $label }}
</span>
