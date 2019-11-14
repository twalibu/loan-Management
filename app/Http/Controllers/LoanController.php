<?php

namespace App\Http\Controllers;

use Alert;
use Carbon;
use Redirect;
use Sentinel;

use App\Loan;
use App\Client;
use App\LoanType;
use App\LoanPenalt;
use App\LoanPayment;
use App\LoanOverdue;
use App\LoanSummary;
use App\LoanContract;
use App\ScheduleAlert;
use App\LoanOverwrite;
use App\LoanPaymentSchedule;

use Illuminate\Http\Request;
use App\Http\Requests\LoanFormRequest as LoanFormRequest;
use App\Http\Requests\LoanEditFormRequest as LoanEditFormRequest;

class LoanController extends Controller
{
    public function __construct()
    {
        // Middleware
        $this->middleware('sentinel.auth');
        $this->middleware('sentinel.access:loans.create', ['only' => ['create', 'store']]);
        $this->middleware('sentinel.access:loans.view', ['only' => ['index', 'active', 'paid', 'show']]);
        $this->middleware('sentinel.access:loans.update', ['only' => ['edit', 'update']]);
        $this->middleware('sentinel.access:loans.destroy', ['only' => ['destroy']]);
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

        $loans = Loan::where('tenant_id', $tenant)->orderBy('id', 'desc')->get();

        return view('loans.index', compact('loans'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function active()
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $loans = Loan::where('tenant_id', $tenant)->orderBy('id', 'desc')->get();

        return view('loans.active', compact('loans'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function paid()
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $loans = Loan::where('tenant_id', $tenant)->orderBy('id', 'desc')->get();

        return view('loans.paid', compact('loans'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant;

        return view('loans/create', compact('tenant'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LoanFormRequest $request)
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $loan = new Loan;

        $loan->loan_identity        = $request->loan_identity;
        $loan->amount               = $request->amount;
        $loan->date_issued          = $request->date;
        $loan->client_id            = $request->client;
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

        Alert::success('New Loan Registered!');

        return Redirect::to('loans');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $loan = Loan::findorfail($id);

        return view('loans.show', compact('loan'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $loan = Loan::findorfail($id);

        if ($loan->payments->count() > 0) {
            Alert::error('You Can Not Edit This Loan Anymore!');

            return redirect()->back();
        }
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant;

        return view('loans/edit', compact('loan', 'tenant'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LoanEditFormRequest $request, $id)
    {
        $loan = Loan::findorfail($id);
        $summary = LoanSummary::where('loan_id', $loan->id)->delete();
        $alert = ScheduleAlert::where('loan_id', $loan->id)->delete();
        $schedule = LoanPaymentSchedule::where('loan_id', $loan->id)->delete();
        $loan->delete();

        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $loan = new Loan;

        $loan->loan_identity        = $request->loan_identity;
        $loan->amount               = $request->amount;
        $loan->date_issued          = $request->date;
        $loan->client_id            = $request->client;
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

        Alert::success('Loan Edited Succefully!');

        return redirect()->action('LoanController@show', ['id' => $loan->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $loan = Loan::findorfail($id);

        $penalt = LoanPenalt::where('loan_id', $loan->id)->delete();
        $summary = LoanSummary::where('loan_id', $loan->id)->delete();
        $alert = ScheduleAlert::where('loan_id', $loan->id)->delete();
        $payment = LoanPayment::where('loan_id', $loan->id)->delete();        
        $contract = LoanContract::where('loan_id', $loan->id)->delete();
        $overwrite = LoanOverwrite::where('loan_id', $loan->id)->delete();
        $schedule = LoanPaymentSchedule::where('loan_id', $loan->id)->delete();

        $loan->delete();

        Alert::success('Loan Removed Successfully!');

        return Redirect::to('loans');
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

