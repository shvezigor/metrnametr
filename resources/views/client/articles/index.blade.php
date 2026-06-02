@extends('client.layouts.main')
@section('content')
    @include('client.shared.breadcrumb', ['breadcrumbs' => $breadcrumbs ?? ['Новини']])

    <section class="page-cont news-page">

        <div class="container">
            <div class="row">

                <div class="col-xs-12">
                    <div class="small-title black">новини</div>
                </div>

                <div class="col-xs-12">
                    <div class="wrap-grid col-grid-3 news-grid">

                        @foreach($articles as $article)
                            <div class="grid-items">
                                <div class="inner-wrap">
                                    <a href="{{ $article->location }}" class="img-box" style="background-image: url({{ $article->cover }});"></a>
                                    <div class="date-box">{{ $article->created_at }}</div>
                                    <a href="{{ $article->location }}" class="title">{{ $article->title }}</a>
                                    <a href="{{ $article->location }}" class="read-more">читати повністю</a>
                                </div>
                            </div>
                        @endforeach

                    </div>

                    <div class="pagination-box">
                        {{ $articles->links('client.shared.pagination') }}
                    </div>

                </div>

                <div class="clearfix"></div>
            </div>

        </div>

    </section>
@endsection
