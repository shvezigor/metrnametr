@extends('client.layouts.main')

@section('content')
    @include('client.shared.breadcrumb', ['breadcrumbs' => $breadcrumbs ?? []])

    <section class="seo-landing">
        <div class="container">
            <div class="row">
                <article class="col-xs-12 col-md-10 col-md-offset-1 main-content">
                    <h1>{{ $landing['h1'] }}</h1>

                    @foreach((array) $landing['intro'] as $paragraph)
                        <p class="{{ $loop->first ? 'lead' : '' }}">{{ $paragraph }}</p>
                    @endforeach

                    @foreach($landing['sections'] as $heading => $body)
                        <h2>{{ $heading }}</h2>

                        @foreach((array) $body as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    @endforeach

                    <h2>Отримати консультацію / підібрати двері</h2>
                    <p>Перегляньте доступні моделі у <a href="{{ route('catalog') }}">каталозі дверей</a> або зверніться через сторінку <a href="{{ route('contacts') }}">контактів</a>. Для точного підбору підготуйте приблизні розміри отвору, фото місця встановлення та вимоги до безпеки, тепла, тиші або дизайну.</p>

                    <h2>Корисні сторінки</h2>
                    <ul>
                        <li><a href="{{ route('catalog') }}">Дивитися каталог дверей</a></li>
                        <li><a href="/vkhidni-dveri-lutsk">Вхідні двері у Луцьку</a></li>
                        <li><a href="/mizhkimnatni-dveri-lutsk">Міжкімнатні двері у Луцьку</a></li>
                        <li><a href="/dveri-z-montazhem-lutsk">Двері з монтажем у Луцьку</a></li>
                        <li><a href="/dveri-rivne">Двері у Рівному</a></li>
                        <li><a href="/vkhidni-dveri-rivne">Вхідні двері у Рівному</a></li>
                        <li><a href="/mizhkimnatni-dveri-rivne">Міжкімнатні двері у Рівному</a></li>
                        <li><a href="/dveri-volyn">Двері у Волинській області</a></li>
                        <li><a href="/dveri-dlya-kvartyry">Двері для квартири</a></li>
                        <li><a href="/dveri-dlya-budynku">Двері для будинку</a></li>
                        <li><a href="/dveri-z-termorozryvom">Двері з терморозривом</a></li>
                        <li><a href="{{ route('contacts') }}">Зв’язатися для консультації</a></li>
                    </ul>
                </article>
            </div>
        </div>
    </section>

    @include('client.shared.faq', ['faq' => $faq ?? []])
@endsection
