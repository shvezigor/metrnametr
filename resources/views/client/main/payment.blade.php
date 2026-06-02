@extends('client.layouts.main')
@section('content')
    @include('client.shared.breadcrumb', $breadcrumbs ?? [])

<section class="page-cont payment-wrap">

    <div class="container">
        <div class="row">

            <div class="col-xs-12">
                <h4 class="small-title black">Види оплат</h4>
            </div>

            <div class="col-xs-12">
                <div class="wrap-grid col-grid-3">

                    <div class="grid-items">
                        <div class="inner-wrap">
                            <div class="img-box"><img src="/images/icons/credit-card.svg" alt=""></div>
                            <span class="name">Безготівковий розрахунок</span>
                            <p>Після оформлення замовлення, клієнт отримує рахунок фактуру (надсилаємо по електронній пошті або по факсу), який можна оплатити з розрахункового рахунку компанії замовника.</p>
                        </div>
                    </div>

                    <div class="grid-items">
                        <div class="inner-wrap">
                            <div class="img-box"><img src="/images/icons/bank.svg" alt=""></div>
                            <span class="name">Банківський перерахунок</span>
                            <p>Після оформлення замовлення, клієнт отримує рахунок, який можна оплатити в будь-якому банку. Разом з товаром надсилаються оригінали документів (накладна, гарантійний талон).</p>
                        </div>
                    </div>

                    <div class="grid-items">
                        <div class="inner-wrap">
                            <div class="img-box"><img src="/images/icons/wallet.svg" alt=""></div>
                            <span class="name">Готівкою по факту доставки</span>
                            <p>Для оформлення такого замовлення, обов’язково потрібно внести аванс, в розмірі 10% (для жителів м. Луцька), і 50% (для жителів інших міст і областей).</p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="clearfix"></div>
        </div>

        <div class="row">

            <div class="col-xs-12">
                <h4 class="small-title black">Встановлення та доставка</h4>
            </div>

            <div class="col-xs-12 col-sm-6 col-md-4 delivery-box">
                <div class="inner-wrap">
                    <span class="name">Доставка по м. Луцьк</span>
                    <p>Доставка по Луцьку здійснюється кожен день з 9:00 до 16:00 (послуга безкоштовна).</p>
                </div>
            </div>

            <div class="col-xs-12 col-sm-6 col-md-4 delivery-box">
                <div class="inner-wrap">
                    <span class="name">Доставка по Волинській області</span>
                    <p>Доставка за Луцьк (по Волинській області) коштує 10 грн / 1 км (в дві сторони) залежить від кілометражу.</p>
                </div>
            </div>

            <div class="col-xs-12 col-sm-6 col-md-4 delivery-box">
                <div class="inner-wrap">
                    <span class="name">Доставка по Україні</span>
                    <p>Доставка здійснюється перевізником Нова пошта, передоплата 50%, інші 50% вартості — при отриманні товару.</p>
                </div>
            </div>

            <div class="col-xs-12 col-sm-6 col-md-4 delivery-box col-md-offset-2">
                <div class="inner-wrap">
                    <span class="name">Монтаж </span>
                    <p>Монтаж дверей здійснюється в день доставки (за попереднім погодженням  з менеджером фірми).</p>
                </div>
            </div>

            <div class="col-xs-12 col-sm-6 col-md-4 delivery-box col-sm-offset-3 col-md-offset-0">
                <div class="inner-wrap">
                    <span class="name">Вартість встановлення</span>
                    <p>Встановлення дверей відбувається по фіксованому прайсу. Вартість послуги можна дізнатися у менеджера.</p>
                </div>
            </div>

        </div>

    </div>

</section>

@endsection
