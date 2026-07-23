<article
    class="real-work-case"
    id="{{ $case['id'] }}"
    data-work-case
    data-work-tags="{{ implode(' ', $case['categories']) }}"
>
    <div class="real-work-case__media">
        @foreach($case['images'] as $image)
            <button
                class="real-work-case__image"
                type="button"
                aria-label="Збільшити фото: {{ $image['alt'] }}"
            >
                @include('client.works.partials.picture', [
                    'image' => $image,
                    'priority' => false,
                    'lazy' => true,
                    'lightbox' => true,
                    'caseId' => $case['id'],
                    'imageIndex' => $loop->index,
                ])
            </button>
        @endforeach
    </div>
    <div class="real-work-case__content">
        <p class="real-work-case__meta">{{ $case['type'] }} · {{ $case['location'] }}</p>
        <h2>{{ $case['title'] }}</h2>
        <p>{{ $case['description'] }}</p>
        <p class="real-work-case__services"><strong>Що зроблено:</strong> {{ implode(' / ', $case['services']) }}</p>
        <a
            class="yellow-btn blue-hover"
            href="#order-form"
            data-mobile-order-cta
            data-ga-event="ask_price_click"
            data-cta-location="real_work_{{ $case['id'] }}"
        >{{ $case['cta'] }}</a>
    </div>
</article>
