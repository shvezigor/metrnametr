<footer>

    <div class="container-fluid wrap-cont-footer">
        <div class="background">
            <div class="layer" style="background: #fff;"></div>
        </div>
        <div class="row">

            <div class="container">
                <div class="row">

                    <div class="col-sm-2 col-md-2">
                        <div class="logo-box"><a href="{{ route('home') }}"><img src="/images/logo-2.png" width="309" height="360" alt="Метр на Метр"></a></div>
                    </div>

                    <div class="col-sm-3 col-md-2">
                        <ul>
                            <li><a href="{{ route('catalog') }}">Каталог</a></li>
                            <li><a href="{{ route('guarantee') }}">Гарантія</a></li>
                            <li><a href="{{ route('payment') }}">Оплата і доставка</a></li>
                            <li><a href="{{ route('wholesale') }}">Оптовий продаж</a></li>
                        </ul>
                    </div>

                    <div class="col-sm-2 col-md-2">
                        <ul>
                            <li><a href="{{ route('about') }}">Про нас</a></li>
                            <li><a href="{{ route('knowledge.index') }}">База знань</a></li>
                            <li><a href="{{ route('articles') }}">Новини</a></li>
                            <li><a href="{{ route('vacancies') }}">Вакансії</a></li>
                            <li><a href="{{ route('contacts') }}">Контакти</a></li>
                            <li><a href="{{ route('for-ai-agents') }}">Для AI-агентів</a></li>
                        </ul>
                    </div>

                    <div class="col-sm-2 col-md-2">
                        <p><strong>Популярні запити</strong></p>
                        <ul>
                            <li><a href="/vkhidni-dveri-lutsk">Вхідні двері Луцьк</a></li>
                            <li><a href="/mizhkimnatni-dveri-lutsk">Міжкімнатні двері Луцьк</a></li>
                            <li><a href="/dveri-z-montazhem-lutsk">Двері з монтажем Луцьк</a></li>
                            <li><a href="/dveri-volyn">Двері Волинь</a></li>
                        </ul>
                    </div>

                    <div class="col-sm-3 col-md-3 col-md-offset-1 wrap-subscribe">
                        <p><a href="{{ route('real-works.index') }}">Наші роботи — встановлення дверей</a></p>
                        <p>Підпишіться на нашу розсилку, щоб ми могли проінформувати вас про наші новини та пропозиції</p>
                        <form action="{{ route('subscribe') }}" method="POST" class="subscribe-form">

                            @csrf

                            <input type="email" name="email" placeholder="email" value="{{ old('email') }}" />
                            <button type="submit" class="yellow-btn blue-hover">Підписатися</button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>


    <div class="container wrap-copyright">
        <div class="row">
            <div class="col-xs-6 col-sm-6 copyright">© {{ date('Y') }} Метр на Метр</div>
            <div class="col-xs-6 col-sm-6 social-box">
                <ul>

                    @if (\App\Models\Setting::existValue('facebook'))
                        <li><a href="{{ \App\Models\Setting::getValue('facebook') }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fa fa-facebook-f" aria-hidden="true"></i></a></li>
                    @endif

                    @if (\App\Models\Setting::existValue('instagram'))
                        <li><a href="{{ \App\Models\Setting::getValue('instagram') }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                    @endif

                    @if (\App\Models\Setting::existValue('youtube'))
                        <li><a href="{{ \App\Models\Setting::getValue('youtube') }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fa fa-youtube-play" aria-hidden="true"></i></a></li>
                    @endif

                    @if (\App\Models\Setting::existValue('telegram'))
                        <li><a href="{{ \App\Models\Setting::getValue('telegram') }}" target="_blank" rel="noopener noreferrer" aria-label="Telegram"><i class="fa fa-telegram" aria-hidden="true"></i></a></li>
                    @endif

                </ul>
            </div>
        </div>
    </div>

</footer>
