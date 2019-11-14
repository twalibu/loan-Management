<?php

namespace App\Http\Controllers;

use Alert;
use Carbon;
use Redirect;
use Sentinel;

use App\Loan;
use App\Client;
use App\LoanSummary;
use App\ScheduleAlert;
use App\LoanPaymentSchedule;
use Illuminate\Http\Request;
use App\Http\Requests\FastTrackFormRequest as FastTrackFormRequest;

class FastTrackController extends Controller
{
    public function __construct()
    {
        // Middleware
        $this->middleware('sentinel.auth');
        $this->middleware('sentinel.access:loans.create');
        $this->middleware('sentinel.access:clients.create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant;

        return view('tools.tenant.fastTrack', compact('tenant'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FastTrackFormRequest $request)
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $client = new Client;

        $client->first_name         = $request->first_name;
        $client->middle_name        = $request->middle_name;
        $client->last_name          = $request->last_name;
        $client->email              = $request->email;
        $client->phone_number       = $request->phone_number;
        if($request->phone_number_2){
            $client->phone_number_2     = $request->phone_number_2;
        }else {
            $client->phone_number_2 = 0;
        }
        $client->office_id         = $request->office;
        $client->tenant_id         = $tenant;

        $client->save();

        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $loan = new Loan;

        $loan->loan_identity        = $request->loan_identity;
        $loan->amount               = $request->amount;
        $loan->date_issued          = $request->date;
        $loan->client_id            = $client->id;
        $loan->penalt_id            = $request->penalt;
        $loan->type_id              = $request->type;
        $loan->office_id            = $request->office;
        $loan->tenant_id            = $tenant;

        $loan->save();

        $rate = ($loan->type->interest)/$loan->type->duration; // The interest rate per month
        $nper = $loan->type->duration; // Duration of Loan, in Months
        $pv = $loan->amount; // Loan Amount

        $monthly = round($this->PMT($rate, $nper, -$pv),2); // Monthly Payment

        for ($i=0; $i < $loan->type->duration; $i++) { 
            $schedule = new LoanPaymentSchedule;

            $per = $i + 1;

            $principal = round($this->PPMT($rate, $per, $nper, $pv),2);
            $interest = $monthly - $principal;
            $date = Carbon::parse($loan->date_issued)->addMonths($per);

            $schedule->date             = $date;
            $schedule->amount           = $monthly;
            $schedule->principal        = $principal;
            $schedule->interest         = $interest;
            $schedule->paid             = 0;
            $schedule->balance          = $monthly;
            $schedule->month            = $per;
            $schedule->status           = 'Not Paid';
            $schedule->loan_id          = $loan->id;
            $schedule->tenant_id        = $loan->tenant->id;

            $schedule->save();

            $alert_one = Carbon::parse($date)->subDays(5);
            $alert_two = Carbon::parse($date)->subDays(3);
            $alert_three = $date;

            $alert = new ScheduleAlert;

            $alert->alert_one           = $alert_one;
            $alert->alert_two           = $alert_two;
            $alert->alert_three         = $alert_three;
            $alert->schedule_id         = $schedule->id;
            $alert->loan_id             = $loan->id;
            $alert->tenant_id           = $loan->tenant->id;

            $alert->save();
        }

        $summary = new LoanSummary;

        $summary->monthly             = $monthly;
        $summary->interest            = $loan->schedules->sum('interest');
        $summary->principal           = $loan->schedules->sum('principal');
        $summary->penalt              = 0;
        $summary->paid                = 0;
        $summary->overwrite           = 0;
        $summary->status              = 'Active';
        $summary->loan_id             = $loan->id;
        $summary->tenant_id           = $loan->tenant->id;

        $summary->save();

        Alert::success('Client & Loan Registered!');

        if ($request->finish == "Add Another") {
            return Redirect::to('fastTrack');
        }else {
            return Redirect::to('dashboard');
        }
    }

    public function PMT($rate = 0, $nper = 0, $pv = 0, $fv = 0, $due = 0) 
    {
        if ($rate > 0) {
            return (-$fv - $pv * pow(1 + $rate, $nper)) / (1 + $rate * $due) / ((pow(1 + $rate, $nper) - 1) / $rate);
        } else {
            return (-$pv - $fv) / $nper;
        }
    }

    public function PPMT($rate = 0, $per = 0, $nper = 0, $pv = 0, $fv = 0, $due = 0) 
    {
        $float1 = $per - ($due?2:1);
        $float2 = - $fv*$rate/((pow(1+ $rate,$nper)- 1)*($due?1+ $rate:1))+ - $pv/(($due?1:0)+ 1/$rate*(1- 1/pow(1+ $rate,$nper- ($due?1:0))));
        return (- $fv*$rate/((pow(1+ $rate,$nper)- 1)*($due?1+ $rate:1))+ - $pv/(($due?1:0)+ 1/$rate*(1- 1/pow(1+ $rate,$nper- ($due?1:0))))- - ($pv*pow(1+ $rate,$float1)- (- 0*pow(1+ $rate,$float1)+ - (1/$rate)*$float2*(pow(1+ $rate,$float1)- 1)*($due?1+ $rate:1)- ($due?$float2:0)))*$rate)*-1;
    }
}
