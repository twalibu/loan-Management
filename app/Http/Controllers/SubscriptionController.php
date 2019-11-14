<?php

namespace App\Http\Controllers;

use Alert;
use Redirect;

use App\SubscriptionType;
use Illuminate\Http\Request;
use App\Http\Requests\SubscriptionTypeRequestForm as SubscriptionTypeRequestForm;
use App\Http\Requests\SubscriptionTypeRequestEditForm as SubscriptionTypeRequestEditForm;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        // Middleware
        $this->middleware('sentry.auth');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types = SubscriptionType::all();

        return view('settings.admin.subscriptions.index', compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('settings.admin.subscriptions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubscriptionTypeRequestForm $request)
    {
        $type = new SubscriptionType;

        $type->name           = $request->name;
        $type->amount           = $request->amount;
        $type->duration           = $request->duration;
        
        $type->save();

        Alert::success('New Subscription Type Registered!');

        return Redirect::to('admin/subscriptions');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $type = SubscriptionType::findorfail($id);

        return view('settings.admin.subscriptions.edit', compact('type'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SubscriptionTypeRequestEditForm $request, $id)
    {
        $type = SubscriptionType::findorfail($id);

        $type->name           = $request->name;
        $type->amount           = $request->amount;
        $type->duration           = $request->duration;

        $type->save();

        Alert::success('Subscription Type Edited Successfully!');

        return Redirect::to('admin/subscriptions');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $type = SubscriptionType::findorfail($id);

        if ($type->tenants->count() > 0) {
            Alert::error('Subscription Type has Tenants Already, Please remove the Tenants First', 'Error')->persistent('Close');

            return back();
        }

        $type->delete();

        Alert::success('Subscription Type Removed Successfully!');

        return Redirect::to('admin/subscriptions');
    }
}
