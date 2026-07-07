@extends('client.layouts.main')
@section('content')
    @include('client.shared.breadcrumb', ['breadcrumbs' => $breadcrumbs ?? []])

    <section class="">
        <div class="container">

            <div class="row">

                <div class="col-xs-12 col-md-3 sidebar">

                    <a
                        class="yellow-btn blue-hover open-filter"
                        data-toggle="collapse"
                        href="#multiCollapse"
                        role="button"
                        aria-expanded="false"
                        aria-controls="multiCollapse"
                    >Фільтр</a>

                    <div class="inner-wrap collapse multi-collapse" id="multiCollapse">

                        <form id="filter" method="GET" action="{{ route('catalog') }}">

                            @if($min !== null && $max !== null && $min !== $max)
                                <div class="filter-box open">
                                    <a href="#" class="title-filter">Ціна</a>
                                    <div class="cont-filter">
                                        <div class="wrap-slider-range">
                                            <div
                                                id="slider-range"
                                                data-min="{{ $min }}"
                                                data-max="{{ $max }}"
                                                data-start-min="{{ $startMin }}"
                                                data-start-max="{{ $startMax }}"
                                            ></div>

                                            <div class="slider-labels">
                                                <div class="caption">
                                                    Від<span id="slider-range-value1"></span>
                                                </div>
                                                <div class="caption">
                                                    до <span id="slider-range-value2"></span>
                                                </div>
                                            </div>

                                            <input type="hidden" id="min-price" name="min" value="">
                                            <input type="hidden" id="max-price" name="max" value="">

                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($catalogForFilter && count($catalogForFilter) > 0)
                                <div class="filter-box open">
                                    <a href="#" class="title-filter">Тип дверей</a>
                                    <div class="cont-filter">

                                        <div class="wrap-radio">
                                            <input type="radio" name="catalog" id="radio-0" value="" checked>
                                            <label for="radio-0">Усі</label>
                                        </div>

                                        @foreach($catalogForFilter ?? [] as $item)
                                            <div class="wrap-radio">
                                                <input
                                                    type="radio"
                                                    name="catalog"
                                                    id="radio-{{ $item->id }}"
                                                    value="{{ $item->id }}"
                                                    @if(isset($params['catalog']) && $params['catalog'] == $item->id) checked @endif
                                                >

                                                <label for="radio-{{ $item->id }}">{{ $item->title }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($categoriesForFilter && count($categoriesForFilter) > 0)
                                <div class="filter-box open">
                                    <a href="#" class="title-filter">Група дверей</a>
                                    <div class="cont-filter">

                                        @foreach($categoriesForFilter ?? [] as $item)
                                            <div class="wrap-checkbox">
                                                <input
                                                    type="checkbox"
                                                    name="categories[]"
                                                    id="categories-checkbox-{{ $item->id }}"
                                                    value="{{ $item->id }}"
                                                    @if(isset($params['categories']) && in_array($item->id, $params['categories'])) checked @endif
                                                >

                                                <label for="categories-checkbox-{{ $item->id }}">{{ $item->title }}</label>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>
                            @endif

                            @if ($sizesForFilter && count($sizesForFilter) > 0)
                                <div class="filter-box open">
                                    <a href="#" class="title-filter">Розмір</a>
                                    <div class="cont-filter">

                                        @foreach($sizesForFilter ?? [] as $item)
                                            <div class="wrap-checkbox">
                                                <input
                                                    type="checkbox"
                                                    name="sizes[]"
                                                    id="sizes-checkbox-{{ $item->id }}"
                                                    value="{{ $item->id }}"
                                                    @if(isset($params['sizes']) && in_array($item->id, $params['sizes'])) checked @endif
                                                >

                                                <label for="sizes-checkbox-{{ $item->id }}">{{ $item->title }}</label>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>
                            @endif

                        </form>

                    </div>
                </div>

                <div class="col-xs-12 col-md-9 main-content">

                    <!--
                    <div class="sorting-box">
                        <span>Сортування :</span>
                        <select class="selectpicker">
                            <option value="1">Популярні</option>
                            <option value="2">Непопулярні</option>
                        </select>
                    </div>
                    -->

                    <div class="wrap-grid col-grid-3">

                        @foreach($list as $item)
                            <div class="grid-items">
                                <div class="inner-wrap">
                                    <div class="bg-wrap">

                                        @if($item->label !== 0)
                                            <div
                                                class="label-box {{ $item->label_class }}">{{ $item->label_text }}</div>
                                        @endif

                                        <div class="social-box">
                                            <span>Поділитись</span> <i class="fa fa-share-alt" aria-hidden="true"></i>
                                            <ul>
                                                <li>
                                                    <a href="{{ $item->facebook_share_link }}" target="_blank">
                                                        <i class="fa fa-facebook-f"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ $item->twitter_share_link }}" target="_blank">
                                                        <i class="fa fa-twitter"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ $item->telegram_share_link }}" target="_blank">
                                                        <i class="fa fa-telegram"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="img-box product-image product-item">
                                            <a href="{{ $item->location }}">
                                                <img src="{{ $item->cover }}" alt="Вхідні металеві двері {{ $item->title }}" title="{{ $item->title }}" loading="lazy">
                                            </a>
                                        </div>

                                        <div class="button-wrap">

                                            <!--
                                            <div class="price">7 400 грн <span>6000 грн</span></div>
                                            -->

                                            <div class="price">{{ $item->price_text }}</div>
                                            <div class="order">
                                                <a
                                                    href="#"
                                                    data-toggle="modal"
                                                    data-target="#order-form"
                                                    data-id="{{$item->id}}"
                                                >Запитати ціну</a>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="title-box">
                                        <a href="{{ $item->location }}">{{ $item->title }}</a>
                                        <div class="product-card-type">Вхідні / технічні двері</div>
                                        <div class="product-card-badges">
                                            <span>ДСТУ</span>
                                            <span>Гарантія</span>
                                            <span>Покриття</span>
                                        </div>
                                        <div class="product-card-actions">
                                            <a href="{{ $item->location }}">Детальніше</a>
                                            <a href="#" data-toggle="modal" data-target="#order-form" data-id="{{$item->id}}">Консультація</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>

                    <div class="pagination-box">
                        {{ $list->appends($params ?? [])->links('client.shared.pagination') }}
                    </div>
                </div>

            </div>

        </div>
    </section>

    @include('client.shared.faq', ['faq' => $faq ?? []])

    @include('client.products.shared.modal')

@endsection
