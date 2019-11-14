@extends('beautymail::templates.sunny')

@section('content')

    @include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Welcome to Loan Alert',
        'level' => 'h1',
    ])

    @include('beautymail::templates.sunny.contentStart')

        <p>
            Hello {{ $first_name }} {{ $last_name }} ({{ $position }}),
        </p>

        <p>
            Welcome to Loan Alert Platform. Your Organization (<b>{{ $tenant }}</b>) has been registered.
        </p>

        <p>
            To start using the platform please activate your account below. One of our administrator will contact you soon to help you with the initial setup.
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
