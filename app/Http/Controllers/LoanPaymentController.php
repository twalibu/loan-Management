<?php

namespace App\Http\Controllers;

use Alert;
use Carbon;
use Redirect;
use Sentinel;

use App\Loan;
use App\Client;
use App\LoanType;
use App\LoanOverdue;
use App\LoanSummary;
use App\LoanPayment;
use App\LoanOverwrite;
use App\PaymentMethod;
use App\ScheduleAlert;
use App\LoanPaymentSchedule;

use Illuminate\Http\Request;
use App\Http\Requests\LoanPaymentFormRequest as LoanPaymentFormRequest;
use App\Http\Requests\LoanPaymentEditFormRequest as LoanPaymentEditFormRequest;
use App\Http\Requests\PaymentOverwriteFormRequest as PaymentOverwriteFormRequest;

class LoanPaymentController extends Controller
{
    public function __construct()
    {
        // Middleware
        $this->middleware('sentinel.auth');
        $this->middleware('sentinel.access:payments.create', ['only' => ['create', 'fromloan', 'store']]);
        $this->middleware('sentinel.access:payments.overwrite', ['only' => ['overwrite', 'overwritepost']]);
        $this->middleware('sentinel.access:payments.view', ['only' => ['index', 'show']]);
        $this->middleware('sentinel.access:payments.update', ['only' => ['edit', 'update']]);
        $this->middleware('sentinel.access:payments.destroy', ['only' => ['destroy']]);
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

        $payments = LoanPayment::where('tenant_id', $tenant)->get();

        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $summaries = LoanSummary::where([['tenant_id', $tenant],['status', 'Active']])->get();
        $methods = PaymentMethod::where('tenant_id', $tenant)->get();

        return view('payments/create', compact('summaries', 'methods'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fromloan($id)
    {
        $loan = Loan::findorfail($id);

        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;
        $methods = PaymentMethod::where('tenant_id', $tenant)->get();

        return view('payments/fromloan', compact('loan', 'methods'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function overwrite($id)
    {
        $loan = Loan::findorfail($id);

        return view('payments/overwrite', compact('loan'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function overwritepost(PaymentOverwriteFormRequest $request)
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $schedule = LoanPaymentSchedule::findorfail($request->schedule);

        if ($request->amount > $schedule->balance) {
            $overamount = $schedule->balance;
        }else
        {
            $overamount = $request->amount;
        }

        $overwrite = new LoanOverwrite;

        $overwrite->amount               = $overamount;
        $overwrite->date                 = $request->date;
        $overwrite->schedule_id          = $request->schedule;
        $overwrite->loan_id              = $request->loan;
        $overwrite->tenant_id            = $tenant;

        $overwrite->save();

        $schedule = LoanPaymentSchedule::findorfail($request->schedule);

        $balance = $schedule->balance - $overamount;
        $schedule->balance  = $balance;
        $schedule->save();

        if ($schedule->balance > 0) {
            $schedule->status   = 'Not Completed';
        }else
        {
            $schedule->status   = 'Completed';
        }

        $schedule->save();

        $summary = LoanSummary::where('loan_id', $request->loan)->first();

        $summary->overwrite  = $summary->overwrite + $overamount;
        $summary->save();

        if (($summary->interest + $summary->principal + $summary->penalt) <= ($summary->paid + $summary->overwrite)) {
            $summary->status = 'Completely Paid';
        }
        $summary->save();


        Alert::success('Payment Overwrite Recorded!');

        return redirect()->action('LoanController@show', ['id' => $request->loan]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LoanPaymentFormRequest $request)
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $payment = new LoanPayment;

        $payment->amount               = $request->amount;
        $payment->date          	   = $request->date;
        $payment->method_id            = $request->method;
        $payment->loan_id              = $request->loan;
        $payment->tenant_id            = $tenant;

        $payment->save();

        $term = $payment->loan->type->duration;
        $amount = $payment->amount;


        for ($i=1; $i <= $term ; $i++) { 
        	$schedule = LoanPaymentSchedule::where([['month', $i], ['loan_id', $payment->loan_id]])->first();
        	$x = 1;
        	while ($x > 0 && $amount > 0) {
	        	if ($schedule->balance > 0 && $amount > $schedule->balance && $schedule->status != 'Completed') {
	        		$paid = $schedule->balance;
	        		$schedule->paid 	= $schedule->amount;
	        		$schedule->balance 	= 0;
	        		$schedule->status 	= 'Completed';

	        		$schedule->save();

	        		$summary = LoanSummary::where('loan_id', $payment->loan_id)->first();

	        		$summary->paid 	= $summary->paid + $paid;
	        		$summary->save();

	        		if (($summary->interest + $summary->principal + $summary->penalt + $summary->overwrite) <= $summary->paid) {
	        			$summary->status = 'Completely Paid';
	        		}
	        		$summary->save();

	        		$amount = $amount - $paid;
	        		$x = -1;
	        	}
	        	elseif ($schedule->balance > 0 && $amount > 0 && $schedule->status != 'Completed') {
	        		$paid = $amount + $schedule->paid;

	        		$schedule->paid 	= $paid;
	        		$schedule->save();

	        		$balance 	= $schedule->balance - $schedule->paid;
	        		$schedule->balance 	= $balance;
	        		$schedule->save();

	        		if ($schedule->balance > 0) {
	        			$schedule->status 	= 'Not Completed';
	        		}else
	        		{
	        			$schedule->status 	= 'Completed';
	        		}

	        		$schedule->save();

	        		$summary = LoanSummary::where('loan_id', $payment->loan_id)->first();

	        		$summary->paid 	= $summary->paid + $amount;
	        		$summary->save();

	        		if (($summary->interest + $summary->principal + $summary->penalt + $summary->overwrite) <= $summary->paid) {
	        			$summary->status = 'Completely Paid';
	        		}
	        		$summary->save();

	        		$amount = 0;
	        		$x = -1;
	        	}
	        	else
	        	{
	        		$x = -1;
	        	}
	        }
        }

        Alert::success('New Payment Recorded!');

        return Redirect::to('payments');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $payment = LoanPayment::findorfail($id);

        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;
        $methods = PaymentMethod::where('tenant_id', $tenant)->get();

        return view('payments/edit', compact('payment', 'methods'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LoanPaymentEditFormRequest $request, $id)
    {
        $payment = LoanPayment::findorfail($id);

        $summary = LoanSummary::where('loan_id', $payment->loan->id)->first();

        if ($summary->status == 'Completely Paid') {
            $summary->status = 'Active';
            $summary->save();
        }

        $amount = $payment->amount;

        $old_amount = $summary->paid - $amount;
        $summary->paid = $old_amount;
        $summary->save();

        $term = $payment->loan->type->duration;

        for ($i=$term; $i > 0 ; $i--) { 
            if($amount > 0){
                $schedule = LoanPaymentSchedule::where([['month', $i], ['loan_id', $payment->loan_id]])->first();

                if ($amount >= $schedule->paid && $schedule->paid > 0) {
                    $extra = $schedule->paid - $schedule->amount;
                    if ($extra > 0) {
                       $old_paid = $schedule->paid;
                        $balance = $schedule->balance + $extra;

                        $schedule->paid     = 0;
                        $schedule->balance = $balance;
                        $schedule->status   = 'Not Paid';

                        $schedule->save();

                        $amount = $amount - $old_paid; 
                    }
                    else
                    {
                        $old_paid = $schedule->paid;
                        $balance = $schedule->amount;

                        $schedule->paid     = 0;
                        $schedule->balance = $balance;
                        $schedule->status   = 'Not Paid';

                        $schedule->save();

                        $amount = $amount - $old_paid; 
                    }
                    
                }
                elseif ($schedule->paid > 0) {
                    $old_amount = $schedule->paid - $amount;

                    $schedule->paid     = $old_amount;
                    $schedule->status   = 'Not Completed';
                    $schedule->save();

                    $balance = $schedule->amount - $schedule->paid;

                    $schedule->balance = $balance;
                    $schedule->save();

                    $amount = 0;
                }
            }
        }

        $payment->amount               = $request->amount;
        $payment->date                 = $request->date;
        $payment->method_id            = $request->method;

        $payment->save();

        $amount = $payment->amount;

        for ($i=1; $i <= $term ; $i++) { 
            $schedule = LoanPaymentSchedule::where([['month', $i], ['loan_id', $payment->loan_id]])->first();
            $x = 1;
            while ($x > 0 && $amount > 0) {
                if ($schedule->balance > 0 && $amount > $schedule->balance && $schedule->status != 'Completed') {
                    $paid = $schedule->balance;
                    $schedule->paid     = $schedule->amount;
                    $schedule->balance  = 0;
                    $schedule->status   = 'Completed';

                    $schedule->save();

                    $summary = LoanSummary::where('loan_id', $payment->loan_id)->first();

                    $summary->paid  = $summary->paid + $paid;
                    $summary->save();

                    if (($summary->interest + $summary->principal + $summary->penalt + $summary->overwrite) <= $summary->paid) {
                        $summary->status = 'Completely Paid';
                    }
                    $summary->save();

                    $amount = $amount - $paid;
                    $x = -1;
                }
                elseif ($schedule->balance > 0 && $amount > 0 && $schedule->status != 'Completed') {
                    $paid = $amount + $schedule->paid;

                    $schedule->paid     = $paid;
                    $schedule->save();

                    $balance    = $schedule->balance - $schedule->paid;
                    $schedule->balance  = $balance;
                    $schedule->save();

                    if ($schedule->balance > 0) {
                        $schedule->status   = 'Not Completed';
                    }else
                    {
                        $schedule->status   = 'Completed';
                    }

                    $schedule->save();

                    $summary = LoanSummary::where('loan_id', $payment->loan_id)->first();

                    $summary->paid  = $summary->paid + $amount;
                    $summary->save();

                    if (($summary->interest + $summary->principal + $summary->penalt + $summary->overwrite) <= $summary->paid) {
                        $summary->status = 'Completely Paid';
                    }
                    $summary->save();

                    $amount = 0;
                    $x = -1;
                }
                else
                {
                    $x = -1;
                }
            }
        }

        Alert::success('Payment Edited Sucessfully!');

        return Redirect::to('payments');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = LoanPayment::findorfail($id);

        $summary = LoanSummary::where('loan_id', $payment->loan->id)->first();

        if ($summary->status == 'Completely Paid') {
            $summary->status = 'Active';
            $summary->save();
        }

        $amount = $payment->amount;

        $old_amount = $summary->paid - $amount;
        $summary->paid = $old_amount;
        $summary->save();

        $term = $payment->loan->type->duration;

        for ($i=$term; $i > 0 ; $i--) { 
            if($amount > 0){
                $schedule = LoanPaymentSchedule::where([['month', $i], ['loan_id', $payment->loan_id]])->first();

                if ($amount >= $schedule->amount && $schedule->paid > 0) {
                    $balance = $schedule->amount;
                    $paid = $schedule->paid;

                    $schedule->paid     = 0;
                    $schedule->balance = $balance;
                    $schedule->status   = 'Not Paid';

                    $schedule->save();

                    $amount = $amount - $paid;
                }
                elseif ($schedule->paid > 0) {
                    $old_amount = $schedule->paid - $amount;

                    $schedule->paid     = $old_amount;
                    $schedule->status   = 'Not Completed';
                    $schedule->save();

                    $balance = $schedule->amount - $schedule->paid;

                    $schedule->balance = $balance;
                    $schedule->save();

                    $amount = 0;
                }
            }
        }

        $payment->delete();

        Alert::success('Payment Deleted Sucessfully!');

        return Redirect::to('payments');
    }
}