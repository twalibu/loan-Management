@extends('beautymail::templates.sunny')

@section('content')

    @include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Loan Alert | New Account!',
        'level' => 'h1',
    ])

    @include('beautymail::templates.sunny.contentStart')

        <p>
            Hello {{ $last_name }},
        </p>

        <p>
            Welcome to Loan Alert Platform. Your have been register by your Organization (<b>{{ $tenant }}</b>).
        </p>

        <p>
            To start using the platform please activate your account below. If you need any help please send as an sms to our Tech Support Number: <b>{{ $tech_support }}</b> and we will call you right back.
        </p>

        <p>
            Your Login Details are:-<br>
            <b>E-mail: </b>{{ $email }}<br>
            <b>Password: </b>{{ $password }}
        </p>

        @include('beautymail::templates.sunny.contentEnd')

        @include('beautymail::templates.sunny.button', [
            'title' => 'Activate Account',
            'link' => url('activate/'.$code)
        ])

        @include('beautymail::templates.sunny.contentStart')

        <p>
            <br><i>Or point your browser to this address: <br /> {!! route('auth.activation.attempt', urlencode($code)) !!} </i>
        </p>

        <p>
            Thank you.<br> <b>Loan Alert Team!</b>
        </p>

    @include('beautymail::templates.sunny.contentEnd')

@stop
