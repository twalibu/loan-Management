<?php

namespace App\Http\Controllers;

use App\LoanType;
use App\LoanSummary;
use Illuminate\Http\Request;

use Lava;
use Sentinel;
use Khill\Lavacharts\Lavacharts;

class SummaryController extends Controller
{
    public function __construct()
    {
        // Middleware
        $this->middleware('sentinel.auth');
        $this->middleware('sentinel.access:summary.access');
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

        $active = LoanSummary::where([['tenant_id', $tenant->id],['status', '<>','Completely Paid']])->get();
        $completed = LoanSummary::where([['tenant_id', $tenant->id],['status', 'Completely Paid']])->get();
        $total_loans = LoanSummary::where('tenant_id', $tenant->id)->count();
        
        $active_loans = $active->count();
        $completed_loans = $completed->count();
        $total_amount = $active->sum('principal');
        $total_interest = $active->sum('interest');
        $total_penalt = $active->sum('penalt');
        $total_paid = $active->sum('paid');

        // Charts
        $lava = new Lavacharts; // See note below for Laravel

        $reasons = Lava::DataTable();

        $types = LoanType::where('tenant_id', $tenant->id)->get();

        $name = array();
        $count = array();

        foreach ($types as $type) {
            $name = array_prepend($name, $type->name);
            $count = array_prepend($count, $type->loans->count());
        }

        $collection = collect(['name', 'count']);
        $combined = $collection->combine([$name, $count]);
        $combined->all();

        $items = $types->count();

        $reasons->addStringColumn('Loan Types')
                ->addNumberColumn('Percent');

                for ($i=0; $i < $items; $i++) {
                    $reasons->addRow([$combined['name'][$i], $combined['count'][$i]]);
                }

        $lava->PieChart('Loans', $reasons, [
            'title' => 'Loans Types Taken'
        ]);

        return view('summary.index', compact('tenant', 'total_loans', 'active_loans', 'completed_loans', 'total_amount', 'total_interest', 'total_penalt', 'total_paid', 'lava'));
    }
}
