@extends('client.layouts.main')

@section('content')
    <div class="register container">

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="row">
                    <h2 class="col-sm-offset-2 col-sm-10">{{ __('Register') }}</h2>
                </div>
                <form class="row"
                      method="POST"
                      action="{{ route('register') }}"
                >
                    @csrf

                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">{{ __('Name') }}</label>
                        <div class="col-sm-10">
                            <input type="text"
                                   class="form-control"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   required
                                   autocomplete="name"
                                   autofocus
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label">{{ __('E-Mail Address') }}</label>
                        <div class="col-sm-10">
                            <input type="email"
                                   class="form-control"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   autocomplete="email"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="col-sm-2 control-label">{{ __('Password') }}</label>
                        <div class="col-sm-10">
                            <input id="password"
                                   type="password"
                                   class="form-control"
                                   name="password"
                                   required
                                   autocomplete="new-password"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password-confirm" class="col-sm-2 control-label">{{ __('Confirm Password') }}</label>
                        <div class="col-sm-10">
                            <input id="password-confirm"
                                   type="password"
                                   class="form-control"
                                   name="password_confirmation"
                                   required
                                   autocomplete="new-password"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary">{{ __('Register') }}</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>
@endsection
