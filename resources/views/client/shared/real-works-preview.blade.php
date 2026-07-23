@if(!empty($works) && $works->isNotEmpty())
    <section
        class="real-works-preview"
        data-real-works-preview="{{ $context }}"
        aria-labelledby="real-works-preview-title-{{ $context }}"
    >
        <div class="container">
            <div class="real-works-preview__heading">
                <div>
                    <h2 id="real-works-preview-title-{{ $context }}">Реальні встановлення дверей</h2>
                    <p>Фото дверей після монтажу у квартирах і приватних будинках Луцька та Волинської області.</p>
                </div>
                <a
                    class="yellow-btn blue-hover"
                    href="{{ route('real-works.index') }}"
                    data-ga-event="real_works_click"
                    data-cta-location="{{ $context }}_real_works"
                >Переглянути роботи</a>
            </div>
            <div class="real-works-preview__grid">
                @foreach($works as $case)
                    <a href="{{ route('real-works.index') }}#{{ $case['id'] }}" data-preview-work-image>
                        @include('client.works.partials.picture', [
                            'image' => $case['images'][0],
                            'priority' => false,
                            'lazy' => true,
                            'lightbox' => false,
                        ])
                        <span>{{ $case['title'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
