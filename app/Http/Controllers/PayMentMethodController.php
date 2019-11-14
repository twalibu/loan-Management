<?php

namespace App\Http\Controllers;

use Alert;
use Sentinel;
use Redirect;
use App\PaymentMethod;
use Illuminate\Http\Request;
use App\Http\Requests\PaymentMethodFormRequest as PaymentMethodFormRequest;
use App\Http\Requests\PaymentMethodEditFormRequest as PaymentMethodEditFormRequest;

class PaymentMethodController extends Controller
{
    public function __construct()
    {
        // Middleware
        $this->middleware('sentinel.auth');
        $this->middleware('sentinel.access:settings.access');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $methods = PaymentMethod::where('tenant_id', $tenant)->get();

        return view('settings.tenant.paymentMethods.index', compact('methods'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('settings.tenant.paymentMethods.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PaymentMethodFormRequest $request)
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $method = new PaymentMethod;

        $method->name                = $request->name;
        $method->account_number      = $request->account_number;
        $method->tenant_id           = $tenant;

        $method->save();

        Alert::success('New Payment Method Registered!');

        return Redirect::to('paymentMethods');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $method = PaymentMethod::findorfail($id);

        return view('settings.tenant.paymentMethods.edit', compact('method'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PaymentMethodEditFormRequest $request, $id)
    {
        $method = PaymentMethod::findorfail($id);

        $method->name                = $request->name;
        $method->account_number      = $request->account_number;

        $method->save();

        Alert::success('Payment Method Edited Successfully!');

        return Redirect::to('paymentMethods');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $method = PaymentMethod::findorfail($id);

        if ($method->payments->count() > 0) {
            Alert::error('Method has Payments Registered. Please Remove The Payments First!', 'Error')->persistent('Close');

            return redirect()->back();
        }

        $method->delete();

        Alert::success('Payment Method Removed Successfully!');

        return Redirect::to('paymentMethods');
    }
}
