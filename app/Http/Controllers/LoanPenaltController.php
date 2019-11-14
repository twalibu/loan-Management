<?php

namespace App\Http\Controllers;

use Crypt;
use Carbon;
use App\Tenant;
use App\TenantSMS;
use App\SMSReport;
use App\LoanPenalt;
use App\LoanSummary;
use App\Http\Requests;
use App\LoanPaymentSchedule;

use infobip\api\client\PreviewSms;
use infobip\api\client\SendSingleTextualSms;
use infobip\api\model\sms\mt\send\preview\Preview;
use infobip\api\configuration\BasicAuthConfiguration;
use infobip\api\model\sms\mt\send\preview\PreviewRequest;
use infobip\api\model\sms\mt\send\textual\SMSTextualRequest;

class LoanPenaltController extends Controller
{
    /**
     * Send Notification
     *
     * @return Response
     */
    public function index()
    {
        $tenants = Tenant::all();
        $today = Carbon::today()->toDateString();        

        foreach ($tenants as $tenant) {
            /* Check Subscription*/
            $subscription_expiration = Carbon::parse($tenant->subscription->end_date)->toDateString();
            if($subscription_expiration >= $today){
                /* Get all Tenant Schedules */
                $schedules = LoanPaymentSchedule::where('tenant_id', $tenant->id)->get();

                foreach ($schedules as $schedule) {
            		$schedule_date = Carbon::parse($schedule->date)->toDateString(); 
                    /* Check if any Pending Payments */
            		if($today >= $schedule_date){
            			if($schedule->status != 'Completed'){
            				$penalt = $schedule->balance * $schedule->loan->overdue->penalt;
            				$new_balance = $schedule->balance + $penalt;

            				$schedule->balance = $new_balance;
            				$schedule->save();

            				$loan_penalt = new LoanPenalt;

            				$loan_penalt->amount = $penalt;
            				$loan_penalt->date = Carbon::now();
            				$loan_penalt->month = $schedule->month;
            				$loan_penalt->schedule_id = $schedule->id;
            				$loan_penalt->loan_id = $schedule->loan->id;
            				$loan_penalt->tenant_id = $schedule->tenant->id;

            				$loan_penalt->save();

            				$summary = LoanSummary::where('loan_id', $schedule->loan->id)->first();

            				$new_penalt = $summary->penalt + $penalt;

            				$summary->penalt = $new_penalt;
            				$summary->save();
                            /* Alert Client */
            				$this->alert($loan_penalt->id);
            			}
            		}
                }
            }
        }
    }

    public function alert($id)
    {
        $penalt = LoanPenalt::findorfail($id);

        /* Messages */
        $engMessage = 
        	'Hello '. 
        	$penalt->loan->client->first_name . ' ' . $penalt->loan->client->last_name . 
        	'. This is to notify you that your loan (Loan ID: '. $penalt->loan->loan_identity . ') has been penalized for late payment.'. 
                PHP_EOL .'Penalt : Tsh ' . number_format($penalt->amount, 2) . '/-' . 
                PHP_EOL . 'New amount today ' . Carbon::createFromFormat('Y-m-d', $penalt->date)->toFormattedDateString() . ' is Tsh ' . number_format($penalt->schedule->balance, 2) . '/-' .
                PHP_EOL . 'For more information please call +' . $penalt->tenant->sales->phone_number . '.' .
                PHP_EOL . 'Thank You - '. $penalt->tenant->name;

    	$swaMessage = 
            'Habari Ndugu '. 
            $penalt->loan->client->first_name . ' ' . $penalt->loan->client->last_name . 
            '. Ujumbe huu nikukutaarifu ya kwamba Mkopo wako (Loan ID: '. $penalt->loan->loan_identity . ') umetozwa faini kwa kuchelewa kufanya malipo.'. 
                PHP_EOL .'Faini : Tsh ' . number_format($penalt->amount, 2) . '/-' . 
                PHP_EOL . 'Kiasi kipya leo ' . Carbon::createFromFormat('Y-m-d', $penalt->date)->toFormattedDateString() . ' ni Tsh ' . number_format($penalt->schedule->balance, 2) . '/-' .
                PHP_EOL . 'Kwa maelezo zaidi piga namba +' . $penalt->tenant->sales->phone_number . '.' .
                PHP_EOL . 'Asante - '. $penalt->tenant->name;

        /* Get Clients Details */
        $clients_numbers = [];
        /* Get Clients Contacts */
        $clients_numbers = array_prepend($clients_numbers, $penalt->loan->client->phone_number);
        if ($penalt->loan->client->phone_number_2 != 0) {
            $clients_numbers = array_prepend($clients_numbers, $penalt->loan->client->phone_number_2);
        }

        $client_count = count($clients_numbers);
                            
        /* Sending English SMS*/
        $eng_sms_count = $this->smsCount($engMessage, $penalt->tenant->id);
        $smsBalance = $this->smsBalanceChecker($client_count, $eng_sms_count, $penalt->tenant->id);
        if ($smsBalance) {
            $this->smsSender($engMessage, $clients_numbers, $penalt->tenant->id);
            $this->updateBalance($client_count, $eng_sms_count, $penalt->tenant->id);
            $this->saveReport($clients_numbers, $engMessage, $eng_sms_count, $penalt->tenant->id);
        }

        /* Sending Swahili SMS*/
        $swa_sms_count = $this->smsCount($swaMessage, $penalt->tenant->id);
        $smsBalance1 = $this->smsBalanceChecker($client_count, $swa_sms_count, $penalt->tenant->id);
        if ($smsBalance1) {
            $this->smsSender($swaMessage, $clients_numbers, $penalt->tenant->id);
            $this->updateBalance($client_count, $swa_sms_count, $penalt->tenant->id);
            $this->saveReport($clients_numbers, $swaMessage, $swa_sms_count, $penalt->tenant->id);
        }
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
    public function saveReport($phone_numbers, $message, $sms_count, $tenant)
    {
        foreach ($phone_numbers as $phone_number) {
            $report = new SMSReport;

            $report->text = $message;
            $report->phone_number = $phone_number;
            $report->sms_count = $sms_count;
            $report->date = Carbon::now();
            $report->tenant_id = $tenant;

            $report->save();
        }

        return true;
    }
}
