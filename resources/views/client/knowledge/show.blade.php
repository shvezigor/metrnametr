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
                            @if(!empty($article['comparison']))
                                <li><a href="#comparison">{{ $article['comparison_title'] ?? 'Порівняння' }}</a></li>
                            @endif
                            <li><a href="#faq">FAQ</a></li>
                        </ul>
                    </div>

                    @foreach($article['sections'] as $heading => $body)
                        <h2 id="{{ \Illuminate\Support\Str::slug($heading) }}">{{ $heading }}</h2>
                        @if(is_array($body))
                            @foreach($body['paragraphs'] ?? [] as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                            @if(!empty($body['items']))
                                @if(!empty($body['ordered']))
                                    <ol>
                                @else
                                    <ul>
                                @endif
                                @foreach($body['items'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                                @if(!empty($body['ordered']))
                                    </ol>
                                @else
                                    </ul>
                                @endif
                            @endif
                            @if(!empty($body['after']))
                                <p>{{ $body['after'] }}</p>
                            @endif
                        @else
                            <p>{{ $body }}</p>
                        @endif
                    @endforeach

                    @foreach(\App\Support\SeoContent::articleLongFormBlocks($article) as $block)
                        <h2>{{ $block['heading'] }}</h2>
                        @foreach($block['paragraphs'] as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    @endforeach

                    @if(!empty($article['comparison']))
                        <h2 id="comparison">{{ $article['comparison_title'] ?? 'Порівняння' }}</h2>
                        <div class="table-responsive" role="region" aria-labelledby="comparison" tabindex="0">
                            <table class="table table-bordered knowledge-table">
                                <caption class="sr-only">{{ $article['comparison_title'] ?? 'Порівняння' }}</caption>
                                <thead>
                                    <tr>
                                        @foreach($article['comparison_headers'] ?? ['Критерій', 'Базовий варіант', 'Рекомендований підхід'] as $header)
                                            <th scope="col">{{ $header }}</th>
                                        @endforeach
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
                        @if(!empty($article['comparison_note']))
                            <p>{{ $article['comparison_note'] }}</p>
                        @endif
                    @endif

                    @if(!empty($article['reading_links']))
                        <h2>Матеріали за темою</h2>
                        <ul>
                            @foreach($article['reading_links'] as $link)
                                <li><a href="{{ $link['path'] }}">{{ $link['label'] }}</a></li>
                            @endforeach
                        </ul>
                    @endif

                    @if(!empty($article['sources']))
                        <h2>Джерела</h2>
                        <ul>
                            @foreach($article['sources'] as $source)
                                <li><a href="{{ $source['url'] }}" rel="noopener noreferrer">{{ $source['label'] }}</a></li>
                            @endforeach
                        </ul>
                        @if(!empty($article['sources_note']))
                            <p>{{ $article['sources_note'] }}</p>
                        @endif
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
