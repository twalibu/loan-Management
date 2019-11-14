<?php

namespace App\Http\Controllers;

use Mail;
use Crypt;
use Alert;
use Carbon;
use Sentinel;
use Redirect;

use App\User;
use App\Role;
use App\Staff;
use App\Office;
use App\Client;
use App\Tenant;
use App\Region;
use App\StaffType;
use App\TenantSMS;
use App\TenantSale;
use App\TenantAddress;
use App\TenantContact;
use App\Http\Requests;
use App\TenantSchedule;
use App\SubscriptionType;
use App\SubscriptionAlert;
use App\TenantSubscription;


use Centaur\AuthManager;
use Illuminate\Http\Request;
use infobip\api\client\GetAccountBalance;
use infobip\api\client\SendSingleTextualSms;
use infobip\api\configuration\BasicAuthConfiguration;
use infobip\api\model\sms\mt\send\textual\SMSTextualRequest;
use Cartalyst\Sentinel\Users\IlluminateUserRepository;
use App\Http\Requests\TenantFormRequest as TenantFormRequest;
use App\Http\Requests\TenantEditFormRequest as TenantEditFormRequest;
use App\Http\Requests\TenantAdminFormRequest as TenantAdminFormRequest;
use App\Http\Requests\TenantContactFormRequest as TenantContactFormRequest;

