@extends('client.layouts.main')

@section('content')
    @include('client.shared.breadcrumb', ['breadcrumbs' => $breadcrumbs ?? []])

    <section class="knowledge-page">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-md-10 col-md-offset-1">
                    <div class="small-title black">План бази знань</div>
                    <h1>План бази знань про двері</h1>
                    <p class="lead">Редакційна карта з {{ $totalArticles }} експертних матеріалів для AI-friendly Knowledge Base Метр на Метр. Теми згруповані так, щоб покрити вибір дверей, технології виробництва, монтаж, безпеку, дизайн, догляд і часті питання покупців.</p>
                </div>
            </div>

            @foreach($clusters as $clusterKey => $cluster)
                <div class="row knowledge-plan-group">
                    <div class="col-xs-12 col-md-10 col-md-offset-1">
                        <h2>{{ $cluster['label'] }}</h2>
                        <p>{{ $cluster['summary'] }}</p>
                        <p><strong>Заплановано:</strong> {{ count($cluster['titles']) }} статей</p>
                    </div>

                    <div class="col-xs-12 col-md-10 col-md-offset-1">
                        <div class="table-responsive">
                            <table class="table table-bordered knowledge-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Тема</th>
                                        <th>Slug</th>
                                        <th>Структура</th>
                                        <th>Статус</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($articlesByCluster[$clusterKey] as $index => $article)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $article['title'] }}</td>
                                            <td><code>{{ $article['slug'] }}</code></td>
                                            <td>{{ count($article['sections']) }} розділів, {{ count($article['image_prompts']) }} image prompts, {{ count($article['faq_questions']) }} FAQ</td>
                                            <td>Заплановано</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
