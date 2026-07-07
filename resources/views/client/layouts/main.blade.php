<!DOCTYPE html>
<html lang="uk">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
        <link rel="manifest" href="/favicon/site.webmanifest">
        <link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#5bbad5">
        <link rel="shortcut icon" href="/favicon/favicon.ico">
        <meta name="msapplication-TileColor" content="#e19a5e">
        <meta name="msapplication-config" content="/favicon/browserconfig.xml">
        <meta name="theme-color" content="#ffffff">

        @php
            $canonical = $canonical ?? \App\Support\SeoContent::canonical();
            $metaTitle = $title ?? \App\Support\SeoContent::site('name');
            $metaDescription = $description ?? \App\Support\SeoContent::site('description');
            $metaImage = $ogImage ?? \App\Support\SeoContent::canonical(\App\Support\SeoContent::site('default_image'));
            $pageSchema = \App\Support\SeoContent::defaultPageSchemas($schema ?? []);
        @endphp

        <title>{{ $metaTitle }}</title>
        
        <meta name="description" content="{{ $metaDescription }}" />
        <meta name="keywords" content="{{ $keywords ?? '' }}" />
        <link rel="canonical" href="{{ $canonical }}" />
        <meta property="og:title" content="{{ $ogTitle ?? $metaTitle }}" />
        <meta property="og:description" content="{{ $ogDescription ?? $metaDescription }}" />
        <meta property="og:image" content="{{ $metaImage }}" />
        <meta property="og:type" content="{{ $ogType ?? 'website' }}" />
        <meta property="og:url" content="{{ $canonical }}" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="{{ $ogTitle ?? $metaTitle }}" />
        <meta name="twitter:description" content="{{ $ogDescription ?? $metaDescription }}" />
        <meta name="twitter:image" content="{{ $metaImage }}" />

        @foreach($pageSchema as $schemaItem)
            <script type="application/ld+json">{!! json_encode($schemaItem, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
        @endforeach

        <!-- CSRF Stuff -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script>window.Laravel = { csrfToken: '{{ csrf_token() }}' }</script>

        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('GA_KEY') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', '{{ env('GA_KEY') }}');
        </script>

        <!-- Latest compiled and minified CSS -->
        <link
            rel="stylesheet"
            href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
            integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
            crossorigin="anonymous" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.2.0/css/ion.rangeSlider.min.css" integrity="sha512-zefUbYkuQwf5UTkKYwrn5+rtNJmUPhhB+F2OvdCdWFEjH/N+hg5NMiNEMWb+MhHTCD/r7zwlqIvdsNkefbDA9w==" crossorigin="anonymous" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.2.0/css/ion.rangeSlider.skinHTML5.min.css" integrity="sha512-VktyJxR067rnI8JJc7cras7N1mZerxlt27MyPEoLhBfd0brM3MMnHkIqLoD7yu7eVqM+R91IJaB3Huzy5lAuDQ==" crossorigin="anonymous" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.carousel.min.css" integrity="sha512-GqP/pjlymwlPb6Vd7KmT5YbapvowpteRq9ffvufiXYZp0YpMTtR9tI6/v3U3hFi1N9MQmXum/yBfELxoY+S1Mw==" crossorigin="anonymous" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.theme.default.min.css" integrity="sha512-GQz6nApkdT7cWN1Cnj/DOAkyfzNOoq+txIhSEK1G4HTCbSHVGpsrvirptbAP60Nu7qbw0+XlAAPGUmLU2L5l4g==" crossorigin="anonymous" />

        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
            integrity="sha512-SfTiTlX6kk+qitfevl/7LibUOeJWlt9rbyDn92a1DqWOw9vWG2MFoays0sgObmWazO5BQPiFucnnEAjpAB+/Sw=="
            crossorigin="anonymous" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css" integrity="sha512-ARJR74swou2y0Q2V9k0GbzQ/5vJ2RBSoCWokg4zkfM29Fb3vZEQyv0iWBMW/yvKgyHSR/7D64pFMmU8nYmbRkg==" crossorigin="anonymous" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/8.3.0/nouislider.min.css" integrity="sha512-5+qhRX5a0+xDxResCu4Tfkm0VSl69GB21dHJ4Ks+khYzK98N+ND+jm7C4xOAVbNHxWClAdhK6bGxLUNRd9SqPg==" crossorigin="anonymous" />

        <link rel="stylesheet" href="{{ mix('assets/client/app.css') }}" />

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <body>

        <div id="wrapper">
            @include('client.shared.header')

            @include('client.shared.status')

            @yield('content')

            @include('client.shared.footer')
        </div>

        @yield('scripts-for-map')

        <script
            src="https://code.jquery.com/jquery-2.2.4.min.js"
            integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
            crossorigin="anonymous"></script>

        <script
            src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js"
            type="text/javascript"></script>

        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/jquery.touchswipe/1.6.18/jquery.touchSwipe.min.js"
            integrity="sha512-BNs09vwMdfabwyH36BehO8DJbV5S6RaO0vXqPqiMhYC8mZ4WsfwwPEVhMOl5D7gyrTSDDv98f3HgaOUDlFhywg=="
            crossorigin="anonymous"></script>

        <script
            src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.2.0/js/ion.rangeSlider.min.js" integrity="sha512-rTJRUA79pAymwxP29qwUCbYbAuu+ecKfjbQSXM52b1GOIJOetkL40utFFSJR8yCyJjJBl7Pn8eYfPnYN/iYEyQ==" crossorigin="anonymous"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/owl.carousel.min.js" integrity="sha512-lo4YgiwkxsVIJ5mex2b+VHUKlInSK2pFtkGFRzHsAL64/ZO5vaiCPmdGP3qZq1h9MzZzghrpDP336ScWugUMTg==" crossorigin="anonymous"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js" integrity="sha512-yDlE7vpGDP7o2eftkCiPZ+yuUyEcaBwoJoIhdXv71KZWugFqEphIS3PU60lEkFaz8RxaVsMpSvQxMBaKVwA5xg==" crossorigin="anonymous"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/8.3.0/nouislider.min.js" integrity="sha512-TxK27pfLznRrTtw2L12/ljACll+N+9xLxm27LOOYs7Z9IzNVRgpYdOJAm/dohXADItoXcZb9N1m/QlyEehMKBQ==" crossorigin="anonymous"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/wnumb/1.2.0/wNumb.min.js" integrity="sha512-igVQ7hyQVijOUlfg3OmcTZLwYJIBXU63xL9RC12xBHNpmGJAktDnzl9Iw0J4yrSaQtDxTTVlwhY730vphoVqJQ==" crossorigin="anonymous"></script>

        <script type="text/javascript" src="{{ mix('assets/client/app.js') }}"></script>

    </body>
</html>
