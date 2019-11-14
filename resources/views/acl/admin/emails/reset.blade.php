@extends('beautymail::templates.sunny')

@section('content')

    @include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Password Reset!',
        'level' => 'h1',
    ])

    @include('beautymail::templates.sunny.contentStart')

        <p>To reset your password, please click the button below. If you did not request a password reset, you can safely ignore this email - nothing will be changed.</p>
        <p>Or point your browser to this address: <br /> {{ route('sentinel.reset.form', [$hash, urlencode($code)]) }}</p>
		<p>Thank you, <br />
			~Chagua Chuo</p>

    @include('beautymail::templates.sunny.contentEnd')

    @include('beautymail::templates.sunny.button', [
        	'title' => 'Reset Password',
        	'link' => '{{ route('sentinel.reset.form', [$hash, urlencode($code)]) }}'
    ])

@stop