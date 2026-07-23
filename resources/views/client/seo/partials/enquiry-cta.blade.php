<aside class="commercial-enquiry commercial-enquiry--{{ $position }}" aria-label="Заявка на підбір дверей">
    <div>
        <h2>{{ $position === 'final' ? 'Потрібен точний підбір?' : 'Отримайте попередню консультацію' }}</h2>
        <p>Опишіть, куди потрібні двері, або підготуйте фото отвору для консультації через доступний канал зв’язку.</p>
    </div>
    <div class="commercial-enquiry__actions">
        <a class="yellow-btn blue-hover" href="{{ route('contacts') }}#order-form" data-ga-event="ask_price_click" data-cta-location="landing_{{ $position }}">Запитати ціну</a>
        <a href="{{ route('contacts') }}#order-form" data-ga-event="ask_price_click" data-cta-location="measurement_{{ $position }}">Замовити замір</a>
        <a href="{{ route('contacts') }}">Надіслати фото отвору для попередньої консультації</a>
    </div>
</aside>
