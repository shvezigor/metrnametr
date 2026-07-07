@extends('client.layouts.main')
@section('content')

@php
    $heroProduct = $slides->first() ?: $products->first();
    $heroImage = $heroProduct ? $heroProduct->cover : '/images/logo-2.png';
    $homeCategories = [
        ['title' => 'Двері в квартиру', 'text' => 'Надійні вхідні двері для щоденного використання у багатоквартирних будинках.', 'url' => '/dveri-dlya-kvartyry', 'icon' => '/images/icons/door.svg'],
        ['title' => 'Двері в будинок', 'text' => 'Вуличні моделі з утепленням, покриттям і рішеннями для приватного будинку.', 'url' => '/dveri-dlya-budynku', 'icon' => '/images/icons/door-knob.svg'],
        ['title' => 'Міжкімнатні двері', 'text' => 'Практичні рішення для житлових і комерційних інтер’єрів.', 'url' => '/mizhkimnatni-dveri-lutsk', 'icon' => '/images/icons/catalog.svg'],
        ['title' => 'Протипожежні двері', 'text' => 'Сертифіковані двері для об’єктів із підвищеними вимогами безпеки.', 'url' => '/protypozhezhni-dveri', 'icon' => '/images/icons/lock.svg'],
        ['title' => 'Технічні двері', 'text' => 'Двері для підсобних, складських, виробничих і технічних приміщень.', 'url' => '/catalog', 'icon' => '/images/icons/wallet.svg'],
        ['title' => 'Для гуртових клієнтів', 'text' => 'Постачання для дилерів, будівельних компаній і партнерських проєктів.', 'url' => '/wholesale', 'icon' => '/images/icons/handshake.svg'],
    ];
    $trustItems = [
        ['icon' => '/images/icons/door.svg', 'title' => 'Власне виробництво з 2011 року', 'text' => 'Контролюємо конструкцію, комплектацію та якість дверей на виробництві.'],
        ['icon' => '/images/icons/lock.svg', 'title' => 'ДСТУ та ISO 9001', 'text' => 'Працюємо з сертифікованими рішеннями для житлових і комерційних об’єктів.'],
        ['icon' => '/images/icons/handshake.svg', 'title' => 'Гурт і роздріб', 'text' => 'Підбираємо двері для приватних покупців, дилерів і будівельних команд.'],
        ['icon' => '/images/icons/delivery-truck.svg', 'title' => 'Доставка по Україні', 'text' => 'Організовуємо постачання дверей з Луцька в інші регіони України.'],
    ];
@endphp

<section class="home-hero-modern">
    <div class="container">
        <div class="home-hero-grid">
            <div class="home-hero-copy">
                <h1>Вхідні та міжкімнатні двері від виробника у Луцьку</h1>
                <p>Виготовляємо, продаємо та постачаємо двері для квартир, будинків, комерційних і технічних приміщень. Гурт і роздріб, доставка по Україні.</p>

                <div class="home-hero-actions">
                    <a href="{{ route('catalog') }}" class="yellow-btn blue-hover">Перейти в каталог</a>
                    <a href="#order-form" class="blue-btn" data-toggle="modal" data-target="#order-form">Отримати консультацію</a>
                </div>

                <ul class="home-hero-proof">
                    <li>Власне виробництво</li>
                    <li>ДСТУ та ISO 9001</li>
                    <li>Гурт і роздріб</li>
                    <li>Доставка по Україні</li>
                </ul>
            </div>

            <div class="home-hero-media">
                <img src="{{ $heroImage }}" alt="Вхідні та міжкімнатні двері Метр на Метр" loading="eager">
            </div>
        </div>
    </div>
</section>

<section class="about-us">
    <div class="container">
        <div class="row">

            <div class="col-xs-12 col-sm-6 col-md-5 col-md-offset-1">
                <h2>{{ \App\Models\Setting::getValue('main_title') }}</h2>
                @foreach(array_slice(preg_split('/$/m', \App\Models\Setting::getValue('main_text'), -1, PREG_SPLIT_NO_EMPTY), 0, 4) as $text)
                    <p>{{ $text }}</p>
                @endforeach
                <a href="{{ route('about') }}" class="blue-btn about-more">Детальніше про виробництво</a>
            </div>

            <div class="col-xs-12 col-sm-6">
                <div class="img-box"><img src="/images/logo-2.png" alt="Метр на Метр - двері у Луцьку" title="Метр на Метр"></div>
            </div>

        </div>
    </div>

</section>

