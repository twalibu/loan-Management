<?php

namespace App\Http\Controllers;

use Carbon;
use App\TenantSMS;
use App\TenantSubscription;
use Illuminate\Http\Request;

class AdminToolsController extends Controller
{
    public function __construct()
    {
        // Admin Middleware
        $this->middleware('sentry.auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sms()
    {
        $sms = TenantSMS::where('balance', '<', 10000)->get();

        return view('tools.admin.sms', compact('sms'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function subscription()
    {
    	$currentDate = Carbon::today()->addWeeks(3)->toDateString();

        $subscriptions = TenantSubscription::where('end_date', '<', $currentDate)->get();

        return view('tools.admin.subscriptions', compact('subscriptions'));
    }


}
