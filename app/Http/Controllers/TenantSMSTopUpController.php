<?php

namespace App\Http\Controllers;

use Alert;
use App\Tenant;
use App\TenantSMS;
use Illuminate\Http\Request;
use App\Http\Requests\TenantSMSTopUpFormRequest as TenantSMSTopUpFormRequest;

class TenantSMSTopUpController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // You must have admin access to proceed
        $this->middleware('sentry.auth');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function topUp($id)
    {
        $tenant = Tenant::where('id', $id)->first();

        return view('tenants.topup', compact('tenant'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function postTopUp(TenantSMSTopUpFormRequest $request, $id)
    {
        /* Top up Balance */
        $sms = TenantSMS::where('tenant_id', $request->tenant)->first();

        $sms->balance = $sms->balance + $request->balance;

        $sms->save();

        Alert::success('Tenant SMS Balance Updated Successfully!');

        return redirect()->action(
            'TenantController@show', ['id' => $sms->tenant->id]
        );
    }
}
