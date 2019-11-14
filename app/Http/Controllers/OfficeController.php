<?php

namespace App\Http\Controllers;

use Alert;
use Redirect;
use Sentinel;

use App\Office;
use Illuminate\Http\Request;
use App\Http\Requests\OfficeFormRequest as OfficeFormRequest;
use App\Http\Requests\OfficeEditFormRequest as OfficeEditFormRequest;

class OfficeController extends Controller
{
    public function __construct()
    {
        // Middleware
        $this->middleware('sentinel.auth');
        $this->middleware('sentinel.access:offices.create', ['only' => ['create', 'store']]);
        $this->middleware('sentinel.access:offices.view', ['only' => ['index', 'show']]);
        $this->middleware('sentinel.access:offices.update', ['only' => ['edit', 'update']]);
        $this->middleware('sentinel.access:offices.destroy', ['only' => ['destroy']]);
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

        $offices = Office::where('tenant_id', $tenant)->get();

        return view('offices.index', compact('offices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('offices/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OfficeFormRequest $request)
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $office = new Office;

        $office->name       = $request->name;
        $office->tenant_id  = $tenant;

        $office->save();

        Alert::success('New Office Registered!');

        return Redirect::to('offices');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $office = Office::findorfail($id);

        return view('offices.show', compact('office'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $office = Office::findorfail($id);

        return view('offices.edit', compact('office'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OfficeEditFormRequest $request, $id)
    {
        $office = Office::findorfail($id);

        $office->name      = $request->name;

        $office->save();

        Alert::success('Office Details Edited Successfully!');

        return Redirect::to('offices');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $office = Office::findorfail($id);

        if ($office->clients->count() > 0) {
            Alert::error('Office has Clients Registered. Please Remove The Clients First!', 'Error')->persistent('Close');

            return redirect()->back();
        }

        if ($office->staff->count() > 0) {
            Alert::error('Office has Staff Registered. Please Remove The Staff First!', 'Error')->persistent('Close');

            return redirect()->back();
        }

        if ($office->loans->count() > 0) {
            Alert::error('Office has Loans Registered. Please Remove The Loans First!', 'Error')->persistent('Close');

            return redirect()->back();
        }

        $office->delete();

        Alert::success('Office Removed Successfully!');

        return Redirect::to('offices');
    }
}
