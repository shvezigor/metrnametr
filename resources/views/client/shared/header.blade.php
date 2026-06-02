<div class="container top-line">
    <div class="row">
        <div class="col-xs-12">

            <!--
            <div class="lang-box">
                <div class="dropdown show">
                    <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span>УКР <i class="fa fa-angle-down"></i> </span> <span><img src="/images/icons/ukrainian-flag.svg" alt=""></span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink2">
                        <a class="dropdown-item" href="#"><span>УКР </span></a>
                        <a class="dropdown-item" href="#"><span>РУ </span></a>
                        <a class="dropdown-item" href="#"><span>USA </span></a>
                    </div>
                </div>
            </div>

            <div class="money-box">
                <div class="dropdown show">
                    <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span>₴ UAH <i class="fa fa-angle-down"></i></span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item" href="#"><span>UAH</span></a>
                        <a class="dropdown-item" href="#"><span>EN</span></a>
                        <a class="dropdown-item" href="#"><span>RU</span></a>
                    </div>
                </div>
            </div>
            -->

            <div class="tel-box">

                @foreach(explode(',', \App\Models\Setting::getValue('phones')) as $item)
                    <span>{{ $item }}</span>
                @endforeach

            </div>

            <div class="social-box">
                <ul>
                    @if (\App\Models\Setting::existValue('facebook'))
                        <li><a href="{{ \App\Models\Setting::getValue('facebook') }}" target="_blank"><i class="fa fa-facebook-f"></i></a></li>
                    @endif

                    @if (\App\Models\Setting::existValue('instagram'))
                        <li><a href="{{ \App\Models\Setting::getValue('instagram') }}" target="_blank"><i class="fa fa-instagram"></i></a></li>
                    @endif

                    @if (\App\Models\Setting::existValue('youtube'))
                        <li><a href="{{ \App\Models\Setting::getValue('youtube') }}" target="_blank"><i class="fa fa-youtube-play"></i></a></li>
                    @endif

                    @if (\App\Models\Setting::existValue('telegram'))
                        <li><a href="{{ \App\Models\Setting::getValue('telegram') }}" target="_blank"><i class="fa fa-telegram"></i></a></li>
                    @endif
                </ul>
            </div>

        </div>
    </div>
</div>

<header class="">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 inner-wrap">

                <div class="logo-box">
                    <a href="{{ route('home') }}">
                        <img src="/images/logo-2.png" alt="">
                    </a>
                </div>

                <nav class="navbar navbar-default" role="navigation">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed my-button" data-toggle="collapse"
                                data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>
                    </div>

                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav navbar-left">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle{{ Request::is('catalog')? ' active' : '' }}" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    Каталог
                                    <span class="my-caret"></span>
                                </a>
                                <ul class="dropdown-menu mm-style">

                                    <li>
                                        Тип дверей
                                        <ul>
                                            <li><a href="{{ route('catalog') }}">Усі</a></li>
                                            @foreach($menuTypes as $item)
                                                <li><a href="{{ $item->location }}">{{ $item->title }}</a></li>
                                            @endforeach
                                        </ul>
                                    </li>


                                    @foreach($menuCatalog as $item)
                                        <li>
                                            {{ $item->title }}

                                            <ul>
                                                @foreach($item->categories()->published()->get() as $category)
                                                    <li><a href="/catalog?categories[]={{ $category->id }}">{{ $category->title }}</a></li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endforeach

                                </ul>
                            </li>
                        </ul>

                        <ul class="nav navbar-nav navbar-right">
                            <li><a class="{{ Request::is('guarantee')? 'active' : '' }}" href="{{ route('guarantee') }}">Гарантія</a></li>
                            <li><a class="{{ Request::is('payment')? 'active' : '' }}" href="{{ route('payment') }}">Оплата і доставка</a></li>
                            <li><a class="{{ Request::is('wholesale')? 'active' : '' }}" href="{{ route('wholesale') }}">Оптовий продаж</a></li>
                            <li><a class="{{ Request::is('about')? 'active' : '' }}" href="{{ route('about') }}">Про нас</a></li>
                            <li><a class="{{ Request::is('news')? 'active' : '' }}" href="{{ route('articles') }}">Новини</a></li>
                            <li><a class="{{ Request::is('vacancies')? 'active' : '' }}" href="{{ route('vacancies') }}">Вакансії</a></li>
                            <li><a class="{{ Request::is('contacts')? 'active' : '' }}" href="{{ route('contacts') }}">Контакти</a></li>
                        </ul>

                    </div><!--/.nav-collapse -->
                </nav>

            </div>
        </div>
    </div>
</header>
