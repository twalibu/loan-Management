@extends('masters.tenant.auth')

@section('title')Resend Activation Instructions @stop

@section('content')
    <div class="login-box-body">
        <p class="login-box-msg">Resend Activation Instructions</p>

        <form action="{{ route('auth.activation.resend') }}" method="post">
            <input name="_token" value="{{ csrf_token() }}" type="hidden">
            <div class="form-group has-feedback">
                <input type="email" class="form-control" placeholder="Email">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="row">
                <button type="submit" class="btn btn-primary btn-block btn-lg">Resend</button>
            </div>
        </form>
  </div>
@stop