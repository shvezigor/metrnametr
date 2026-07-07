@extends('client.layouts.main')
@section('content')

    <section class="item-cont">

        <div class="container">
            <div class="row">

                <div class="col-xs-12 col-md-6">
                    <ul class="dot-slider owl-carousel owl-theme">

                        @foreach($product->images as $image)
                            <li class=""
                                data-dot="<div><img src='{{ $image->location }}' alt='{{ $product->title }}'></div><span>{{ $product->title }}</span>">
                                <a href="{{ $image->location }}" data-fancybox="group" data-fancybox
                                   data-caption="{{ $product->title }}" class="img-box">
                                    <img src="{{ $image->location }}" alt="Вхідні металеві двері {{ $product->title }}" title="{{ $product->title }}" loading="lazy">
                                </a>
                            </li>
                        @endforeach

                    </ul>
                </div>

                <div class="col-xs-12 col-md-6 main-content">

                    @include('client.shared.breadcrumb', ['breadcrumbs' => $breadcrumbs ?? []])

                    <h1>{{ $product->title }}</h1>

                    <p class="product-intro">Підберемо комплектацію, покриття та розмір під ваш об’єкт. Можна замовити консультацію або уточнити ціну перед покупкою.</p>

                    <div class="product-page-badges">
                        <span>ДСТУ</span>
                        <span>Гарантія</span>
                        <span>Виробник</span>
                        <span>Доставка</span>
                    </div>

                    <div class="feature-list">
                        {!! $product->text !!}
                    </div>

                    <div class="row product-cta-row">
                        <div class="col-xs-6 product-price">
                            <span class="product-price">{{ $product->price_text }}</span>
                        </div>
                        <div class="col-xs-6 text-right product-cta-buttons">
                            <button
                                class="yellow-btn blue-hover"
                                data-toggle="modal"
                                data-target="#order-form"
                                data-id="{{$product->id}}"
                            >
                                Запитати ціну
                            </button>
                            <button
                                class="blue-btn"
                                data-toggle="modal"
                                data-target="#order-form"
                                data-id="{{$product->id}}"
                            >
                                Отримати консультацію
                            </button>
                        </div>
                    </div>

                    <div class="ps-box">
                        <p>
                            Використання дверей з МДФ накладками на вулиці, тільки при наявності накриття над дверима і
                            виключення попадання опадів.
                        </p>
                    </div>

                    @if(!empty($extra))
                        <div class="product-seo-block">
                            <h2>Для кого ця модель</h2>
                            <p>{{ $extra['audience'] }}</p>

                            <h2>Переваги</h2>
                            <ul>
                                @foreach($extra['benefits'] as $benefit)
                                    <li>{{ $benefit }}</li>
                                @endforeach
                            </ul>

                            <h2>Технічні характеристики</h2>
                            <table class="table table-bordered">
                                <tbody>
                                    @foreach($extra['specs'] as $name => $value)
                                        @if(!empty($value))
                                            <tr>
                                                <th>{{ $name }}</th>
                                                <td>{{ $value }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>

                            <h2>Рекомендовані статті</h2>
                            <ul>
                                <li><a href="{{ route('knowledge.show', ['slug' => 'yak-vybraty-vkhidni-dveri-dlia-kvartyry']) }}">Як вибрати вхідні двері для квартири</a></li>
                                <li><a href="{{ route('knowledge.show', ['slug' => 'yaka-tovshchyna-metalu-u-dveriakh']) }}">Яка товщина металу має бути у вхідних дверях</a></li>
                                <li><a href="{{ route('knowledge.show', ['slug' => 'montazh-vkhidnykh-dverei']) }}">Як проходить монтаж вхідних дверей</a></li>
                            </ul>
                        </div>
                    @endif

                </div>

                <div class="clearfix"></div>

            </div>
        </div>

    </section>

    @if($list && count($list) > 0)
        <section class="wrap-popular">
            <div class="container">

                <div class="row">
                    <div class="col-xs-12">
                        <div class="small-title black">Популярні товари</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 wrap-grid">

                        @foreach($list as $item)
                            <div class="grid-items">
                                <div class="inner-wrap">
                                    <div class="bg-wrap">

                                        @if($item->label !== 0)
                                            <div
                                                class="label-box {{ $item->label_class }}">{{ $item->label_text }}</div>
                                        @endif

                                        <div class="social-box">
                                            <span>Поділитись</span> <i class="fa fa-share-alt" aria-hidden="true"></i>
                                            <ul>
                                                <li><a href="{{ $item->facebook_share_link }}" target="_blank"><i
                                                            class="fa fa-facebook-f"></i></a></li>
                                                <li><a href="{{ $item->twitter_share_link }}" target="_blank"><i
                                                            class="fa fa-twitter"></i></a></li>
                                                <li><a href="{{ $item->telegram_share_link }}" target="_blank"><i
                                                            class="fa fa-telegram"></i></a></li>
                                            </ul>
                                        </div>

                                        <div class="img-box product-image product-item">
                                            <a href="{{ $item->location }}">
                                                <img src="{{ $item->cover }}" alt="{{ $item->title }}">
                                            </a>
                                        </div>

                                        <div class="button-wrap">
                                            <div class="price">{{ $item->price_text }}</div>
                                            <div class="order">
                                                <a href="#" data-toggle="modal" data-target="#order-form"
                                                   data-id="{{$item->id}}">Запитати ціну</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="title-box">
                                        <a href="{{ $item->location }}">{{ $item->title }}</a>
                                        <div class="product-card-type">Вхідні / технічні двері</div>
                                        <div class="product-card-badges">
                                            <span>ДСТУ</span>
                                            <span>Гарантія</span>
                                            <span>Покриття</span>
                                        </div>
                                        <div class="product-card-actions">
                                            <a href="{{ $item->location }}">Детальніше</a>
                                            <a href="#" data-toggle="modal" data-target="#order-form" data-id="{{$item->id}}">Консультація</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>

            </div>
        </section>
    @endif

    @include('client.shared.faq', ['faq' => $faq ?? []])

    @include('client.products.shared.modal')

@endsection
