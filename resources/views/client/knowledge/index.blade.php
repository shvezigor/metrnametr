@extends('client.layouts.main')

@section('content')
    @include('client.shared.breadcrumb', ['breadcrumbs' => $breadcrumbs ?? []])

    <section class="knowledge-page">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-md-10 col-md-offset-1">
                    <div class="small-title black">База знань</div>
                    <h1>База знань про вхідні двері</h1>
                    <p class="lead">Практичні матеріали Метр на Метр допомагають зрозуміти, які двері краще обрати для квартири або будинку, як оцінювати замки, утеплення, товщину металу, замір і монтаж.</p>
                    <p><a href="{{ route('knowledge.plan') }}">Переглянути план розвитку бази знань зі 100 статей</a></p>
                </div>
            </div>

            <div class="row knowledge-grid">
                @foreach($articles as $article)
                    <div class="col-xs-12 col-sm-6 col-md-4">
                        <article class="knowledge-card">
                            <h2><a href="{{ route('knowledge.show', ['slug' => $article['slug']]) }}">{{ $article['title'] }}</a></h2>
                            <p>{{ $article['description'] }}</p>
                            <a href="{{ route('knowledge.show', ['slug' => $article['slug']]) }}" class="read-more">Читати повністю</a>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
