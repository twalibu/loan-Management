<?php

namespace App\Http\Controllers;

use Alert;
use Redirect;
use Sentinel;

use App\StaffType;
use Illuminate\Http\Request;
use App\Http\Requests\TypeFormRequest as TypeFormRequest;
use App\Http\Requests\TypeEditFormRequest as TypeEditFormRequest;

class TypeController extends Controller
{
    public function __construct()
    {
        // Middleware
        $this->middleware('sentinel.auth');
        $this->middleware('sentinel.access:settings.access');
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

        $types = StaffType::where('tenant_id', $tenant)->get();

        return view('settings.tenant.staff_type.index', compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('settings.tenant.staff_type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TypeFormRequest $request)
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $type = new StaffType;

        $type->name       = $request->name;
        $type->tenant_id  = $tenant;

        $type->save();

        Alert::success('New Staff Type Registered!');

        return Redirect::to('types');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $type = StaffType::findorfail($id);

        return view('settings.tenant.staff_type.edit', compact('type'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TypeEditFormRequest $request, $id)
    {
        $type = StaffType::findorfail($id);

        $type->name      = $request->name;

        $type->save();

        Alert::success('Staff Type Edited Successfully!');

        return Redirect::to('types');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $type = StaffType::findorfail($id);

        if ($type->staff->count() > 0) {
            Alert::error('Staff Type Has Staff Registered. Please Remove The Staff First!', 'Error')->persistent('Close');

            return redirect()->back();
        }

        $type->delete();

        Alert::success('Staff Type Removed Successfully!');

        return Redirect::to('types');
    }
}
