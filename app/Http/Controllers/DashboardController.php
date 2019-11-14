<?php

namespace App\Http\Controllers;

use Carbon;
use Sentinel;
use App\Loan;
use App\Client;
use App\Tenant;
use Illuminate\Http\Request;
// // use infobip\api\client\GetAccountBalance;
// use infobip\api\configuration\BasicAuthConfiguration;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Tenant Middleware
        $this->middleware('sentinel.auth', ['only' => ['tenant']]);

        // Admin Middleware
        $this->middleware('sentry.auth', ['only' => ['admin']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin()
    {
        // $username =  env('INFOBIP_USERNAME');
        // $password =  env('INFOBIP_PASSWORD');

        // Initializing GetAccountBalance client with appropriate configuration
        // $client = new GetAccountBalance(new BasicAuthConfiguration($username, $password));
        // Executing request
        // $response = $client->execute();

        // $amount = $response->getBalance();
        // $currency = $response->getCurrency();
        $tenants = Tenant::all()->count();
        $clients = Client::all()->count();
        $loans = Loan::all()->count();

        return view('dashboards.admin', compact('tenants', 'clients', 'loans'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tenant()
    {
        // Retrieving Tenant Details
        $to_pay_today = 0;
        $yesterday_penalts = 0;
        $tenant = Sentinel::getUser()->tenant;
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        foreach ($tenant->paymentSchedules as $schedule) {
            if (Carbon::parse($schedule->date)->toDateString() == $today) {
                $to_pay_today += 1;
            }
        }

        foreach ($tenant->penalts as $penalt) {
            if (Carbon::parse($penalt->date)->toDateString() == $yesterday) {
                $yesterday_penalts += 1;
            }
        }

        return view('dashboards.tenant', compact('tenant', 'to_pay_today', 'yesterday_penalts'));
    }   
}
