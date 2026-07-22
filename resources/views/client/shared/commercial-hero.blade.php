@php
    $heroLocation = $heroLocation ?? 'commercial_hero';
    $askPriceUrl = $askPriceUrl ?? route('contacts') . '#order-form';
    $askPriceModal = $askPriceModal ?? false;
    $heroImage = $heroImage ?? '/images/content/slider-3.jpg';
@endphp

<section class="home-hero-modern commercial-hero" aria-labelledby="{{ $heroLocation }}-title">
    <div class="container">
        <div class="home-hero-grid">
            <div class="home-hero-copy">
                <h1 id="{{ $heroLocation }}-title">{{ $heroTitle }}</h1>
                <p>{{ $heroText }}</p>

                <div class="home-hero-actions" aria-label="Основні дії">
                    <a
                        href="{{ $askPriceUrl }}"
                        class="yellow-btn blue-hover"
                        data-ga-event="ask_price_click"
                        data-cta-location="{{ $heroLocation }}"
                        @if($askPriceModal) data-toggle="modal" data-target="#order-form" @endif
                    >Запитати ціну</a>
                    <a
                        href="tel:+380673343368"
                        class="blue-btn"
                        data-ga-event="phone_click"
                        data-cta-location="{{ $heroLocation }}"
                    >Подзвонити</a>
                    <a
                        href="{{ route('catalog') }}"
                        class="blue-btn"
                        data-ga-event="catalog_click"
                        data-cta-location="{{ $heroLocation }}"
                    >{{ $catalogLabel ?? 'Перейти в каталог' }}</a>
                </div>

                <ul class="home-hero-proof" aria-label="Переваги Метр на Метр">
                    @foreach($heroTrustItems as $trustItem)
                        <li>
                            @if(!empty($trustItem['url']))
                                <a href="{{ $trustItem['url'] }}" target="_blank" rel="noopener">{{ $trustItem['label'] }}</a>
                            @else
                                {{ $trustItem['label'] }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="home-hero-media">
                <img src="{{ $heroImage }}" alt="{{ $heroImageAlt }}" loading="eager">
            </div>
        </div>
    </div>
</section>
