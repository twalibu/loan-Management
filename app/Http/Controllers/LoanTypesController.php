<?php

namespace App\Http\Controllers;

use Alert;
use Sentinel;
use Redirect;
use App\LoanType;
use Illuminate\Http\Request;
use App\Http\Requests\LoanTypeFormRequest as LoanTypeFormRequest;
use App\Http\Requests\LoanTypeEditFormRequest as LoanTypeEditFormRequest;

class LoanTypesController extends Controller
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

        $types = LoanType::where('tenant_id', $tenant)->get();

        return view('settings.tenant.loanTypes.index', compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('settings.tenant.loanTypes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LoanTypeFormRequest $request)
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $type = new LoanType;

        $type->name                	= $request->name;
        $type->interest             = (($request->interest)/100);
        $type->duration             = $request->duration;
        $type->tenant_id            = $tenant;

        $type->save();

        Alert::success('New Loan Type Registered!');

        return Redirect::to('loanTypes');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $type = LoanType::findorfail($id);

        return view('settings.tenant.loanTypes.edit', compact('type'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LoanTypeEditFormRequest $request, $id)
    {
        $type = LoanType::findorfail($id);

        $type->name                	= $request->name;
        $type->interest             = (($request->interest)/100);
        $type->duration             = $request->duration;

        $type->save();

        Alert::success('Loan Type Edited Successfully!');

        return Redirect::to('loanTypes');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $type = LoanType::findorfail($id);

        if ($type->loan->count() > 0) {
            Alert::error('Loan Type has Loans Registered. Please Remove The Loans First!', 'Error')->persistent('Close');

            return redirect()->back();
        }

        $type->delete();

        Alert::success('Loan Type Removed Successfully!');

        return Redirect::to('loanTypes');
    }
}
