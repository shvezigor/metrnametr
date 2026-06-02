@extends('client.layouts.main')

@section('content')
    <div class="login container">

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="row">
                    <h2 class="col-sm-offset-2 col-sm-10">{{ __('Login') }}</h2>
                </div>
                <form class="row"
                      method="POST"
                      action="{{ route('login') }}"
                >
                    @csrf

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
                                   autofocus
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
                                   autocomplete="current-password"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10 remember">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember">{{ __('Remember Me') }}</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary">{{ __('Login') }}</button>
                            @if (Route::has('password.request'))
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>
@endsection
