<?php

namespace App\Http\Controllers;

use Alert;
use Redirect;
use Sentinel;

use App\TenantSale;
use Illuminate\Http\Request;
use App\Http\Requests\TenantSMSSaleRequestForm as TenantSMSSaleRequestForm;

class TenantSaleController extends Controller
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

        $sale = TenantSale::where('tenant_id', $tenant)->first();

        return view('settings.tenant.sales.index', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $sale = TenantSale::findorfail($id);

        return view('settings.tenant.sales.edit', compact('sale'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TenantSMSSaleRequestForm $request, $id)
    {
        $sale = TenantSale::findorfail($id);

        $sale->phone_number    = $request->phone_number;
        $sale->email           = $request->email;

        $sale->save();

        Alert::success('Sales Contacts Edited Successfully!');

        return Redirect::to('sales');
    }
}
