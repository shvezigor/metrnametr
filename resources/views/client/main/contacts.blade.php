@extends('client.layouts.main')
@section('content')
    @include('client.shared.breadcrumb', $breadcrumbs ?? [])

<section class="page-cont vacancies-page">

    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>Контакти Метр на Метр</h1>
            </div>
        </div>

        <div class="row wrap-contact-manager">

            <div class="col-xs-12 col-md-4">
                <div class="cont-box">
                    <div class="addr">
                        <i class="fa fa-map-marker" aria-hidden="true"></i>
                        {{ \App\Models\Setting::getValue('address') }}
                    </div>
                    <div class="num">
                        <i class="fa fa-phone" aria-hidden="true"></i>
                        @foreach(explode(',', \App\Models\Setting::getValue('phones')) as $item)
                            <span>{{ $item }}</span>
                        @endforeach
                    </div>
                    <div class="mail">
                        <i class="fa fa-envelope" aria-hidden="true"></i>
                        {{ \App\Models\Setting::getValue('email') }}
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-md-8">
                <div class="wrap-form" id="order-form" tabindex="-1">

                    <h4 class="small-title black text-cenetr">Інформація Для Оптових покупців</h4>

                    <form action="{{ route('message') }}" method="POST" class="contact-manager">
                        @csrf

                        <input name="name" type="text" class="half mr20" placeholder="Ім'я">
                        <input name="email" type="email" class="half" required placeholder="Email">
                        <input name="title" type="text" placeholder="Тема повідомлення">
                        <textarea name="text" required placeholder="Повідомлення"></textarea>

                        <button type="submit" class="yellow-btn blue-hover">Надіслати</button>
                    </form>

                </div>


            </div>

            <div class="clearfix"></div>
        </div>

        @include('client.shared.secondary-page-cta', [
            'title' => 'Почати з каталогу або консультації?',
            'text' => 'Перейдіть до моделей дверей або залиште звернення, якщо потрібен підбір під конкретний отвір, бюджет чи об’єкт.',
        ])


    </div>

</section>

<section class="map-box">

    <div class="background">
        <div class="layer" style="background: #f5f5f5;)"></div>
    </div>

    <div class="container-fluid">

        <div class="row wrap-maps">
            <div class="col-xs-12 map-canvas" id="map-canvas">
                <iframe
                    title="Метр на Метр на Google Maps"
                    src="https://www.google.com/maps?q=%D0%9C%D0%B5%D1%82%D1%80%20%D0%BD%D0%B0%20%D0%9C%D0%B5%D1%82%D1%80%2C%20%D0%BF%D1%80%D0%BE%D1%81%D0%BF%D0%B5%D0%BA%D1%82%20%D0%9F%D0%B5%D1%80%D0%B5%D0%BC%D0%BE%D0%B3%D0%B8%2024%2C%20%D0%9B%D1%83%D1%86%D1%8C%D0%BA&output=embed"
                    width="100%"
                    height="420"
                    style="border:0;"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    allowfullscreen></iframe>
                <div class="map-embed-meta" style="padding: 14px 20px; text-align: center; background: #eef2f4;">
                    <strong>{{ \App\Models\Setting::getValue('address') }}</strong>
                    <span>{{ \App\Models\Setting::getValue('phones') }}</span>
                    <a href="https://www.google.com/maps?cid=15751063054979951698"
                       target="_blank"
                       rel="noopener">Відкрити в Google Maps</a>
                </div>
            </div>
        </div>

    </div>

</section>

@endsection
