@extends(config('sentinel.layout'))

{{-- Web site Title --}}
@section('title')
@parent
Register
@stop

{{-- Content --}}
@section('content')
<div class="row">
    <div class="col l6 offset-l3 m8 offset-m2 s12">
        <form method="POST" action="{{ route('sentinel.register.user') }}" accept-charset="UTF-8">
            <input name="_token" value="{{ csrf_token() }}" type="hidden">

            <h4>Register New Account</h4>

            <div class="row">
                <div class="input-field col s12">
                    <input id="username" name="username" type="text" class="validate" value="{{ Request::old('username') }}">
                    <label for="name">Username</label>
                    {{ ($errors->has('username') ? $errors->first('username') : '') }}
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <input id="email" name="email" type="text" class="validate" value="{{ Request::old('email') }}">
                    <label for="name">E-Mail</label>
                    {{ ($errors->has('email') ? $errors->first('email') : '') }}
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <input id="password" name="password" type="password" class="validate">
                    <label for="name">Password</label>
                    {{ ($errors->has('password') ? $errors->first('password') : '') }}
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <input id="password_confirmation" name="password_confirmation" type="password" class="validate">
                    <label for="name">Confirm Password</label>
                    {{ ($errors->has('password_confirmation') ? $errors->first('password_confirmation') : '') }}
                </div>
            </div>

            <p>
                <button class="btn waves-effect waves-light red" type="submit" name="action">Register
                    <i class="mdi-content-send right"></i>
                </button>
            </p>

        </form>
    </div>
</div>
@stop