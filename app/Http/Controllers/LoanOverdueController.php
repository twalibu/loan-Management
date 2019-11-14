<?php

namespace App\Http\Controllers;

use Alert;
use Sentinel;
use Redirect;
use App\LoanOverdue;
use Illuminate\Http\Request;
use App\Http\Requests\PenaltFormRequest as PenaltFormRequest;
use App\Http\Requests\PenaltEditFormRequest as PenaltEditFormRequest;

class LoanOverdueController extends Controller
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

        $penalts = LoanOverdue::where('tenant_id', $tenant)->get();

        return view('settings.tenant.loanOverdues.index', compact('penalts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('settings.tenant.loanOverdues.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PenaltFormRequest $request)
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $penalt = new LoanOverdue;

        $penalt->penalt                = (($request->penalt)/100);
        $penalt->tenant_id           = $tenant;

        $penalt->save();

        Alert::success('New Loan Overdue Penalt Registered!');

        return Redirect::to('loanOverdues');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $penalt = LoanOverdue::findorfail($id);

        return view('settings.tenant.loanOverdues.edit', compact('penalt'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PenaltEditFormRequest $request, $id)
    {
        $penalt = LoanOverdue::findorfail($id);

        $penalt->penalt                = (($request->penalt)/100);

        $penalt->save();

        Alert::success('Loan Overdue Penalt Edited Successfully!');

        return Redirect::to('loanOverdues');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $penalt = LoanOverdue::findorfail($id);

        if ($penalt->loan > 0) {
            Alert::error('Loan Overdue Penalt has Loans Registered. Please Remove The Loans First!', 'Error')->persistent('Close');

            return redirect()->back();
        }

        $penalt->delete();

        Alert::success('Loan Overdue Penalt Removed Successfully!');

        return Redirect::to('loanOverdues');
    }
}
