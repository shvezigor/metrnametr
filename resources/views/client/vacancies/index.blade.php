@extends('client.layouts.main')
@section('content')
    @include('client.shared.breadcrumb', ['breadcrumbs' => $breadcrumbs ?? ['Вакансії']])

    <section class="page-cont vacancies-page">

        <div class="container">
            <div class="row">

                <div class="col-xs-12">
                    <div class="small-title black">Вакансії</div>
                </div>

                <div class="col-xs-12">
                    <div class="wrap-grid vacancies-grid">
                        @foreach($list as $item)
                            <div class="grid-items">
                                <div class="inner-wrap">
                                    <a href="#"
                                       class="title"
                                       data-toggle="modal"
                                       data-target="#vacancy-form"
                                       data-vacancy="{{ $item }}"
                                       data-phone="{{ $phone }}">
                                        {{ $item->title }}
                                    </a>
                                    <div class="salary">Заробітна плата від</div>
                                    <div class="sum">{{ $item->salary_text }}<span> грн</span></div>
                                </div>
                            </div>

                        @endforeach
                    </div>
                </div>

                <div class="pagination-box">
                    {{ $list->links('client.shared.pagination') }}
                </div>

                <div class="clearfix"></div>
            </div>

        </div>

    </section>

    @include('client.vacancies.shared.modal')

@endsection
