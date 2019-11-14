@extends('masters.tenant.auth')

@section('title')Password Reset @stop

@section('content')
    <div class="login-box-body">
        <p class="login-box-msg">Reset Your Password</p>

        <form action="{{ route('auth.password.request.attempt') }}" method="post">
            <input name="_token" value="{{ csrf_token() }}" type="hidden">
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <p><strong>Whoops!</strong> There were some problems with your input.</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="form-group has-feedback">
                <input name="email" type="email" class="form-control" placeholder="Email">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="row">
                <button type="submit" class="btn btn-primary btn-block btn-lg">Help</button>
            </div>
        </form>
  </div>
@stop