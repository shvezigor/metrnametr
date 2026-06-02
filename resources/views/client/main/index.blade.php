@extends('client.layouts.main')
@section('content')

@include('client.main.shared.slider')

<section class="about-us">
    <div class="container">
        <div class="row">

            <div class="col-xs-12 col-sm-6 col-md-5 col-md-offset-1">
                <h2>{{ \App\Models\Setting::getValue('main_title') }}</h2>
                @foreach(preg_split('/$/m', \App\Models\Setting::getValue('main_text'), -1, PREG_SPLIT_NO_EMPTY) as $text)
                    <p>{{ $text }}</p>
                @endforeach
            </div>

            <div class="col-xs-12 col-sm-6">
                <div class="img-box"><img src="/images/logo-2.png" alt=""></div>
            </div>

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
                                    <img src="{{ $slide->cover }}" alt="{{ $slide->title }}">
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
                                        <img src="{{ $product->cover }}" alt="{{ $product->title }}">
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
                                            Замовити
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="title-box">
                                <a href="{{ $product->location }}">{{ $product->title }}</a>
                            </div>
                        </div>
                    </div>

                @endforeach

            </div>
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

@include('client.main.shared.map')

@include('client.products.shared.modal')

@endsection
