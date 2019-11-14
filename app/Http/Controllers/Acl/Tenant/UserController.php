<?php

namespace App\Http\Controllers\Acl\Tenant;

use Alert;
use Sentinel;
use App\Staff;
use App\Http\Requests;
use Centaur\AuthManager;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Centaur\Mail\CentaurWelcomeEmail;
use Cartalyst\Sentinel\Users\IlluminateUserRepository;
use App\Http\Requests\TenantUserRequestForm as TenantUserRequestForm;
use App\Http\Requests\TenantEditUserRequestForm as TenantEditUserRequestForm;

class UserController extends Controller
{
    /** @var Cartalyst\Sentinel\Users\IlluminateUserRepository */
    protected $userRepository;

    /** @var Centaur\AuthManager */
    protected $authManager;

    public function __construct(AuthManager $authManager)
    {
        // Middleware
        $this->middleware('sentinel.auth');
        $this->middleware('sentinel.access:users.create', ['only' => ['create', 'store']]);
        $this->middleware('sentinel.access:users.view', ['only' => ['index', 'show']]);
        $this->middleware('sentinel.access:users.update', ['only' => ['edit', 'update']]);
        $this->middleware('sentinel.access:users.destroy', ['only' => ['destroy']]);

        // Dependency Injection
        $this->userRepository = app()->make('sentinel.users');
        $this->authManager = $authManager;
    }

    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieving tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $users = $this->userRepository->createModel()->where(('tenant_id'), $tenant)->with('roles')->paginate(15);

        return view('acl.tenant.users.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tenant = Sentinel::getUser()->tenant;

        return view('acl.tenant.users.create', compact('tenant'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TenantUserRequestForm $request)
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;
        
        /* Save Tenant Staff Details */
        $staff = new Staff;

        $staff->first_name        = $request->first_name;
        $staff->last_name         = $request->last_name;
        $staff->phone_number      = $request->phone_number;
        $staff->office_id         = $request->office;
        $staff->staff_type_id     = $request->staff;
        $staff->tenant_id         = $tenant;

        $staff->save();

        // Assemble registration credentials and attributes
        $user_password = str_random(8);
        $credentials = [
            'email' => trim($request->get('email')),
            'password' => $user_password,
            'staff_id' => $staff->id,
            'tenant_id' => $tenant,
        ];
        $activate = (bool)(false);

        // Attempt the registration
        $result = $this->authManager->register($credentials, $activate);

        if ($result->isFailure()) {
            return $result->dispatch;
        }

        // Do we need to send an activation email?
        if (!$activate) {
            $email = $result->user->email;
            $tenant = $result->user->tenant->name;
            $code = $result->activation->getCode();
            $tech_support = config('app.tech_support');
            $last_name = $result->user->last_name;

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

        Alert::success("User {$request->get('email')} has been created.", 'User Created');
        return $result->dispatch(route('users.index'));
    }

    /**
     * Display the specified user.
     *
     * @param  string  $hash
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // The user detail page has not been included for the sake of brevity.
        // Change this to point to the appropriate view for your project.
        return redirect()->route('users.index');
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  string  $hash
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Fetch the user object
        // $id = $this->decode($hash);
        $user = $this->userRepository->findById($id);

        if ($user) {
            return view('acl.tenant.users.edit', [
                'user' => $user
            ]);
        }

        Alert::error('Invalid User', 'Error');
        return redirect()->back();
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $hash
     * @return \Illuminate\Http\Response
     */
    public function update(TenantEditUserRequestForm $request, $id)
    {
        // Fetch the user object
        $user = $this->userRepository->findById($id);
        if (!$user) {
            if ($request->ajax()) {
                return response()->json("Invalid user.", 422);
            }
            Alert::error('Invalid User.', 'Error');
            return redirect()->back()->withInput();
        }

        $staff = Staff::findorfail($user->staff->id);

        $staff->first_name        = $request->first_name;
        $staff->middle_name       = $request->middle_name;
        $staff->last_name         = $request->last_name;
        $staff->phone_number      = $request->phone_number;

        $staff->save();

        // Assemble the updated attributes
        $attributes = [
            'email' => trim($request->get('email')),
        ];

        // Do we need to update the password as well?
        if ($request->password != null) {
            $attributes['password'] = $request->get('password');
        }        

        // Update the user
        $user = $this->userRepository->update($user, $attributes);

        // All done
        if ($request->ajax()) {
            return response()->json(['user' => $user], 200);
        }

        Alert::success("Profile has been Updated.", 'User Updated');
        return redirect()->route('users.edit', Sentinel::getUser()->id);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  string  $hash
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // Fetch the user object
        //$id = $this->decode($hash);
        $user = $this->userRepository->findById($id);

        // Check to be sure user cannot delete himself
        if (Sentinel::getUser()->id == $user->id) {
            $message = "You cannot remove yourself!";

            if ($request->ajax()) {
                return response()->json($message, 422);
            }
            
            Alert::error($message, 'Haha, So Funny!');
            return redirect()->route('users.index');
        }

        // Remove the user
        $user->delete();

        // All done
        $message = "{$user->email} has been removed.";
        if ($request->ajax()) {
            return response()->json([$message], 200);
        }

        Alert::success($message, 'User Removed');
        return redirect()->route('users.index');
    }

    /**
     * Decode a hashid
     * @param  string $hash
     * @return integer|null
     */
    // protected function decode($hash)
    // {
    //     $decoded = $this->hashids->decode($hash);

    //     if (!empty($decoded)) {
    //         return $decoded[0];
    //     } else {
    //         return null;
    //     }
    // }
}
