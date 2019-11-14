<?php

namespace App\Http\Controllers;

use Alert;
use Excel;
use Carbon;
use Sentinel;
use App\LoanPaymentSchedule;

class TodayController extends Controller
{
    public function __construct()
    {
        // Middleware
        $this->middleware('sentinel.auth');
        $this->middleware('sentinel.access:loans.view');
        $this->middleware('sentinel.access:clients.view');
    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function payments()
    {
    	$today = Carbon::today()->toDateString();

        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $schedules = LoanPaymentSchedule::where([['tenant_id', $tenant],['date', $today]])->get();

        return view('tools.tenant.todays', compact('schedules'));
    }
}
