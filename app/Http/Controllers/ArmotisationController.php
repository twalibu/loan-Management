<?php

namespace App\Http\Controllers;

use Alert;
use Input;
use Crypt;
use Carbon;
use Sentinel;
use Redirect;

use App\Tenant;
use App\LoanType;
use App\TenantSMS;
use App\SMSReport;
use App\TenantContact;

use Illuminate\Http\Request;
use infobip\api\client\PreviewSms;
use infobip\api\client\SendSingleTextualSms;
use infobip\api\model\sms\mt\send\preview\Preview;
use infobip\api\configuration\BasicAuthConfiguration;
use infobip\api\model\sms\mt\send\preview\PreviewRequest;
use infobip\api\model\sms\mt\send\textual\SMSTextualRequest;
use App\Http\Requests\ProjectionFormRequest as ProjectionFormRequest;
use App\Http\Requests\ArmotisationFormRequest as ArmotisationFormRequest;

class ArmotisationController extends Controller
{
    public function __construct()
    {
        // Middleware
        $this->middleware('sentinel.auth');
    }

    public function calculate(ArmotisationFormRequest $request)
    {
        $type = LoanType::findorfail($request->type);

        $total_principal = 0;
        $total_interest = 0;

        $rate = $type->interest/$type->duration; // The interest rate per month
        $nper = $type->duration; // Duration of Loan, in Months
        $pv = $request->amount; // Loan Amount

        $monthly = round($this->PMT($rate, $nper, -$pv),2); // Monthly Payment

        $schedules = [];
        for ($i=0; $i < $type->duration; $i++) { 

            $per = $i + 1;

            $principal = round($this->PPMT($rate, $per, $nper, $pv),2);
            $interest = $monthly - $principal;

            $schedules[]= array ('principal'=>$principal,
                                'interest'=>$interest,
                                'payment'=>$principal + $interest,
                                'month'=>$per);

            $total_principal = $total_principal + $principal;
            $total_interest = $total_interest + $interest;
        }
        $total_repayment = $total_principal + $total_interest;
        $insurance = $pv * 0.015;

        return view('tools.tenant.armotisation', compact('total_principal', 'total_interest', 'total_repayment', 'monthly', 'type', 'schedules', 'insurance', 'pv'));
    }

    public function projection(ProjectionFormRequest $request)
    {
        // Retrieving Tenant Details
        $client_count = 1;
        $tenant = Sentinel::getUser()->tenant;
        $client_number = Input::get('phone_number');

    	/* Loan Calcutation */
        $type = LoanType::findorfail($request->type);

        $total_principal = 0;
        $total_interest = 0;

        $rate = $type->interest/$type->duration; // The interest rate per month
        $nper = $type->duration; // Duration of Loan, in Months
        $pv = $request->amount; // Loan Amount

        $monthly = round($this->PMT($rate, $nper, -$pv),2); // Monthly Payment

        for ($i=0; $i < $type->duration; $i++) { 

            $per = $i + 1;

            $principal = round($this->PPMT($rate, $per, $nper, $pv),2);
            $interest = $monthly - $principal;

            $total_principal = $total_principal + $principal;
            $total_interest = $total_interest + $interest;
        }
        $total_repayment = $total_principal + $total_interest;
        $insurance = $pv * 0.015;

        /* Messages */
        $engMessage = 
        	'Hello '. 
        	$request->full_name . 
        	'. Thank you for the interest to get a loan with us. Below is the summary of the loan you inquired about:-' . PHP_EOL .
        	'Loan Type : ' . $type->name . PHP_EOL .
            'Loan Duration : ' . $type->duration .' '. str_plural('Month', $type->duration) . PHP_EOL . 
        	'Loan Amount : Tsh ' . number_format($pv, 2) . '/=' . PHP_EOL . 
            'Total Interest : Tsh ' . number_format($total_interest, 2) . '/=' . PHP_EOL . 
        	'Total Payback Amount : Tsh ' . number_format($total_repayment, 2) . '/=' . PHP_EOL .
        	'Monthly Payment : Tsh ' . number_format($monthly, 2) . '/=' . PHP_EOL . 
        	'Loan Insurance : Tsh ' . number_format($insurance, 2) . '/=' . PHP_EOL . 
            'For more information please call +' . $tenant->sales->phone_number . '.' . PHP_EOL . 
        	'Thank You - '. $tenant->name;

    	$swaMessage = 
        	'Habari Ndugu '. 
        	$request->full_name . 
        	'. Asante kwakutaka mkopo nasi. Tafadhali pata maelezo zaidi kuhusu mkopo unaotaka:-' . PHP_EOL .
        	'Aina Ya Mkopo : ' . $type->name . PHP_EOL . 
            'Muda Wa Mkopo : Mwezi/Miezi ' . $type->duration . PHP_EOL . 
        	'Kiasi cha Mkopo : Tsh ' . number_format($pv, 2) . '/=' . PHP_EOL . 
            'Jumla ya Riba : Tsh ' . number_format($total_interest, 2) . '/=' . PHP_EOL . 
        	'Jumla ya Marejesho : Tsh ' . number_format($total_repayment, 2) . '/=' . PHP_EOL .
        	'Marejesho kwa Mwezi : Tsh ' . number_format($monthly, 2) . '/=' . PHP_EOL . 
        	'Bima ya Mkopo : Tsh ' . number_format($insurance, 2) . '/=' . PHP_EOL . 
            'Kwa maelezo zaidi piga namba +' . $tenant->sales->phone_number . '.' . PHP_EOL . 
        	'Asante - '. $tenant->name;        

        /* Sending English SMS*/
        $eng_sms_count = $this->smsCount($engMessage, $tenant->id);
        $smsBalance = $this->smsBalanceChecker($client_count, $eng_sms_count, $tenant->id);
        if ($smsBalance) {
            $this->smsSender($engMessage, $client_number, $tenant->id);
            $this->updateBalance($client_count, $eng_sms_count, $tenant->id);
            $this->saveReport($client_number, $engMessage, $eng_sms_count, $tenant->id);
        }

        /* Sending Swahili SMS*/
        $swa_sms_count = $this->smsCount($swaMessage, $tenant->id);
        $smsBalance1 = $this->smsBalanceChecker($client_count, $swa_sms_count, $tenant->id);
        if ($smsBalance1) {
            $this->smsSender($swaMessage, $client_number, $tenant->id);
            $this->updateBalance($client_count, $swa_sms_count, $tenant->id);
            $this->saveReport($client_number, $swaMessage, $swa_sms_count, $tenant->id);
        }

        /* Send Email */
        $email = $tenant->sales->email;
        $monthly = number_format($monthly, 2);
        $insurance = number_format($insurance, 2);
        $tech_support = config('app.tech_support');
        $amount = number_format($request->amount, 2);
        $total_interest = number_format($total_interest, 2);
        $total_repayment = number_format($total_repayment, 2);
        $total_principal = number_format($total_principal, 2);
                        
        $data = [
            'amount'=> $amount,
            'monthly'=> $monthly,
            'insurance'=> $insurance,
            'tenant' => $tenant->name,
            'loan_type' => $type->name,
            'total_interest'=> $total_interest,
            'full_name' => $request->full_name,
            'total_repayment'=> $total_repayment,
            'total_principal'=> $total_principal,
            'phone_number' => $request->phone_number,            
            'tech_support' => config('app.tech_support'),
        ];

        $beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
        $beautymail->send('mails.tenants.projection', $data, function($message) use ($email)
        {
            $message
                ->from('no-reply@loan-alert.pro', 'Loan Alert')
                ->to($email)
                ->subject('Loan Alert | Armotisation Projection');
        });

        Alert::success('Projection Sent Successfully!');

        return Redirect::to('dashboard');
    }

