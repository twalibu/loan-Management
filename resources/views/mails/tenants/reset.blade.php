@extends('beautymail::templates.sunny')

@section('content')

    @include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Password Reset',
        'level' => 'h1',
    ])

    @include('beautymail::templates.sunny.contentStart')

        <p>
            Hello {{ $last_name }}, 
        </p>

        <p>
            You have requested a Password. To Change your Password, click the Button Below. If you did not make this request, ignor this email and your password wont be changed.
        </p>

    @include('beautymail::templates.sunny.contentEnd')

    @include('beautymail::templates.sunny.button', [
            'title' => 'Reset Password',
            'link' => url('password/reset/'.$code)
        ])

    @include('beautymail::templates.sunny.contentStart')

        <p>
            <br><i>Or point your browser to this address: <br /> {!! route('auth.password.reset.form', urlencode($code)) !!} </i>
        </p>

        <p>
            Thank you.<br> <b>Loan Alert Team!</b>
        </p>

    @include('beautymail::templates.sunny.contentEnd')
@stop