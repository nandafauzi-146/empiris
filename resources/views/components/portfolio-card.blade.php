@props([
    'title',
    'location',
    'category',
    'year',
    'href' => null,
    'image' => null,
])

<article @class(['portfolio-card', 'portfolio-card-cover' => $image])>
    @if ($image)
        <img class="portfolio-card-image" src="{{ $image }}" alt="" loading="lazy" aria-hidden="true">
    @else
        <div class="portfolio-card-art" aria-hidden="true">
            <span>{{ $category }}</span>
        </div>
    @endif
    @if ($image)
        <div class="portfolio-card-art portfolio-card-art-cover" aria-hidden="true">
            <span>{{ $category }} / {{ $year }}</span>
        </div>
    @endif
    <div class="portfolio-card-content">
        @if ($image)
            <p class="portfolio-card-meta">
                <span class="portfolio-card-location">
                    <img src="{{ asset('images/figma/icon-pin.svg') }}" alt="" aria-hidden="true">
                    {{ $location }}
                </span>
            </p>
        @else
            <p class="portfolio-card-meta">{{ $location }} <span aria-hidden="true">·</span> {{ $year }}</p>
        @endif
        <h3>{{ $title }}</h3>
        @if ($href)
            <a class="text-link" href="{{ $href }}">Lihat dokumentasi <span aria-hidden="true">→</span></a>
        @endif
    </div>
</article>
