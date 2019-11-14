<?php

namespace App\Http\Controllers;

use Alert;
use Redirect;
use Sentinel;

use App\Staff;
use Centaur\AuthManager;
use Illuminate\Http\Request;
use App\Http\Requests\StaffFormRequest as StaffFormRequest;
use App\Http\Requests\StaffEditFormRequest as StaffEditFormRequest;

class StaffController extends Controller
{
    public function __construct(AuthManager $authManager)
    {
        // Middleware
        $this->middleware('sentinel.auth');
        $this->middleware('sentinel.access:staff.create', ['only' => ['create', 'store']]);
        $this->middleware('sentinel.access:staff.view', ['only' => ['index', 'show']]);
        $this->middleware('sentinel.access:staff.update', ['only' => ['edit', 'update']]);
        $this->middleware('sentinel.access:staff.destroy', ['only' => ['destroy']]);

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
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $staff = Staff::where('tenant_id', $tenant)->get();

        return view('staff.index', compact('staff'));
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

        return view('staff.create', compact('tenant'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StaffFormRequest $request)
    {
        $tenant = Sentinel::getUser()->tenant->id;

        /* Save Tenant Staff Details */
        $staff = new Staff;

        $staff->first_name        = $request->first_name;
        $staff->middle_name       = $request->middle_name;
        $staff->last_name         = $request->last_name;
        $staff->phone_number      = $request->phone_number;
        $staff->office_id         = $request->office;
        $staff->staff_type_id     = $request->type;
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
            Alert::error('Staff User Account Not Registered!');

            return Redirect::to('staff');
        }

        // Assign User Roles
        foreach ($request->get('roles', []) as $slug => $id) {
            $role = Sentinel::findRoleBySlug($slug);
            if ($role) {
                $role->users()->attach($result->user);
            }
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

        Alert::success('New Staff Registered!');

        return Redirect::to('staff');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $staff = Staff::findorfail($id);
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant;
        
        return view('staff.edit', compact('staff', 'tenant'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StaffEditFormRequest $request, $id)
    {
        $staff = Staff::findorfail($id);

        $staff->first_name        = $request->first_name;
        $staff->middle_name       = $request->middle_name;
        $staff->last_name         = $request->last_name;
        $staff->phone_number      = $request->phone_number;
        $staff->office_id         = $request->office;
        $staff->staff_type_id     = $request->type;

        $staff->save();

        // Assemble the updated attributes
        $attributes = [
            'email' => trim($request->get('email')),
        ];

        // Do we need to update the password as well?
        if ($request->password != null) {
            $attributes['password'] = $request->get('password');
        }

        // Fetch the user object
        $user = $this->userRepository->findById($staff->user->id);
        if (!$user) {
            if ($request->ajax()) {
                return response()->json("Invalid user.", 422);
            }
            Alert::error('Invalid User.', 'Error');
            return redirect()->back()->withInput();
        }

        // Update the user
        $user = $this->userRepository->update($user, $attributes);

        // Update role assignments
        $roleIds = array_values($request->get('roles', []));
        $user->roles()->sync($roleIds);

        // All done
        if ($request->ajax()) {
            return response()->json(['user' => $user], 200);
        }

        Alert::success('Staff Details Updated Successfully!');

        return Redirect::to('staff');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $staff  = Staff::findorfail($id);

        // Check to be sure user cannot delete himself
        if (Sentinel::getUser()->id == $staff->user->id) {
            $message = "You cannot remove yourself!";
            
            Alert::error($message, 'Haha, So Funny!');
            return back();
        }

        $staff->user->delete();
        $staff->delete();

        Alert::success('Staff Removed Successfully!');

        return Redirect::to('staff');
    }
}
