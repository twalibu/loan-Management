<?php

namespace App\Http\Controllers\Acl\Tenant;

use Alert;
use Sentinel;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Cartalyst\Sentinel\Users\IlluminateUserRepository;
use App\Http\Requests\TenantRoleRequestForm as TenantRoleRequestForm;
use App\Http\Requests\TenantEditRoleRequestForm as TenantEditRoleRequestForm;

class RoleController extends Controller
{
    /** @var Cartalyst\Sentinel\Users\IlluminateRoleRepository */
    protected $roleRepository;

    public function __construct()
    {
        // Middleware
        $this->middleware('sentinel.auth');
        $this->middleware('sentinel.access:roles.create', ['only' => ['create', 'store']]);
        $this->middleware('sentinel.access:roles.view', ['only' => ['index', 'show']]);
        $this->middleware('sentinel.access:roles.update', ['only' => ['edit', 'update']]);
        $this->middleware('sentinel.access:roles.destroy', ['only' => ['destroy']]);

        // Fetch the Role Repository from the IoC container
        $this->roleRepository = app()->make('sentinel.roles');
    }

    /**
     * Display a listing of the roles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieving Institute Details
        $tenant = Sentinel::getUser()->tenant->id;

        $roles = $this->roleRepository->createModel()->where('tenant_id', $tenant)->get();

        return view('acl.tenant.roles.index')
            ->with('roles', $roles);
    }

    /**
     * Show the form for creating a new role.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('acl.tenant.roles.create');
    }

    /**
     * Store a newly created role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TenantRoleRequestForm $request)
    {
        $slug = Sentinel::getUser()->tenant->id . trim($request->get('slug'));
        // Create the Role
        $role = Sentinel::getRoleRepository()->createModel()->create([
            'name' => trim($request->get('name')),
            'slug' => $slug,
            'tenant_id' => Sentinel::getUser()->tenant->id,
        ]);

        // Cast permissions values to boolean
        $permissions = [];
        foreach ($request->get('permissions', []) as $permission => $value) {
            $permissions[$permission] = (bool)$value;
        }

        // Set the role permissions
        $role->permissions = $permissions;
        $role->save();

         // All done
        if ($request->ajax()) {
            return response()->json(['role' => $role], 200);
        }

        Alert::success("Role '{$role->name}' has been created.", 'Role Created');
        return redirect()->route('roles.index');
    }

    /**
     * Display the specified role.
     *
     * @param  string  $hash
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // The roles detail page has not been included for the sake of brevity.
        // Change this to point to the appropriate view for your project.
        return redirect()->route('roles.index');
    }

    /**
     * Show the form for editing the specified role.
     *
     * @param  string  $hash
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Fetch the role object
        // $id = $this->decode($hash);
        $role = $this->roleRepository->findById($id);

        if ($role) {
            return view('acl.tenant.roles.edit')
                ->with('role', $role);
        }

        Alert::error('Invalid Role', 'Error');
        return redirect()->back();
    }

    /**
     * Update the specified role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $hash
     * @return \Illuminate\Http\Response
     */
    public function update(TenantEditRoleRequestForm $request, $id)
    {
        // Decode the role id
        // $id = $this->decode($hash);

        // Fetch the role object
        $role = $this->roleRepository->findById($id);
        if (!$role) {
            if ($request->ajax()) {
                return response()->json("Invalid role.", 422);
            }
            Alert::error('Invalid Role', 'Error');
            return redirect()->back()->withInput();
        }

        // Update the role
        $role->name = $request->get('name');

        // Cast permissions values to boolean
        $permissions = [];
        foreach ($request->get('permissions', []) as $permission => $value) {
            $permissions[$permission] = (bool)$value;
        }

        // Set the role permissions
        $role->permissions = $permissions;
        $role->save();

        // All done
        if ($request->ajax()) {
            return response()->json(['role' => $role], 200);
        }

        Alert::success("Role '{$role->name}' has been updated.", 'Role Editted!');
        return redirect()->route('roles.index');
    }

    /**
     * Remove the specified role from storage.
     *
     * @param  string  $hash
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // Fetch the role object
        // $id = $this->decode($hash);
        $role = $this->roleRepository->findById($id);
        $slug = Sentinel::getUser()->tenant->id . ('administrator');
        
        if ($role->slug == $slug) {
            Alert::error('Sorry You Can Not Delete the Original Role', 'System Error')->persistent('Close');
            return redirect()->back();
        }

        if ($role->users->count() > 0) {
            Alert::error('Sorry You Can Not Delete Role It Has Users. Please Remove First', 'Error')->persistent('Close');
            return redirect()->back();
        }

        // Remove the role
        $role->delete();

        // All done
        $message = "Role '{$role->name}' has been removed.";
        if ($request->ajax()) {
            return response()->json([$message], 200);
        }

        Alert::success($message);
        return redirect()->route('roles.index');
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
