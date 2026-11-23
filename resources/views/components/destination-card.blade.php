@props([
    'name',
    'region',
    'image',
    'category' => '',
    'description' => '',
    'status' => null,
    'statusVariant' => 'open',
    'featured' => false,
])

<button
    {{ $attributes->class([
        'destination-card',
        'destination-card-featured' => $featured,
    ]) }}
    type="button"
    aria-label="Pilih destinasi {{ $name }}"
    aria-pressed="{{ $featured ? 'true' : 'false' }}"
    data-destination-card
    data-name="{{ $name }}"
    data-region="{{ $region }}"
    data-category="{{ $category }}"
    data-description="{{ $description }}"
    data-image="{{ $image }}"
    @if ($featured) data-featured @endif
>
    <img class="destination-image" src="{{ $image }}" alt="" loading="lazy">
    <span class="destination-shade" aria-hidden="true"></span>

    @if ($status)
        <x-status-badge class="absolute left-4 top-4" :label="$status" :variant="$statusVariant" />
    @endif

    <span class="destination-details">
        <span class="destination-region">
            <img src="{{ asset('images/figma/icon-pin.svg') }}" alt="" aria-hidden="true">
            {{ $region }}
        </span>
        <span class="destination-card-title">{{ $name }}</span>
    </span>
</button>
