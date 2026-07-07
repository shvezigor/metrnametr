@php
    $phones = array_values(array_filter(array_map('trim', explode(',', \App\Models\Setting::getValue('phones')))));
    $primaryPhone = $phones[0] ?? '';
    $phoneHref = preg_replace('/[^0-9+]/', '', $primaryPhone);
@endphp

<section class="secondary-page-cta">
    <div class="secondary-page-cta__content">
        <h2>{{ $title ?? 'Потрібна допомога з вибором дверей?' }}</h2>
        <p>{{ $text ?? 'Підкажемо модель, комплектацію, умови доставки, гарантії та співпраці під вашу задачу.' }}</p>
    </div>

    <div class="secondary-page-cta__actions">
        <a href="{{ route('catalog') }}" class="yellow-btn blue-hover">Перейти в каталог</a>
        <a href="{{ route('contacts') }}" class="blue-btn">Отримати консультацію</a>
        @if($phoneHref)
            <a href="tel:{{ $phoneHref }}" class="secondary-page-cta__phone">Подзвонити</a>
        @endif
    </div>
</section>
