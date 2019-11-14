<?php

namespace App\Http\Controllers;

use Crypt;
use Carbon;
use App\Tenant;
use App\TenantSMS;
use App\SMSReport;
use App\ScheduleAlert;
use App\Http\Requests;

use infobip\api\client\PreviewSms;
use infobip\api\client\SendSingleTextualSms;
use infobip\api\model\sms\mt\send\preview\Preview;
use infobip\api\configuration\BasicAuthConfiguration;
use infobip\api\model\sms\mt\send\preview\PreviewRequest;
use infobip\api\model\sms\mt\send\textual\SMSTextualRequest;

class ScheduleController extends Controller
{
    /**
     * Send Notification
     *
     * @return Response
     */
    public function index()
    {
    	$tenants = Tenant::all();
    	$now = Carbon::now()->format('H:i');
    	$today = Carbon::today()->toDateString();        

        foreach ($tenants as $tenant) {
        	/* Check Subscription*/
        	$subscription_expiration = Carbon::parse($tenant->subscription->end_date)->toDateString();
	    	if($subscription_expiration >= $today){
	    		/* Check Tenant Alert Time */
	            $time = Carbon::parse($tenant->schedule->alert)->format('H:i');
            	if($time == $now){
            		/* Get all Tenant Alert */
		        	$alerts = ScheduleAlert::where('tenant_id', $tenant->id)->get();
			    	foreach ($alerts as $alert) {
		    			/* Get Alert Dates */
		    			$alert_one = Carbon::parse($alert->alert_one)->toDateString();
		    			$alert_two = Carbon::parse($alert->alert_two)->toDateString();
		    			$alert_three = Carbon::parse($alert->alert_three)->toDateString();

		    			if ($alert_one == $today) {
		    				/* Get Clients Details */
			    			$clients_numbers = [];
	    					/* Get Clients Contacts */
	    					$clients_numbers = array_prepend($clients_numbers, $alert->loan->client->phone_number);
				            if ($alert->loan->client->phone_number_2 != 0) {
				                $clients_numbers = array_prepend($clients_numbers, $alert->loan->client->phone_number_2);                
				            }

				            /* Generate Message for Client */
				            $engMessage = 
				        	'Hello '. 
				        	$alert->loan->client->first_name . ' ' . $alert->loan->client->last_name . 
				        	'. This is a reminder that your loan (Loan ID: '. $alert->loan->loan_identity . ') payment of Tsh '. number_format($alert->schedule->amount, 2) . '/- is due on '. Carbon::parse($alert->schedule->date)->toFormattedDateString() . ' at our ' . $alert->loan->office->name . ' office. For more information please call +' . $alert->tenant->sales->phone_number . '.' . PHP_EOL . 'Thank You - '. $alert->tenant->name;

				        	$swaMessage = 
				        	'Habari Ndugu '. 
				        	$alert->loan->client->first_name . ' ' . $alert->loan->client->last_name . 
				        	'. Ujumbe huu nikukukumbusha malipo ya Mkopo wako (Loan ID: '. $alert->loan->loan_identity . ') kiasi cha Tsh '. number_format($alert->schedule->amount, 2) . '/- yanatakiwa tarehe '. Carbon::parse($alert->schedule->date)->toFormattedDateString() . ' kwenye ofisi yetu ya ' . $alert->loan->office->name . '. Kwa maelezo zaidi piga namba +' . $alert->tenant->sales->phone_number . '.' . PHP_EOL . 'Asante - '. $alert->tenant->name;

				        	$client_count = count($clients_numbers);
				            
				        	/* Sending English SMS*/
				        	$eng_sms_count = $this->smsCount($engMessage, $tenant->id);
				        	$smsBalance = $this->smsBalanceChecker($client_count, $eng_sms_count, $tenant->id);
				        	if ($smsBalance) {
				                $this->smsSender($engMessage, $clients_numbers, $tenant->id);
				                $this->updateBalance($client_count, $eng_sms_count, $tenant->id);
				                $this->saveReport($clients_numbers, $engMessage, $eng_sms_count, $tenant->id);
				            }

					        /* Sending Swahili SMS*/
				        	$swa_sms_count = $this->smsCount($swaMessage, $tenant->id);
				        	$smsBalance1 = $this->smsBalanceChecker($client_count, $swa_sms_count, $tenant->id);
				        	if ($smsBalance1) {
				                $this->smsSender($swaMessage, $clients_numbers, $tenant->id);
				                $this->updateBalance($client_count, $swa_sms_count, $tenant->id);
				                $this->saveReport($clients_numbers, $swaMessage, $swa_sms_count, $tenant->id);
				            }						        
		    			} elseif ($alert_two == $today) {
		    				/* Get Clients Details */
			    			$clients_numbers = [];
	    					/* Get Clients Contacts */
	    					$clients_numbers = array_prepend($clients_numbers, $alert->loan->client->phone_number);
				            if ($alert->loan->client->phone_number_2 != 0) {
				                $clients_numbers = array_prepend($clients_numbers, $alert->loan->client->phone_number_2);                
				            }

				            /* Generate Message for Client */
				            $engMessage = 
				        	'Hello '. 
				        	$alert->loan->client->first_name . ' ' . $alert->loan->client->last_name . 
				        	'. This is a reminder that your loan (Loan ID: '. $alert->loan->loan_identity . ') payment of Tsh '. number_format($alert->schedule->amount, 2) . '/- is due on '. Carbon::parse($alert->schedule->date)->toFormattedDateString() . ' at our ' . $alert->loan->office->name . ' office. For more information please call +' . $alert->tenant->sales->phone_number . '.' . PHP_EOL . 'Thank You - '. $alert->tenant->name;

				        	$swaMessage = 
				        	'Habari Ndugu '. 
				        	$alert->loan->client->first_name . ' ' . $alert->loan->client->last_name . 
				        	'. Ujumbe huu nikukukumbusha malipo ya Mkopo wako (Loan ID: '. $alert->loan->loan_identity . ') kiasi cha Tsh '. number_format($alert->schedule->amount, 2) . '/- yanatakiwa tarehe '. Carbon::parse($alert->schedule->date)->toFormattedDateString() . ' kwenye ofisi yetu ya ' . $alert->loan->office->name . '. Kwa maelezo zaidi piga namba +' . $alert->tenant->sales->phone_number . '.' . PHP_EOL . 'Asante - '. $alert->tenant->name;

				        	$client_count = count($clients_numbers);
				            
				        	/* Sending English SMS*/
				        	$eng_sms_count = $this->smsCount($engMessage, $tenant->id);
				        	$smsBalance = $this->smsBalanceChecker($client_count, $eng_sms_count, $tenant->id);
				        	if ($smsBalance) {
				                $this->smsSender($engMessage, $clients_numbers, $tenant->id);
				                $this->updateBalance($client_count, $eng_sms_count, $tenant->id);
				                $this->saveReport($clients_numbers, $engMessage, $eng_sms_count, $tenant->id);
				            }

					        /* Sending Swahili SMS*/
				        	$swa_sms_count = $this->smsCount($swaMessage, $tenant->id);
				        	$smsBalance1 = $this->smsBalanceChecker($client_count, $swa_sms_count, $tenant->id);
				        	if ($smsBalance1) {
				                $this->smsSender($swaMessage, $clients_numbers, $tenant->id);
				                $this->updateBalance($client_count, $swa_sms_count, $tenant->id);
				                $this->saveReport($clients_numbers, $swaMessage, $swa_sms_count, $tenant->id);
				            }						        
		    			} elseif ($alert_three == $today) {
		    				/* Get Clients Details */
			    			$clients_numbers = [];
	    					/* Get Clients Contacts */
	    					$clients_numbers = array_prepend($clients_numbers, $alert->loan->client->phone_number);
				            if ($alert->loan->client->phone_number_2 != 0) {
				                $clients_numbers = array_prepend($clients_numbers, $alert->loan->client->phone_number_2);                
				            }

				            /* Generate Message for Client */
				            $engMessage = 
				        	'Hello '. 
				        	$alert->loan->client->first_name . ' ' . $alert->loan->client->last_name . 
				        	'. This is a reminder that your loan (Loan ID: '. $alert->loan->loan_identity . ') payment of Tsh '. number_format($alert->schedule->amount, 2) . '/- is due today on '. Carbon::parse($alert->schedule->date)->toFormattedDateString() . ' at our ' . $alert->loan->office->name . ' office. For more information please call +' . $alert->tenant->sales->phone_number . '.' . PHP_EOL . 'Thank You - '. $alert->tenant->name;

				        	$swaMessage = 
				        	'Habari Ndugu '. 
				        	$alert->loan->client->first_name . ' ' . $alert->loan->client->last_name . 
				        	'. Ujumbe huu nikukukumbusha malipo ya Mkopo wako (Loan ID: '. $alert->loan->loan_identity . ') kiasi cha Tsh '. number_format($alert->schedule->amount, 2) . '/- yanatakiwa leo tarehe '. Carbon::parse($alert->schedule->date)->toFormattedDateString() . ' kwenye ofisi yetu ya ' . $alert->loan->office->name . '. Kwa maelezo zaidi piga namba +' . $alert->tenant->sales->phone_number . '.' . PHP_EOL . 'Asante - '. $alert->tenant->name;

				        	$client_count = count($clients_numbers);
				            
				        	/* Sending English SMS*/
				        	$eng_sms_count = $this->smsCount($engMessage, $tenant->id);
				        	$smsBalance = $this->smsBalanceChecker($client_count, $eng_sms_count, $tenant->id);
				        	if ($smsBalance) {
				                $this->smsSender($engMessage, $clients_numbers, $tenant->id);
				                $this->updateBalance($client_count, $eng_sms_count, $tenant->id);
				                $this->saveReport($clients_numbers, $engMessage, $eng_sms_count, $tenant->id);
				            }

					        /* Sending Swahili SMS*/
				        	$swa_sms_count = $this->smsCount($swaMessage, $tenant->id);
				        	$smsBalance1 = $this->smsBalanceChecker($client_count, $swa_sms_count, $tenant->id);
				        	if ($smsBalance1) {
				                $this->smsSender($swaMessage, $clients_numbers, $tenant->id);
				                $this->updateBalance($client_count, $swa_sms_count, $tenant->id);
				                $this->saveReport($clients_numbers, $swaMessage, $swa_sms_count, $tenant->id);
				            }						        
		    			}
			    	}
	    		}
	    	}
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
