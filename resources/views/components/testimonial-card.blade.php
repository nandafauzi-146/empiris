@props([
    'quote',
    'author',
])

<figure class="testimonial-card">
    <div class="testimonial-stars" aria-label="5 dari 5 bintang">
        @for ($star = 0; $star < 5; $star++)
            <svg viewBox="0 0 20 20" aria-hidden="true">
                <path d="m10 1.8 2.5 5.1 5.7.8-4.1 4 .97 5.7L10 14.7l-5.1 2.7.97-5.7-4.1-4 5.7-.8L10 1.8Z" fill="currentColor"/>
            </svg>
        @endfor
    </div>
    <blockquote>“{{ $quote }}”</blockquote>
    <figcaption>{{ $author }}</figcaption>
</figure>
