@extends('client.layouts.main')
@section('content')
    @include('client.shared.breadcrumb', $breadcrumbs ?? [])

<section class="page-cont about-wrap">

    <div class="container">
        <div class="row">

            <div class="col-xs-12 col-md-6">

                <p class="italic">
                    <i>Шановні відвідувачі, ми раді вітати Вас на нашому сайті. Нашій компанії вже 10 років, і за період свого існування отримала добру оцінку від наших партнерів. Ми гідно представляємо продукцію відомих виробників на території України.</i>
                    <br>
                    <br>
                    Наша компанія опрацювала та встановила власну лінію виробництва вхідних дверей ТМ «Mert Door». Технологічні можливості дозволяють виготовляти якісні ексклюзивні металеві вхідні двері двох груп з МДФ накладками. Розроблені двері застосовуються як у внутрішніх так і у зовнішніх приміщеннях, чим відрізняються у своїх категоріях. При їх виробництві використовуються найкращі матеріали.
                </p>

            </div>

            <div class="col-xs-12 col-md-6">

                <p>
                    Наші двері вирізняються високую надійністю і довговічністю. Вражаючий досвід роботи, відмінно поєднується з сучасним підходом до виробництва дверей. Професіонали, що випускають двері ТМ «Mert Door» йдуть в ногу з часом, відслідковуються та впроваджуються нові технології виробництва. Обладнання, що використовується для вир  обництва дверей ТМ «Mert Door» відповідає всім нормам та європейській якісті продукту.
                    <br>
                    <br>
                    Модельний ряд «Mert Door» відображає преваги класичних тенденцій, але в той же час ми пропонуємо нашим партнерам двері різних дизайнерських стилів і напрямків. На даний час ми пропонуємо 100 стандартних моделей та існує можливість виробництва ексклюзиву під замовлення (з власним малюнком чи індетифікацією для певних іміджевих стилів).
                </p>
            </div>

            <div class="col-xs-12">
                <img src="/images/content/slider-3.jpg" class="page-img" alt="">
            </div>

            <div class="col-xs-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
                <p>
                    Компанія не зупиняється на  досягнутому і розробляє нові моделі, розширює базу представництв по   Україні та за її межами. Цінуємо своє ім’я і репутацію, стараємося будувати відносини з нашими клієнтами    і партнерами на принципах глибокої поваги і дотримання покладених на себе зобов’язань.
                    <br><br>
                    Індивідуально працюємо з кожним клієнтом, беручи до уваги регіон, асортимент та пропозиції інших постачальників. Ми надаємо рекламну продукцію на наш товар. Готові розглянути будь-які форми співробітництва з Вами. Наша компанія орієнтована на широке коло покупців і відрізняється лояльною ціновою політикою. У нас можна знайти як економічні варіанти, так і клас «Преміум» і «Люкс».
                    <br><br>
                    Ми представлені командою спеціалістів і однодумців, виграємо не кількістю, а якістю. Незважаючи на невеликий колектив, підприємство працює на найвищому рівні і гарантує результат. Відповідальність, готовність вирішувати задачі будь-якої складності, постійне вдосконалення і оптимізація робочих процесів — наше професійне кредо і запорука успішного розвитку нашої компанії. Ми завжди орієнтуємось на кращий світовий досвід і прагнемо до розвитку нових напрямків і завоювання нових ринків. Це робить нас надійними партнерами як для малого і середнього бізнесу, так і для великих підприємств.
                </p>
            </div>

            <div class="col-xs-12 wrap-our-advantages">

                <h4 class="small-title black text-center">Наші переваги</h4>

                <div class="wrap-grid">

                    <div class="grid-items">
                        <div class="inner-wrap">
                            <div class="img-box"><img src="/images/icons/wallet.svg" alt=""></div>
                            <div class="name">Доступні ціни</div>
                        </div>
                    </div>

                    <div class="grid-items">
                        <div class="inner-wrap">
                            <div class="img-box"><img src="/images/icons/catalog.svg" alt=""></div>
                            <div class="name">Широкий асортимент</div>
                        </div>
                    </div>

                    <div class="grid-items">
                        <div class="inner-wrap">
                            <div class="img-box"><img src="/images/icons/door.svg" alt=""></div>
                            <div class="name">Власна лінія дверей ТМ «Metr Door» </div>
                        </div>
                    </div>

                    <div class="grid-items">
                        <div class="inner-wrap">
                            <div class="img-box"><img src="/images/icons/delivery-truck.svg" alt=""></div>
                            <div class="name">Безкоштовна доставка по Україні</div>
                        </div>
                    </div>

                    <div class="grid-items">
                        <div class="inner-wrap">
                            <div class="img-box"><img src="/images/icons/money-bag.svg" alt=""></div>
                            <div class="name">Гнучка система знижок</div>
                        </div>
                    </div>

                    <div class="grid-items">
                        <div class="inner-wrap">
                            <div class="img-box"><img src="/images/icons/lock.svg" alt=""></div>
                            <div class="name">Гарантія на продукцію</div>
                        </div>
                    </div>

                    <div class="grid-items">
                        <div class="inner-wrap">
                            <div class="img-box"><img src="/images/icons/door-knob.svg" alt=""></div>
                            <div class="name">Екслюзивні замовлення</div>
                        </div>
                    </div>

                    <div class="grid-items">
                        <div class="inner-wrap">
                            <div class="img-box"><img src="/images/icons/handshake.svg" alt=""></div>
                            <div class="name">Партнерська програма</div>
                        </div>
                    </div>

                </div>

            </div>


            <div class="clearfix"></div>
        </div>

    </div>

</section>

@endsection
