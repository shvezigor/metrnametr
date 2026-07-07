@extends('client.layouts.main')
@section('content')
    @include('client.shared.breadcrumb', $breadcrumbs ?? [])

<section class="page-cont vacancies-page">

    <div class="container">
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
            <div class="col-xs-12 map-canvas" id="map-canvas"
                 data-map-latitude="50.754183"
                 data-map-longitude="25.3416367"
                 data-cursor-latitude="50.754183"
                 data-cursor-longitude="25.3416367">
                <div class="map-fallback">
                    <strong>{{ \App\Models\Setting::getValue('address') }}</strong>
                    <span>{{ \App\Models\Setting::getValue('phones') }}</span>
                </div>
            </div>
        </div>

    </div>

</section>

@endsection
