@extends('client.layouts.main')

@section('content')
    @include('client.shared.breadcrumb', ['breadcrumbs' => $breadcrumbs ?? []])

    <section class="seo-landing">
        <div class="container">
            <div class="row">
                <article class="col-xs-12 col-md-10 col-md-offset-1 main-content">
                    <h1>{{ $landing['h1'] }}</h1>
                    <p class="lead">{{ $landing['intro'] }}</p>

                    @foreach($landing['sections'] as $heading => $body)
                        <h2>{{ $heading }}</h2>
                        <p>{{ $body }}</p>
                    @endforeach

                    <h2>Як замовити</h2>
                    <p>Перегляньте доступні моделі у <a href="{{ route('catalog') }}">каталозі дверей</a> або зверніться через сторінку <a href="{{ route('contacts') }}">контактів</a>. Для точного підбору підготуйте приблизні розміри отвору, фото місця встановлення та вимоги до безпеки, тепла, тиші або дизайну.</p>
                </article>
            </div>
        </div>
    </section>

    @include('client.shared.faq', ['faq' => $faq ?? []])
@endsection
