@extends('client.layouts.main')
@section('content')
    @include('client.shared.breadcrumb', $breadcrumbs ?? [])

<section class="page-cont opt-wrap">

    <div class="container">
        <div class="row">

            <div class="col-xs-12">
                <h4 class="small-title black">Інформація Для Оптових покупців</h4>
            </div>

            <div class="col-xs-12">
                <div class="wrap-grid col-grid-3">

                    <div class="grid-items">
                        <div class="inner-wrap">
                            <span class="name">Ми відкриті до співвпраці</span>
                            <p>Готові розглянути будь-які форми співробітництва з Вами на взаємовигідних умовах. </p>
                        </div>
                    </div>

                    <div class="grid-items">
                        <div class="inner-wrap">
                            <span class="name">Партнерська програма</span>
                            <p>Працюємо з партнерами як малого і середнього бізнесу, так і з великими підприємствами.</p>
                        </div>
                    </div>

                    <div class="grid-items">
                        <div class="inner-wrap">
                            <span class="name">Цінова політика</span>
                            <p>Компанія має лояльну ціновою політикою. Для оптових покупців надається гнучка система знижок! </p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="clearfix"></div>
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
                <div class="wrap-form">

                    <h4 class="small-title black text-cenetr">Інформація Для Оптових покупців</h4>

                    <form action="{{ route('message') }}" method="POST" class="contact-manager">
                        @csrf

                        <input name="name" type="text" class="half mr20" placeholder="Ім'я">
                        <input name="email" type="email" required class="half" placeholder="Email">
                        <input name="title" type="text" placeholder="Тема повідомлення">
                        <textarea name="text" required placeholder="Повідомлення" data-validation="required"></textarea>

                        <button type="submit" class="yellow-btn blue-hover">Надіслати</button>
                    </form>

                </div>


            </div>


        </div>

    </div>

</section>

@endsection
