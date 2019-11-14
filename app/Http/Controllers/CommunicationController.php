<?php

namespace App\Http\Controllers;

use Alert;
use Crypt;
use Input;
use Carbon;
use Sentinel;

use App\Group;
use App\Staff;
use App\Client;
use App\Tenant;
use App\SMSReport;
use App\TenantSMS;
use App\TenantContact;
use Illuminate\Http\Request;

use infobip\api\client\PreviewSms;
use infobip\api\client\GetAccountBalance;
use infobip\api\client\SendSingleTextualSms;
use infobip\api\model\sms\mt\send\preview\Preview;
use infobip\api\configuration\BasicAuthConfiguration;
use infobip\api\model\sms\mt\send\preview\PreviewRequest;
use infobip\api\model\sms\mt\send\textual\SMSTextualRequest;
use App\Http\Requests\BulkCommunicationFormRequest as BulkCommunicationFormRequest;
use App\Http\Requests\GroupCommunicationFormRequest as GroupCommunicationFormRequest;
use App\Http\Requests\StaffCommunicationFormRequest as StaffCommunicationFormRequest;
use App\Http\Requests\AdminCommunicationFormRequest as AdminCommunicationFormRequest;
use App\Http\Requests\ClientCommunicationFormRequest as ClientCommunicationFormRequest;


