@props(['status'])

@php
    $variant = match ($status) {
        \App\Enums\KonsultasiStatus::Baru, \App\Enums\KonsultasiStatus::Batal => 'limited',
        \App\Enums\KonsultasiStatus::Penawaran => 'featured',
        \App\Enums\KonsultasiStatus::Dibalas, \App\Enums\KonsultasiStatus::Deal => 'open',
    };
@endphp

<x-status-badge
    :label="$status->label()"
    :variant="$variant"
    {{ $attributes }}
/>