class TenantController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AuthManager $authManager)
    {
        // You must have admin access to proceed
        $this->middleware('sentry.auth');

        // Dependency Injection
        $this->userRepository = app()->make('sentinel.users');
        $this->authManager = $authManager;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tenants = Tenant::all();

        return view('tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $regions = Region::all();
        $types = SubscriptionType::all();

        return view('tenants.create', compact('regions', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function admin($id)
    {
        $tenant = Tenant::findorfail($id);

        return view('tenants.admin', compact('tenant'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function contact($id)
    {
        $tenant = Tenant::findorfail($id);

        return view('tenants.contact', compact('tenant'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postAdmin(TenantAdminFormRequest $request)
    {
        /* Save Tenant Staff Details */
        $staff = new Staff;

        $staff->first_name        = $request->first_name;
        $staff->last_name         = $request->last_name;
        $staff->phone_number      = $request->phone_number;
        $staff->office_id         = $request->office;
        $staff->staff_type_id     = $request->staff;
        $staff->tenant_id         = $request->tenant;

        $staff->save();

        // Assemble registration credentials and attributes
        $user_password = str_random(8);
        $credentials = [
            'email' => trim($request->get('email')),
            'password' => $user_password,
            'staff_id' => $staff->id,
            'tenant_id' => $request->get('tenant'),
        ];
        $activate = (bool)(false);

        // Attempt the registration
        $result = $this->authManager->register($credentials, $activate);

        if ($result->isFailure()) {
            $tenant = Tenant::findorfail($request->tenant);

            Alert::error('New Tenant Admin Not Registered!');

            return redirect()->action(
                'TenantController@show', ['id' => $tenant->id]
            );
        }

        // Do we need to send an activation email?
        if (!$activate) {
            $email = $result->user->email;
            $tenant = $staff->tenant->name;
            $code = $result->activation->getCode();
            $tech_support = config('app.tech_support');
            $last_name = $result->user->staff->last_name;

            $data = [
                'code' => $code,
                'email' => $email,
                'tenant' => $tenant,
                'last_name' => $last_name,
                'password' => $user_password,
                'tech_support' => $tech_support,
            ];

            $beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
            $beautymail->send('mails.tenants.registration', $data, function($message) use ($email)
            {
                $message
                    ->from('no-reply@loan-alert.pro', 'Loan Alert')
                    ->to($email)
                    ->subject('Loan Alert | Account Activation');
            });
        }

        // Assign User Roles
        foreach ($request->get('roles', []) as $slug => $id) {
            $role = Sentinel::findRoleBySlug($slug);
            if ($role) {
                $role->users()->attach($result->user);
            }
        }

        $tenant = Tenant::findorfail($request->tenant);

        Alert::success('New Tenant Admin Registered!');

        return redirect()->action(
            'TenantController@show', ['id' => $tenant->id]
        );
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postContact(TenantContactFormRequest $request)
    {
        /* Save Tenant Contact Details */
        $contact = new TenantContact;

        $contact->first_name        = $request->first_name;
        $contact->last_name         = $request->last_name;
        $contact->position          = $request->position;
        $contact->phone_number      = $request->phone_number;
        $contact->email             = $request->email;
        $contact->tenant_id         = $request->tenant;

        $contact->save();

        Alert::success('New Tenant Contact Registered!');

        return redirect()->action(
            'TenantController@show', ['id' => $request->tenant]
        );      
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TenantFormRequest $request)
    {
        /* Save Tenant */
        $tenant = new Tenant;

        $tenant->name       = $request->name;
        $tenant->slug       = $request->slug;

        $tenant->save();

        /* Compute & Save Subscription & Alert */
        $type = SubscriptionType::findorfail($request->subscription);
        $start_date = Carbon::now();
        $end_date = Carbon::now()->addMonths($type->duration);

        $subscription = new TenantSubscription;

        $subscription->subscription_id      = $type->id;
        $subscription->start_date           = $start_date;
        $subscription->end_date             = $end_date;
        $subscription->tenant_id            = $tenant->id;

        $subscription->save();

        $alert = new SubscriptionAlert;

        $alert->alert_one           = Carbon::parse($subscription->end_date)->subDays(21);
        $alert->alert_two           = Carbon::parse($subscription->end_date)->subDays(14);
        $alert->alert_three         = Carbon::parse($subscription->end_date)->subDays(7);
        $alert->subscription_id     = $subscription->id;

        $alert->save();

        /* Save Tenant Address */
        $address = new TenantAddress;

        $address->address           = $request->address;
        $address->region_id         = $request->region;
        $address->tenant_id         = $tenant->id;

        $address->save();

        /* Save Tenant SMS Details */
        $sms = new TenantSMS;

        $sms->username          = $request->smsusername;
        $sms->password          = Crypt::encrypt($request->smspassword);
        $sms->sender_name       = $request->sender;
        $sms->price             = $request->price;
        $sms->balance           = $request->balance;
        $sms->tenant_id         = $tenant->id;

        $sms->save();

        /* Save Tenant Contact Details */
        $contact = new TenantContact;

        $contact->first_name        = $request->first_name;
        $contact->last_name         = $request->last_name;
        $contact->position          = $request->position;
        $contact->phone_number      = $request->phone_number;
        $contact->email             = $request->email;
        $contact->tenant_id         = $tenant->id;

        $contact->save();

        /* Save Tenant Sales Contact Details */
        $sales = new TenantSale;

        $sales->phone_number      = $request->phone_number;
        $sales->email             = $request->email;
        $sales->tenant_id         = $tenant->id;

        $sales->save();

        /* Innitiate Tenant Schedule */
        $schedule = new TenantSchedule;

        $schedule->alert          = '07:00:00';
        $schedule->tenant_id         = $tenant->id;

        $schedule->save();

        /* Tenant Office */
        $office = new Office;

        $office->name              = $request->office;
        $office->tenant_id         = $tenant->id;

        $office->save();

        /* Tenant Staff Type */
        $type = new StaffType;

        $type->name              = $request->position;
        $type->tenant_id         = $tenant->id;

        $type->save();

        /* Save Tenant Staff Details */
        $staff = new Staff;

        $staff->first_name        = $request->first_name;
        $staff->last_name         = $request->last_name;
        $staff->phone_number      = $request->phone_number;
        $staff->office_id         = $office->id;
        $staff->staff_type_id     = $type->id;
        $staff->tenant_id         = $tenant->id;

        $staff->save();

        /* Innitiate Tenant Admin Role */
        $slug = $tenant->slug . 'administrator';
        Sentinel::getRoleRepository()->create(array(
            'name' => 'Administrator',
            'slug' => $slug,
            'tenant_id' => $tenant->id,
            'permissions' => array(
                'users.create'              => true,
                'users.update'              => true,
                'users.view'                => true,
                'users.destroy'             => true,
                'roles.create'              => true,
                'roles.update'              => true,
                'roles.view'                => true,
                'roles.destroy'             => true,
                'clients.create'            => true,
                'clients.update'            => true,
                'clients.view'              => true,
                'clients.destroy'           => true,
                'staff.create'              => true,
                'staff.update'              => true,
                'staff.view'                => true,
                'staff.destroy'             => true,
                'offices.create'            => true,
                'offices.update'            => true,
                'offices.view'              => true,
                'offices.destroy'           => true,
                'loans.create'              => true,
                'loans.update'              => true,
                'loans.view'                => true,
                'loans.destroy'             => true,
                'payments.create'           => true,
                'payment.overwrite'         => true,
                'payments.update'           => true,
                'payments.view'             => true,
                'payments.destroy'          => true,
                'tools.access'              => true,
                'settings.access'           => true,
                'communications.access'     => true,
            )
        ));

        $user_password = str_random(8);

        // Assemble registration credentials
        $credentials = [
            'email' => trim($request->email),
            'password'  => $user_password,
            'staff_id'  => $staff->id,
            'tenant_id' => $tenant->id,
        ];

        $activate = (bool)(false);

        // Attempt the registration
        $result = $this->authManager->register($credentials, $activate);

        if ($result->isFailure()) {
            return $result->dispatch;
        }

        // Assign Role to User
        $role = Sentinel::findRoleBySlug($slug);
        if ($role) {
            $role->users()->attach($result->user);
        }

        // Do we need to send an activation email?
        if (!$activate) {
            // Send the activation email
            $code = $result->activation->getCode();
            $email = $result->user->email;

            $email = $contact->email;
            $data = [
                'email'         => $contact->email,
                'first_name'    => $contact->first_name,
                'last_name'     => $contact->last_name,
                'position'      => $contact->position,
                'tenant'        => $tenant->name,
                'password'      => $user_password,
                'code'          => $code,
            ];

            $beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
            $beautymail->send('mails.tenants.activation', $data, function($message) use($email)
            {
                $message
                    ->from('no-reply@loan-alert.pro')
                    ->to($email)
                    ->subject('Welcome Message | Loan Alert!');
            });
        }

        /* Send Welcome Message to User */
        $username = env('INFOBIP_USERNAME');
        $password = env('INFOBIP_PASSWORD');
        $sender = env('INFOBIP_SENDER');

        $message = 'Hello '. $request->first_name . ' ' . $request->last_name . '. Thank you for joining Loan Alert Platform.'. PHP_EOL .'Your Organization "' . $tenant->name .'" has been registered. Please Follow Instructions sent to your email to continue.' . PHP_EOL . 'From TechLegend Team';

        // Initializing SendSingleTextualSms client with appropriate configuration
        $client = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));

        // Creating request body
        $requestBody = new SMSTextualRequest();
        $requestBody->setFrom($sender);
        $requestBody->setTo($request->phone_number);
        $requestBody->setText($message);

        // Executing request
        $response = $client->execute($requestBody);

        Alert::success('New Tenant Registered!');

        return redirect()->action(
            'TenantController@show', ['id' => $tenant->id]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tenant = Tenant::findorfail($id);

        return view('tenants.show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $regions = Region::all();
        $tenant = Tenant::findorfail($id);

        return view('tenants.edit', compact('tenant', 'regions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TenantEditFormRequest $request, $id)
    {
        $tenant = Tenant::findorfail($id);

        $tenant->name       = $request->name;

        $tenant->save();

        $sms = TenantSMS::where('tenant_id', $id)->first();

        $sms->sender_name       = $request->sender;

        $sms->save();

        $address = TenantAddress::where('tenant_id', $id)->first();

        $address->region_id         = $request->region;
        $address->address           = $request->address;

        $address->save();

        $sms = TenantSMS::where('tenant_id', $id)->first();

        $sms->sender_name         = $request->sender;

        if ($request->username) {
            $sms->username   = $request->username;
        }

        if ($request->password) {
            $sms->password   = Crypt::encrypt($request->password);
        }

        $sms->save();

        Alert::success('Tenant Edited Successfully!');

        return redirect()->action(
            'TenantController@show', ['id' => $tenant->id]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {  
        $tenant = Tenant::findorfail($id);

        $tenant->sms->delete();
        $tenant->address->delete();
        $tenant->schedule->delete();
        $tenant->subscription->alert->delete();
        $tenant->subscription->delete();

        foreach ($tenant->contacts as $contact) {
            $contact->delete();
        }

        foreach ($tenant->loans as $loan) {
            $loan->contract->delete();
            $loan->summary->delete();

            foreach ($loan->penalts as $penalt) {
                $penalt->delete();
            }

            foreach ($loan->overwrites as $overwrite) {
                $overwrite->delete();
            }

            foreach ($loan->payments as $payment) {
                $payment->delete();
            }

            foreach ($loan->schedules as $schedule) {
                $schedule->delete();
            }

            $loan->delete();
        }

        foreach ($tenant->loanTypes as $type) {
            $type->delete();
        }

        foreach ($tenant->offices as $office) {
            $office->delete();
        }

        foreach ($tenant->paymentMethods as $method) {
            $method->delete();
        }        

        foreach ($tenant->clients as $client) {
            $client->delete();
        }

        foreach ($tenant->users as $user) {
            $user->delete();
        }

        foreach ($tenant->roles as $role) {
            $role->delete();
        }

        foreach ($tenant->types as $type) {
            $type->delete();
        }

        foreach ($tenant->staff as $staff) {
            $staff->delete();
        }

        foreach ($tenant->groups as $group) {
            $group->contacts->delete();
        }

        $tenant->delete();

        Alert::success('Tenant Delete Successfully!');

        return Redirect::to('admin/tenants');        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyContact($id)
    {  
        $contact = TenantContact::findorfail($id);

        $tenant = $contact->tenant->id;

        $contact->delete();

        Alert::success('Tenant Contact Delete Successfully!');

        return redirect()->action(
            'TenantController@show', ['id' => $tenant]
        );
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  string  $hash
     * @return \Illuminate\Http\Response
     */
    public function destroyAdmin($id)
    {
        // Fetch the user object
        //$id = $this->decode($hash);
        $user = $this->userRepository->findById($id);

        $tenant = $user->tenant->id;

        foreach($user->staff->casets as $caset)
        {
            if ($caset->staff->count() < 2) {
                Alert::success('Please Allocate Replacement Staff to cases held by '. $user->staff->last_name . ' first!');

                return back();
            }
        }        
        $user->staff->delete();
        $user->delete();

        // All done
        $message = "{$user->email} has been removed.";

        Alert::success($message, 'User Removed');
        
        return redirect()->action(
            'TenantController@show', ['id' => $tenant]
        );
    }


}
