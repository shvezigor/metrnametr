@extends('client.layouts.main')
@section('content')

    @include('client.shared.breadcrumb', $breadcrumbs ?? [])

    <section class="page-cont post-page">

        <div class="container">
            <div class="row">

                <div class="col-xs-12 col-md-8 main-cont">

                    <img src="{{ $article->cover }}" class="page-img" alt="{{ $article->title }}">
                    <div class="inner-main-cont article">

                        <h1>{{ $article->title }}</h1>

                        <p>{!! $article->text !!}</p>

                        <div class="date-box">{{ $article->created_at }}</div>

                        <div class="social-box">
                            <ul>
                                <li><a href="{{ $article->facebook_share_link }}" target="_blank"><i class="fa fa-facebook-f"></i></a></li>
                                <li><a href="{{ $article->twitter_share_link }}" target="_blank"><i class="fa fa-twitter"></i></a></li>
                                <li><a href="{{ $article->telegram_share_link }}" target="_blank"><i class="fa fa-telegram"></i></a></li>
                            </ul>
                        </div>

                        <div class="clearfix"></div>

                    </div>

                </div>

                <div class="col-xs-12 col-md-4 sidebar">

                    <div class="inner-bg">

                        <h3 class="small-title black">Останні новини</h3>

                        <div class="last-news">

                            @foreach($list as $item)
                                <div class="grid-items">
                                    <div class="inner-wrap">
                                        <a href="{{ $item->location }}" class="img-box" style="background-image: url({{ $item->cover }});"></a>
                                        <div class="date-box">{{ $item->created_at }}</div>
                                        <a href="{{ $item->location }}" class="title">{{ $item->title }}</a>
                                        <a href="{{ $item->location }}" class="read-more">читати повністю</a>
                                    </div>
                                </div>
                            @endforeach

                            <a href="{{ route('articles') }}" class="yellow-btn blue-hover">Усі новини</a>

                        </div>

                    </div>

                </div>

                <div class="clearfix"></div>
            </div>

        </div>

    </section>

@endsection
