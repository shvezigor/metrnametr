@extends('client.layouts.main')

@section('content')
    @include('client.shared.breadcrumb', ['breadcrumbs' => $breadcrumbs ?? []])

    <section class="knowledge-article">
        <div class="container">
            <div class="row">
                <article class="col-xs-12 col-md-8 col-md-offset-1 main-content">
                    <div class="small-title black">База знань</div>
                    <h1>{{ $article['title'] }}</h1>
                    <p class="lead">{{ $article['intro'] }}</p>

                    @php($image = $article['image'])
                    <figure class="knowledge-hero-image">
                        <img
                            src="{{ $image['src'] }}"
                            alt="{{ $image['alt'] }}"
                            title="{{ $image['title'] }}"
                            width="1200"
                            height="675"
                            loading="eager"
                        >
                        <figcaption>{{ $image['caption'] }}</figcaption>
                    </figure>

                    <div class="knowledge-toc">
                        <strong>Зміст</strong>
                        <ul>
                            @foreach($article['sections'] as $heading => $body)
                                <li><a href="#{{ \Illuminate\Support\Str::slug($heading) }}">{{ $heading }}</a></li>
                            @endforeach
                            <li><a href="#comparison">Порівняння</a></li>
                            <li><a href="#faq">FAQ</a></li>
                        </ul>
                    </div>

                    @foreach($article['sections'] as $heading => $body)
                        <h2 id="{{ \Illuminate\Support\Str::slug($heading) }}">{{ $heading }}</h2>
                        <p>{{ $body }}</p>
                    @endforeach

                    @foreach(\App\Support\SeoContent::articleLongFormBlocks($article) as $block)
                        <h2>{{ $block['heading'] }}</h2>
                        @foreach($block['paragraphs'] as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    @endforeach

                    @if(!empty($article['comparison']))
                        <h2 id="comparison">Порівняння</h2>
                        <div class="table-responsive">
                            <table class="table table-bordered knowledge-table">
                                <thead>
                                    <tr>
                                        <th>Критерій</th>
                                        <th>Базовий варіант</th>
                                        <th>Рекомендований підхід</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($article['comparison'] as $row)
                                        <tr>
                                            <td>{{ $row['criteria'] }}</td>
                                            <td>{{ $row['basic'] }}</td>
                                            <td>{{ $row['recommended'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <nav class="knowledge-commercial-links" aria-labelledby="knowledge-commercial-links-title">
                        <h2 id="knowledge-commercial-links-title">Що подивитися далі</h2>
                        <p>Для підбору конкретної моделі перейдіть у <a href="{{ route('catalog') }}">каталог дверей</a> або зверніться через сторінку <a href="{{ route('contacts') }}">контактів</a>. Якщо потрібні загальні пояснення, перегляньте інші матеріали бази знань.</p>
                        <ul>
                            @foreach($commercialLinks as $link)
                                <li><a href="{{ $link['path'] }}">{{ $link['label'] }}</a></li>
                            @endforeach
                        </ul>
                    </nav>
                </article>

                <aside class="col-xs-12 col-md-3 sidebar">
                    <div class="inner-bg">
                        <div class="small-title black">Корисні статті</div>
                        <ul class="knowledge-related">
                            @foreach($related as $item)
                                <li><a href="{{ route('knowledge.show', ['slug' => $item['slug']]) }}">{{ $item['title'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <div id="faq">
        @include('client.shared.faq', ['faq' => $article['faq']])
    </div>
@endsection
