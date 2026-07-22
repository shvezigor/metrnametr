@if(session()->has('success') || session()->has('error') || count($errors))
    <section class="status-info">
        <div class="container">
            <div class="row">
                <div class="col-md-offset-3 col-md-6">

                    @if(session()->has('success'))
                        <div
                            class="alert alert-success"
                            @if(session('conversion_event'))
                                data-ga-success-event="{{ session('conversion_event') }}"
                                data-form-type="{{ session('conversion_form_type', 'contact') }}"
                            @endif
                        >
                            {!! session()->get('success') !!}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session()->get('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (count($errors))
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <strong>Error !</strong> {{ $error }}
                            @endforeach
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </section>
@endif
