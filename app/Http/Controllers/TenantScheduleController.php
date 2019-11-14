<?php

namespace App\Http\Controllers;

use Alert;
use Redirect;
use Sentinel;

use App\TenantSchedule;
use Illuminate\Http\Request;
use App\Http\Requests\ScheduleEditFormRequest as ScheduleEditFormRequest;

class TenantScheduleController extends Controller
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
        $tenant = Sentinel::getUser()->tenant;

        return view('settings.tenant.schedules.index', compact('tenant'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $schedule = TenantSchedule::findorfail($id);

        return view('settings.tenant.schedules.edit', compact('schedule'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ScheduleEditFormRequest $request, $id)
    {
        $schedule = TenantSchedule::findorfail($id);

        $schedule->alert      = $request->time;

        $schedule->save();

        Alert::success('Schedule Time Editted Successfully!');

        return Redirect::to('schedules');
    }
}
