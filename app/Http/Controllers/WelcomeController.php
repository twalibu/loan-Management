<?php

namespace App\Http\Controllers;

use Alert;
use Redirect;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Show the application splash screen.
     *
     * @return Response
     */
    public function show()
    {
        return view('welcome');
    }

    public function contact(Request $request)
    {
    	$data = [
	                'name' =>  $request->input('contactname'),
	                'email' =>  $request->input('contactemail'),
	                'text' => $request->input('contactmessage')
	    ];

	    $beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
	    $beautymail->send('mails.contact', $data, function($message)
	    {
	        $message
	            ->from('no-reply@loan-alert.pro', 'Loan Alert')
	            ->to('ask@techlegend.co', 'TechLegend | Help Desk')
	            ->subject('Loan Alert | Contact Form');
	    });
    	
    	Alert::success('Message Succefully Received', 'Thank You!');
    	return Redirect::to('/');
    }
}
