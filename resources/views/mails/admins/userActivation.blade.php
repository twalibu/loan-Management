@extends('beautymail::templates.sunny')

@section('content')

    @include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Welcome to Loan Alert',
        'level' => 'h1',
    ])

    @include('beautymail::templates.sunny.contentStart')

        <p>
            Hello {{ $last_name }}, 
        </p>

        <p>
            Welcome to Loan Alert Platform. You have been registered. To start using the platform please activate your account below.
        </p>

        <p>
            Your Login Details are:-<br>
            <b>E-mail: </b>{{ $email }}<br>
            <b>Password: </b>Please Get Password from Your Admin!
        </p>

    @include('beautymail::templates.sunny.contentEnd')

    @include('beautymail::templates.sunny.button', [
            'title' => 'Activate Account',
            'link' => url('admin/users/activate/'.$hash.'/'.$code)
        ])

    @include('beautymail::templates.sunny.contentStart')

        <p>
            <br><i>Or point your browser to this address: <br /> {{ route('sentinel.activate', [$hash, urlencode($code)]) }} </i>
        </p>

        <p>
            Thank you.<br> <b>Loan Alert Team!</b>
        </p>

    @include('beautymail::templates.sunny.contentEnd')
@stop
