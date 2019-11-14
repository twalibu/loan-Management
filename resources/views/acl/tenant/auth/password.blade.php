@extends('masters.tenant.auth')

@section('title')Create A New Password @stop

@section('content')
    <div class="login-box-body">
        <p class="login-box-msg">Reset Your Password</p>

        <form action="{{ route('auth.password.reset.attempt', $code) }}" method="post">
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
                <input name="password" type="password" class="form-control" placeholder="Password">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input name="password_confirmation" type="password" class="form-control" placeholder="Password">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <button type="submit" class="btn btn-primary btn-block btn-lg">Save</button>
            </div>
        </form>
  </div>
@stop