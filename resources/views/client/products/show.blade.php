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
                                    <img src="{{ $image->location }}" alt="{{ $product->title }}">
                                </a>
                            </li>
                        @endforeach

                    </ul>
                </div>

                <div class="col-xs-12 col-md-6 main-content">

                    @include('client.shared.breadcrumb', ['breadcrumbs' => $breadcrumbs ?? []])

                    <h1>{{ $product->title }}</h1>

                    <div class="feature-list">
                        {!! $product->text !!}
                    </div>

                    <div class="row">
                        <div class="col-xs-6 product-price">
                            <span class="product-price">{{ $product->price_text }}</span>
                        </div>
                        <div class="col-xs-6 text-right">
                            <button
                                class="yellow-btn blue-hover"
                                data-toggle="modal"
                                data-target="#order-form"
                                data-id="{{$product->id}}"
                            >
                                Замовити
                            </button>
                        </div>
                    </div>

                    <div class="ps-box">
                        <p>
                            Використання дверей з МДФ накладками на вулиці, тільки при наявності накриття над дверима і
                            виключення попадання опадів.
                        </p>
                    </div>

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
                                                   data-id="{{$item->id}}">Замовити</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="title-box"><a href="{{ $item->location }}">{{ $item->title }}</a></div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>

            </div>
        </section>
    @endif

    @include('client.products.shared.modal')

@endsection