class CommunicationController extends Controller
{
    public function __construct()
    {
        // Admin Middleware
        $this->middleware('sentry.auth', ['only' => ['admin', 'postAdmin']]);

        // Admin Middleware
        $this->middleware('sentinel.access:communications.access', ['only' => ['tenant', 'sendClients', 'sendStaff', 'sendBulk', 'sendGroup']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin()
    {
        $username =  env('INFOBIP_USERNAME');
        $password =  env('INFOBIP_PASSWORD');
        $sender =  env('INFOBIP_SENDER');

        // Initializing GetAccountBalance client with appropriate configuration
        $client = new GetAccountBalance(new BasicAuthConfiguration($username, $password));
        // Executing request
        $response = $client->execute();

        $amount = $response->getBalance();
        $currency = $response->getCurrency();
        $tenants = Tenant::all();
        
        return view('communications.admin.index',compact('amount', 'currency', 'tenants', 'sender'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postAdmin(AdminCommunicationFormRequest $request)
    {
        $username =  env('INFOBIP_USERNAME');
        $password =  env('INFOBIP_PASSWORD');
        $sender =  env('INFOBIP_SENDER');

        $message = $request->message . PHP_EOL . 'By ' . $sender;
        
        $receiver = Input::get('receiver');

        if(in_array("all", $receiver)){
            $clients = TenantContact::all()->pluck('phone_number')->toArray();

            // Initializing SendSingleTextualSms client with appropriate configuration
            $client = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));

            // Creating request body
            $requestBody = new SMSTextualRequest();
            $requestBody->setFrom($sender);
            $requestBody->setTo($clients);
            $requestBody->setText($message);

            // Executing request
            $response = $client->execute($requestBody);

            Alert::success('SMS Sent Successfully!');

            return back();
        }

        // Initializing SendSingleTextualSms client with appropriate configuration
        $client = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));

        // Creating request body
        $requestBody = new SMSTextualRequest();
        $requestBody->setFrom($sender);
        $requestBody->setTo($receiver);
        $requestBody->setText($message);

        // Executing request
        $response = $client->execute($requestBody);

        Alert::success('SMS Sent Successfully!');

        return back();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tenant()
    {
        /// Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $staff = Staff::where('tenant_id', $tenant)->get();
        $clients = Client::where('tenant_id', $tenant)->get();
        $sms = TenantSMS::where('tenant_id', $tenant)->get()->first();
                
        return view('communications.tenant.index',compact('sms', 'staff', 'clients'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendClients(ClientCommunicationFormRequest $request)
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;
        $name = Tenant::where('id', $tenant)->value('name');
        $message = $request->message . PHP_EOL . 'By ' . $name;
        $sms_count = $this->smsCount($message, $tenant);
        $receiver = Input::get('clients');

        if(in_array("all", $receiver)){            
            $all = [];
            $clients = Client::where('tenant_id', $tenant)->get();

            foreach ($clients as $client) {
                $all = array_prepend($all, $client->phone_number);
                if ($client->phone_number_2 != 0) {
                    $all = array_prepend($all, $client->phone_number_2);                
                }
            }
            $client_count = count($all);            

            $smsBalance = $this->smsBalanceChecker($client_count, $sms_count, $tenant);

            if ($smsBalance) {

                $this->smsSender($message, $all, $tenant);

                $this->updateBalance($client_count, $sms_count, $tenant);

                $this->saveReport($all, $message, $sms_count, $tenant);
                
                Alert::success('SMS Sent Successfully!');

                return back();

            }else{
                Alert::error('SMS Not Sent!', 'Insuficient Balance');

                return back();
            }
        }

        $selectedClients = [];

        foreach ($receiver as $id) {
            $client = Client::findorfail($id);
            $selectedClients = array_prepend($selectedClients, $client->phone_number);
            if ($client->phone_number_2 != 0) {
                $selectedClients = array_prepend($selectedClients, $client->phone_number_2);                
            }
        }

        $client_count = count($selectedClients);            

        $smsBalance = $this->smsBalanceChecker($client_count, $sms_count, $tenant);

        if ($smsBalance) {

            $this->smsSender($message, $selectedClients, $tenant);

            $this->updateBalance($client_count, $sms_count, $tenant);

            $this->saveReport($selectedClients, $message, $sms_count, $tenant);
            
            Alert::success('SMS Sent Successfully!');

            return back();

        }else{
            Alert::error('SMS Not Sent!', 'Insuficient Balance');

            return back();
        }              
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendStaff(StaffCommunicationFormRequest $request)
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;
        $name = Tenant::where('id', $tenant)->value('name');
        $message = $request->message1 . PHP_EOL . 'By ' . $name;
        $sms_count = $this->smsCount($message, $tenant);
        $receiver = Input::get('staff');

        if(in_array("all", $receiver)){            
            $all = [];
            $staffs = Staff::where('tenant_id', $tenant)->get();

            foreach ($staffs as $staff) {
                $all = array_prepend($all, $staff->phone_number);
            }
            $staff_count = count($all);            

            $smsBalance = $this->smsBalanceChecker($staff_count, $sms_count, $tenant);

            if ($smsBalance) {

                $this->smsSender($message, $all, $tenant);

                $this->updateBalance($staff_count, $sms_count, $tenant);

                $this->saveReport($all, $message, $sms_count, $tenant);
                
                Alert::success('SMS Sent Successfully!');

                return back();

            }else{
                Alert::error('SMS Not Sent!', 'Insuficient Balance');

                return back();
            }
        }

        $selectedStaff = [];

        foreach ($receiver as $id) {
            $staff = Staff::findorfail($id);
            $selectedStaff = array_prepend($selectedStaff, $staff->phone_number);
        }

        $staff_count = count($selectedStaff);            

        $smsBalance = $this->smsBalanceChecker($staff_count, $sms_count, $tenant);

        if ($smsBalance) {

            $this->smsSender($message, $selectedStaff, $tenant);

            $this->updateBalance($staff_count, $sms_count, $tenant);

            $this->saveReport($selectedStaff, $message, $sms_count, $tenant);
            
            Alert::success('SMS Sent Successfully!');

            return back();

        }else{
            Alert::error('SMS Not Sent!', 'Insuficient Balance');

            return back();
        }              
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendBulk(BulkCommunicationFormRequest $request)
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;
        $name = Tenant::where('id', $tenant)->value('name');

        $message = $request->message . PHP_EOL . 'By ' . $name;
        $sms_count = $this->smsCount($message, $tenant);
        $receiver = Input::get('receiver');


        if(in_array("all", $receiver)){
            $groups = Group::where('tenant_id', $tenant)->get();

            $clients = [];

            foreach ($groups as $group) {
                foreach ($group->contacts as $contact) {
                    $clients = array_prepend($clients, $contact->phone_number);
                }
            }

            $client_count = count($clients);
            $smsBalance = $this->smsBalanceChecker($client_count, $sms_count, $tenant);

            if ($smsBalance) {

                $this->smsSender($message, $clients, $tenant);

                $this->updateBalance($client_count, $sms_count, $tenant);


                $this->saveReport($clients, $message, $sms_count, $tenant);                    
                
                Alert::success('SMS Sent Successfully!');

                return back();
            }else{
                Alert::error('SMS Not Sent!', 'Please Contact System Admin');

                return back();
            }
        }

        $clients = [];

        foreach ($receiver as $one) {
            $group = Group::findorfail($one);

            foreach ($group->contacts as $contact) {
                $clients = array_prepend($clients, $contact->phone_number);
            }
        }

        $client_count = count($clients);
        $smsBalance = $this->smsBalanceChecker($client_count, $sms_count, $tenant);

        if ($smsBalance) {

            $this->smsSender($message, $clients, $tenant);

            $this->updateBalance($client_count, $sms_count, $tenant);

            $this->saveReport($clients, $message, $sms_count, $tenant);                    
            
            Alert::success('SMS Sent Successfully!');

            return back();
        }else{
            Alert::error('SMS Not Sent!', 'Please Contact System Admin');

            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendGroup(GroupCommunicationFormRequest $request)
    {
        /// Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;
        $name = Tenant::where('id', $tenant)->value('name');
        $message = $request->message . PHP_EOL . 'By ' . $name;
        $sms_count = $this->smsCount($message, $tenant);
        $receiver = Input::get('receiver');

        if(in_array("all", $receiver)){
            $group = Group::findorfail($request->group);

            $clients = [];

            foreach ($group->contacts as $contact) {
                $clients = array_prepend($clients, $contact->phone_number);
            }

            $client_count = count($clients);
            $smsBalance = $this->smsBalanceChecker($client_count, $sms_count, $tenant);

            if ($smsBalance) {

                $this->smsSender($message, $clients, $tenant);

                $this->updateBalance($client_count, $sms_count, $tenant);

                $this->saveReport($clients, $message, $sms_count, $tenant);                    
                
                Alert::success('SMS Sent Successfully!');

                return back();
            }else{
                Alert::error('SMS Not Sent!', 'Please Contact System Admin');

                return back();
            }
        }


        $client_count = count($receiver);
        $smsBalance = $this->smsBalanceChecker($client_count, $sms_count, $tenant);

        if ($smsBalance) {

            $this->smsSender($message, $receiver, $tenant);

            $this->updateBalance($client_count, $sms_count, $tenant);

            $this->saveReport($receiver, $message, $sms_count, $tenant);                    
            
            Alert::success('SMS Sent Successfully!');

            return back();
        }else{
            Alert::error('SMS Not Sent!', 'Please Contact System Admin');

            return back();
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
