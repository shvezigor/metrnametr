@php
    $specificationLabels = [
        'leaf_thickness' => 'Товщина полотна',
        'insulation' => 'Утеплення',
        'locks' => 'Замки',
        'finish' => 'Покриття',
        'thermal_break' => 'Терморозрив',
    ];
@endphp

<section class="commercial-products" aria-labelledby="commercial-products-title">
    <h2 id="commercial-products-title">{{ $landing['popular_heading'] ?? 'Популярні моделі' }}</h2>
    <p>Показуємо опубліковані моделі з каталогу. Характеристики й ціни вказані лише там, де вони підтверджені для конкретного товару.</p>

    @if($commercialProducts->isNotEmpty())
        <div class="commercial-products__grid">
            @foreach($commercialProducts as $card)
                <article class="commercial-product-card">
                    <a class="commercial-product-card__image" href="{{ $card['url'] }}">
                        <img src="{{ $card['image'] }}" alt="{{ $card['image_alt'] }}" loading="lazy" decoding="async">
                    </a>
                    <div class="commercial-product-card__body">
                        <h3><a href="{{ $card['url'] }}">{{ $card['title'] }}</a></h3>
                        @if(!empty($card['type']))
                            <p class="commercial-product-card__type">{{ $card['type'] }}</p>
                        @endif
                        @if(!empty($card['specifications']))
                            <ul class="commercial-products__specs">
                                @foreach($card['specifications'] as $key => $value)
                                    <li><strong>{{ $specificationLabels[$key] ?? $key }}:</strong> {{ $value }}</li>
                                @endforeach
                            </ul>
                        @endif
                        @if(!empty($card['availability']))
                            <p class="commercial-product-card__availability">{{ $card['availability'] }}</p>
                        @endif
                        <p class="commercial-product-card__price">{{ $card['price'] }}</p>
                        <div class="commercial-product-card__actions">
                            <a href="{{ route('contacts') }}#order-form" data-ga-event="ask_price_click" data-cta-location="commercial_product">Запитати ціну</a>
                            <a href="{{ route('contacts') }}#order-form" data-ga-event="ask_price_click" data-cta-location="commercial_measurement">Замовити замір</a>
                            <a href="{{ $card['url'] }}">Детальніше</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="commercial-products__empty">
            <p>У цій вибірці зараз немає моделей із достатньо підтвердженими даними. Перегляньте повний каталог або зверніться по підбір.</p>
            <p><a href="{{ route('catalog') }}">Перейти до каталогу</a> · <a href="{{ route('contacts') }}#order-form">Отримати консультацію</a></p>
        </div>
    @endif
</section>