<section class="home-category-section">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="small-title black">Категорії дверей</div>
                <h2 class="section-heading">Швидко оберіть двері під вашу задачу</h2>
            </div>
        </div>

        <div class="home-category-grid">
            @foreach($homeCategories as $category)
                <a class="home-category-card" href="{{ $category['url'] }}">
                    <span class="category-icon"><img src="{{ $category['icon'] }}" alt="" loading="lazy"></span>
                    <span class="category-title">{{ $category['title'] }}</span>
                    <span class="category-text">{{ $category['text'] }}</span>
                    <span class="category-link">Дивитися</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="door-groups">
    <div class="background">
        <div class="layer" style="background:#333333;"></div>
    </div>
    <div class="container">
        <div class="row">

            <div class="col-xs-12 col-md-10 col-md-offset-1">
                <div class="small-title">Групи дверей</div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1">

                <div class="door-slider owl-carousel">

                    @foreach($slides as $slide)

                        <div class="row">

                            <div class="col-xs-12 col-sm-6">
                                <div class="img-box product-image product-slider">
                                    <img src="{{ $slide->cover }}" alt="Вхідні металеві двері {{ $slide->title }}" title="{{ $slide->title }}" loading="lazy">
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6">
                                <div class="cont-box">
                                    <h2>{{ $slide->lastPublishedCatalog()->title }}</h2>

                                    <p>{!! $slide->text !!}</p>

                                    <a href="{{ $slide->location }}" class="blue-btn">Більше інформації</a>
                                    <a href="/catalog?catalog={{ $slide->lastPublishedCatalog()->id }}" class="yellow-btn blue-hover">В каталог</a>
                                </div>
                            </div>
                        </div>

                    @endforeach

                </div>

            </div>

        </div>
    </div>
</section>

<section class="wrap-popular">
    <div class="container">

        <div class="row">
            <div class="col-xs-12">
                <div class="small-title black">Популярні товари</div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 wrap-grid">

                @foreach($products as $product)

                    <div class="grid-items">
                        <div class="inner-wrap">
                            <div class="bg-wrap">


                                @if($product->label !== 0)
                                    <div class="label-box {{ $product->label_class }}">{{ $product->label_text }}</div>
                                @endif

                                <div class="social-box">
                                    <span>Поділитись</span> <i class="fa fa-share-alt" aria-hidden="true"></i>
                                    <ul>
                                        <li><a href="{{ $product->facebook_share_link }}" target="_blank"><i class="fa fa-facebook-f"></i></a></li>
                                        <li><a href="{{ $product->twitter_share_link }}" target="_blank"><i class="fa fa-twitter"></i></a></li>
                                        <li><a href="{{ $product->telegram_share_link }}" target="_blank"><i class="fa fa-telegram"></i></a></li>
                                    </ul>
                                </div>

                                <div class="img-box product-image product-item">
                                    <a href="{{ $product->location }}">
                                        <img src="{{ $product->cover }}" alt="Вхідні металеві двері {{ $product->title }}" title="{{ $product->title }}" loading="lazy">
                                    </a>
                                </div>

                                <div class="button-wrap">
                                    <div class="price">{{ $product->price_text }}</div>
                                    <div class="order">
                                        <a
                                            href="#"
                                            data-toggle="modal"
                                            data-target="#order-form"
                                            data-id="{{$product->id}}"
                                        >
                                            Запитати ціну
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="title-box">
                                <a href="{{ $product->location }}">{{ $product->title }}</a>
                                <div class="product-card-type">Вхідні / технічні двері</div>
                                <div class="product-card-badges">
                                    <span>ДСТУ</span>
                                    <span>Гарантія</span>
                                    <span>Покриття</span>
                                </div>
                                <div class="product-card-actions">
                                    <a href="{{ $product->location }}">Детальніше</a>
                                    <a href="#" data-toggle="modal" data-target="#order-form" data-id="{{$product->id}}">Консультація</a>
                                </div>
                            </div>
                        </div>
                    </div>

                @endforeach

            </div>
        </div>

    </div>
</section>

<section class="home-trust-section">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="small-title black">Чому обирають Метр на Метр</div>
            </div>
        </div>

        <div class="home-trust-grid">
            @foreach($trustItems as $item)
                <div class="home-trust-card">
                    <div class="trust-icon"><img src="{{ $item['icon'] }}" alt="" loading="lazy"></div>
                    <h3>{{ $item['title'] }}</h3>
                    <p>{{ $item['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="news-box">
    <div class="background">
        <div class="layer" style="background: #fff;"></div>
    </div>

    <div class="container">
        <div class="row">

            <div class="col-xs-12">
                <div class="small-title black">новини</div>
            </div>

            <div class="col-xs-12">
                <div class="wrap-news-slider">
                    <div class="news-slider owl-carousel">

                        @foreach($articles as $article)
                            <div class="item">
                                <a href="{{ $article->location }}" class="img-box" style="background-image: url({{ $article->cover }});"></a>
                                <div class="date-box">{{ $article->created_at }}</div>
                                <a href="{{ $article->location }}" class="title">{{ $article->title }}</a>
                                <a href="{{ $article->location }}" class="read-more">читати повністю</a>
                            </div>
                        @endforeach

                    </div>

                    <div class="wrap-range">
                        <div><input type="text" id="range" value="" name="range" /></div>
                    </div>
                </div>
            </div>

            <div class="col-xs-12">
                <a href="{{ route('articles') }}" class="yellow-btn blue-hover">Усі новини</a>
            </div>

        </div>
    </div>
</section>

<section class="ai-summary">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-10 col-md-offset-1">
                <p>Метр на Метр — компанія, що займається продажем та встановленням вхідних і міжкімнатних дверей. На сайті можна підібрати двері для квартири, приватного будинку або комерційного приміщення, переглянути характеристики моделей, отримати консультацію та замовити монтаж.</p>
                <p><a href="{{ route('knowledge.index') }}">Перейти до бази знань про двері</a></p>
            </div>
        </div>
    </div>
</section>

@include('client.shared.faq', ['faq' => $faq ?? []])

@include('client.main.shared.map')

@include('client.products.shared.modal')

@endsection
