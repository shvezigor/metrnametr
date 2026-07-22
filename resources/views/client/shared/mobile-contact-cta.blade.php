@php
    $phones = array_values(array_filter(array_map('trim', explode(',', \App\Models\Setting::getValue('phones')))));
    $primaryPhone = $phones[0] ?? '';
    $phoneHref = preg_replace('/[^0-9+]/', '', $primaryPhone);
@endphp

<nav class="mobile-contact-cta" aria-label="Швидкі дії">
    @if($phoneHref)
        <a href="tel:{{ $phoneHref }}" class="mobile-contact-cta__item" data-ga-event="phone_click" data-cta-location="mobile_sticky">
            <i class="fa fa-phone" aria-hidden="true"></i>
            <span>Подзвонити</span>
        </a>
    @endif

    <a
        href="{{ route('contacts') }}#order-form"
        class="mobile-contact-cta__item mobile-contact-cta__item--primary"
        data-mobile-order-cta
        data-ga-event="ask_price_click"
        data-cta-location="mobile_sticky"
    >
        <i class="fa fa-comment" aria-hidden="true"></i>
        <span>Запитати ціну</span>
    </a>

    <a href="{{ route('catalog') }}" class="mobile-contact-cta__item" data-ga-event="catalog_click" data-cta-location="mobile_sticky">
        <i class="fa fa-th-large" aria-hidden="true"></i>
        <span>Каталог</span>
    </a>
</nav>
