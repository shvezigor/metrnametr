<section class="breadcrumb-wrap">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <ul class="breadcrumb">
                    <li><a href="{{route('home')}}">Головна</a></li>
                    @foreach($breadcrumbs ?? [] as $link => $title)
                        <li>
                            @if($loop->last)
                                {{ $title }}
                            @else
                                <a href="{{ $link }}">{{ $title }}</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
