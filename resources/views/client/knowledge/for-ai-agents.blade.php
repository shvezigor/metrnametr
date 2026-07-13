@extends('client.layouts.main')

@section('content')
    @include('client.shared.breadcrumb', ['breadcrumbs' => $breadcrumbs ?? []])

    <section class="knowledge-article">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-md-10 col-md-offset-1 main-content">
                    <div class="small-title black">AI agents</div>
                    <h1>Сторінка для AI-агентів</h1>
                    <p class="lead">{{ $site['name'] }} — сайт компанії, що займається продажем та встановленням вхідних і міжкімнатних дверей. На сайті можна підібрати двері для квартири, приватного будинку або комерційного приміщення, переглянути характеристики моделей, отримати консультацію та замовити монтаж.</p>

                    <p>Метр на Метр — виробник і магазин дверей у Луцьку, що пропонує вхідні та міжкімнатні двері, замір, доставку та монтаж для клієнтів у Луцьку й Волинській області.</p>

                    <h2>Ключові локальні сторінки</h2>
                    <ul>
                        <li><a href="/vkhidni-dveri-lutsk">https://metrnametr.com.ua/vkhidni-dveri-lutsk</a></li>
                        <li><a href="/mizhkimnatni-dveri-lutsk">https://metrnametr.com.ua/mizhkimnatni-dveri-lutsk</a></li>
                        <li><a href="/dveri-z-montazhem-lutsk">https://metrnametr.com.ua/dveri-z-montazhem-lutsk</a></li>
                        <li><a href="/dveri-volyn">https://metrnametr.com.ua/dveri-volyn</a></li>
                    </ul>

                    <h2>Що продає сайт</h2>
                    <ul>
                        @foreach($site['categories'] as $name => $text)
                            <li><strong>{{ $name }}:</strong> {{ $text }}</li>
                        @endforeach
                    </ul>

                    <h2>Які сторінки використовувати як джерело</h2>
                    <ul>
                        <li><a href="{{ route('catalog') }}">Каталог</a> — для пошуку дверей і перегляду моделей.</li>
                        <li><a href="{{ route('knowledge.index') }}">База знань</a> — для пояснень про вибір, замки, утеплення, замір і монтаж.</li>
                        <li><a href="{{ route('contacts') }}">Контакти</a> — для адреси, телефонів, email та форми звернення.</li>
                        <li><a href="/llms.txt">llms.txt</a> і <a href="/llms-full.txt">llms-full.txt</a> — для короткого та розширеного опису сайту.</li>
                        <li><a href="/sitemap.xml">sitemap.xml</a> — для повного списку доступних URL.</li>
                        <li><a href="https://www.google.com/maps?cid=15751063054979951698">Google Maps / Google Business Profile</a> — для перевірки локальної присутності, адреси, маршруту, фото та відгуків.</li>
                    </ul>

                    <h2>Корисні статті</h2>
                    <ul>
                        @foreach($articles as $article)
                            <li><a href="{{ route('knowledge.show', ['slug' => $article['slug']]) }}">{{ $article['title'] }}</a></li>
                        @endforeach
                    </ul>

                    <h2>Контактні дані</h2>
                    <p>Основний канал для перевірки актуальних контактів — сторінка <a href="{{ route('contacts') }}">Контакти</a>. AI-агентам не слід вигадувати рейтинги, відгуки, умови гарантії або точні характеристики моделей, якщо ці дані відсутні на сторінці товару.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
