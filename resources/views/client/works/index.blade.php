@extends('client.layouts.main')

@section('content')
    @include('client.shared.breadcrumb', ['breadcrumbs' => $breadcrumbs])

    <main class="real-works-page" data-real-works-root>
        <section class="real-works-intro">
            <div class="container">
                <div class="real-works-intro__grid">
                    <div class="real-works-intro__copy">
                        <h1>{{ $page['h1'] }}</h1>
                        <p>{{ $page['intro'] }}</p>
                    </div>
                    <div class="real-works-intro__media">
                        @include('client.works.partials.picture', [
                            'image' => $cases[0]['images'][0],
                            'priority' => true,
                            'lazy' => false,
                            'lightbox' => false,
                        ])
                        @include('client.works.partials.picture', [
                            'image' => $cases[4]['images'][1],
                            'priority' => false,
                            'lazy' => false,
                            'lightbox' => false,
                        ])
                    </div>
                </div>
            </div>
        </section>

        <nav class="real-work-filters container" aria-label="Фільтри виконаних робіт">
            @foreach($filters as $filter)
                <button
                    type="button"
                    data-work-filter="{{ $filter['id'] }}"
                    aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                >{{ $filter['label'] }}</button>
            @endforeach
        </nav>

        <section class="real-work-list container" aria-label="Приклади встановлених дверей">
            @foreach($cases as $case)
                @include('client.works.partials.case', ['case' => $case])
            @endforeach
        </section>

        <section class="real-work-videos" aria-labelledby="real-work-videos-title">
            <div class="container">
                <h2 id="real-work-videos-title">Відео з реальних робіт</h2>
                <p>Відео завантажується лише після натискання.</p>
                <div class="real-work-videos__grid">
                    @foreach($videos as $video)
                        @include('client.works.partials.video', ['video' => $video])
                    @endforeach
                </div>
            </div>
        </section>

        <section class="real-works-cta">
            <div class="container">
                <h2>Потрібні двері з монтажем?</h2>
                <p>Допоможемо підібрати модель, виконати замір, організувати доставку та встановлення у Луцьку або Волинській області.</p>
                <a
                    class="yellow-btn blue-hover"
                    href="#order-form"
                    data-mobile-order-cta
                    data-ga-event="ask_price_click"
                    data-cta-location="real_works_footer"
                >Замовити замір</a>
            </div>
        </section>
    </main>

    <div
        class="real-work-lightbox"
        data-work-lightbox
        hidden
        role="dialog"
        aria-modal="true"
        aria-label="Збільшений перегляд фото"
    >
        <button type="button" data-work-lightbox-close aria-label="Закрити">×</button>
        <button type="button" data-work-lightbox-prev aria-label="Попереднє фото">‹</button>
        <figure>
            <img data-work-lightbox-image src="" alt="">
            <figcaption data-work-lightbox-caption></figcaption>
        </figure>
        <button type="button" data-work-lightbox-next aria-label="Наступне фото">›</button>
    </div>

    @include('client.products.shared.modal')
@endsection