    /**
     * Get SMS Count
     */
    public function smsCount($message, $tenant)
    {
        $sms = TenantSMS::where('tenant_id', $tenant)->first();

        $username =  $sms->username;
        $password =  Crypt::decrypt($sms->password);

        // Initializing PreviewSms client with appropriate configuration
        $client = new PreviewSms(new BasicAuthConfiguration($username, $password));
        $previewRequest = new PreviewRequest();
        $previewRequest->setText($message);
        $previewResponse = $client->execute($previewRequest);
        $noConfigurationPreview = $previewResponse->getPreviews()[0];
        $smsCount = $noConfigurationPreview->getMessageCount();

        return $smsCount;
    }

    /**
     * SMS Balance Checker
     */
    public function smsBalanceChecker($client_count, $sms_count, $tenant)
    {
        $sms = TenantSMS::where('tenant_id', $tenant)->first();

        $balance = false;

        if (($client_count * $sms_count  * $sms->price) <= $sms->balance ) {
            $balance = true;
        }

        return $balance;
    }

    /**
     * SMS Sender
     */
    public function smsSender($message, $phone_numbers, $tenant)
    {
        $sms = TenantSMS::where('tenant_id', $tenant)->first();

        // Initializing SendSingleTextualSms client with appropriate configuration
        $client = new SendSingleTextualSms(new BasicAuthConfiguration($sms->username, Crypt::decrypt($sms->password)));

        // Creating request body
        $requestBody = new SMSTextualRequest();
        $requestBody->setFrom($sms->sender);
        $requestBody->setTo($phone_numbers);
        $requestBody->setText($message);

        // Executing request
        $response = $client->execute($requestBody);

        return true;
    }

    /**
     * Update Tenant SMS Balance
     */
    public function updateBalance($client_count, $sms_count, $tenant)
    {
        $sms = TenantSMS::where('tenant_id', $tenant)->first();

        $sms->balance = $sms->balance - ($client_count * $sms_count * $sms->price);

        $sms->save();

        return true;
    }

    /**
     * Save SMS Reports
     */
    public function saveReport($phone_number, $message, $sms_count, $tenant)
    {
        $report = new SMSReport;

        $report->text = $message;
        $report->phone_number = $phone_number;
        $report->sms_count = $sms_count;
        $report->date = Carbon::now();
        $report->tenant_id = $tenant;

        $report->save();

        return true;
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
