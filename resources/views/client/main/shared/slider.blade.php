@if(\App\Models\Setting::existValue('slider'))

    <section class="wrap-primary-slider">

        <div class="container">
            <div class="row">
                <div class="col-xs-12">

                    <div class="primary-slider owl-carousel">

                        @foreach(\App\Models\Setting::getValue('slider') as $item)
                            @if(array_key_exists('image', $item) && $item['image'])
                                <div>
                                    <div class="img-box"><img src="{{ $item['image'] }}" alt="{{ $item['title'] }}"></div>
                                    <div class="cont-box">

                                        @if($item['label'])
                                            <div class="action">{{ $item['label'] }}</div>
                                        @endif

                                        @if($item['title'])
                                            <h3>{{ $item['title'] }}</h3>
                                        @endif

                                        @if($item['text'])
                                            <p>{{ $item['text'] }}</p>
                                        @endif

                                        @if($item['link'] && $item['button'])
                                            <a href="{{ $item['link'] }}" class="yellow-btn">{{ $item['button'] }}</a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach

                    </div>

                </div>
            </div>
        </div>

    </section>
@endif
