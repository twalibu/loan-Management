@extends('beautymail::templates.sunny')

@section('content')

    @include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Loan Alert | Contact Form!',
        'level' => 'h1',
    ])

    @include('beautymail::templates.sunny.contentStart')

        <p>
        	<h2>Full Name: </h2> {{ $name }}
        </p>

        <p>
        	<h2>Email: </h2> {{ $email }}
        </p>

        <p>
        	<h2>Message: </h2> {{ $text }}
        </p>

        <p>
            <br>
            Thank you.<br> <b>Loan Alert Team!</b>
        </p>

    @include('beautymail::templates.sunny.contentEnd')

@stop
