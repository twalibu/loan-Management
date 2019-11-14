<?php

namespace App\Http\Controllers;

use Alert;
use Carbon;
use App\SubscriptionType;
use App\SubscriptionAlert;
use App\TenantSubscription;
use Illuminate\Http\Request;
use App\Http\Requests\TenantSubscriptionUpdateFormRequest as TenantSubscriptionUpdateFormRequest;

class TenantSubscriptionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // You must have admin access to proceed
        $this->middleware('sentry.auth');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $types = SubscriptionType::all();
        $subscription = TenantSubscription::findorfail($id);

        return view('tenants.subscription', compact('types', 'subscription'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TenantSubscriptionUpdateFormRequest $request, $id)
    {
        /* Compute & Save Subscription & Alert */
        $type = SubscriptionType::findorfail($request->subscription);
        $start_date = $request->date;
        $end_date = Carbon::parse($start_date)->addMonths($type->duration);

        $subscription = TenantSubscription::findorfail($id);

        $subscription->subscription_id      = $type->id;
        $subscription->start_date           = $start_date;
        $subscription->end_date             = $end_date;

        $subscription->save();

        $alert = SubscriptionAlert::where('subscription_id', $subscription->id)->first();

        $alert->alert_one           = Carbon::parse($subscription->end_date)->subDays(21);
        $alert->alert_two           = Carbon::parse($subscription->end_date)->subDays(14);
        $alert->alert_three         = Carbon::parse($subscription->end_date)->subDays(7);

        $alert->save();

        Alert::success('Tenant Subscription Updated Successfully!');

        return redirect()->action(
            'TenantController@show', ['id' => $subscription->tenant->id]
        );
    }
}
