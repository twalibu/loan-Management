<?php

namespace App\Http\Controllers;

use DB;
use Lava;
use Excel;
use Carbon;
use Sentinel;

use App\Client;
use App\Policy;
use App\InsuaranceType;
use Illuminate\Http\Request;
use Khill\Lavacharts\Lavacharts;
use App\Http\Requests\ReportGenerateRequest as ReportGenerateRequest;

class ReportController extends Controller
{
    public function __construct()
    {
        // Middleware
        $this->middleware('sentinel.auth');
        $this->middleware('sentinel.access:reports.access');
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
        $month = Carbon::now()->month;
        $monthName = Carbon::now()->format('F');
        $currentDate = Carbon::today()->toDateString();        

        /* Get Monthly Income */
        $total_premium = Policy::where('tenant_id', $tenant)
                            ->whereMonth('registration_date', $month)
                            ->sum('premium');

        $total_vat = Policy::where('tenant_id', $tenant)
                            ->whereMonth('registration_date', $month)
                            ->sum('vat');

        $total_income = Policy::where('tenant_id', $tenant)
                            ->whereMonth('registration_date', $month)
                            ->sum('total');

        $total_clients = Client::where('tenant_id', $tenant)->count();
        $new_clients = Client::where('tenant_id', $tenant)
                            ->whereMonth('created_at', $month)
                            ->count();

        $total_policies = Policy::where('tenant_id', $tenant)->count();
        $new_policies = Policy::where('tenant_id', $tenant)
                            ->whereMonth('registration_date', $month)
                            ->count();

        $policies = Policy::where('tenant_id', $tenant)->get();
        $total_notifications = 0;

        foreach ($policies as $policy) {
            if ($policy->alert->expiration_date == $currentDate || $policy->alert->alert_one == $currentDate || $policy->alert->alert_two == $currentDate) {
                $total_notifications += 1;
            }
        }

        // Charts
        $lava = new Lavacharts; 

        $reasons = Lava::DataTable();

        $types = InsuaranceType::where('tenant_id', $tenant)->get();

        $name = array();
        $count = array();

        foreach ($types as $type) {
            $name = array_prepend($name, $type->name);
            $count = array_prepend($count, $type->policies->count());
        }

        $collection = collect(['name', 'count']);
        $combined = $collection->combine([$name, $count]);
        $combined->all();

        $items = $types->count();

        $reasons->addStringColumn('Insuarance Types')
                ->addNumberColumn('Percent');

                for ($i=0; $i < $items; $i++) {
                    $reasons->addRow([$combined['name'][$i], $combined['count'][$i]]);
                }

        $lava->PieChart('LoanTypes', $reasons, [
            'title' => 'Insuarance Types Taken'
        ]);

        return view('reports.index', compact('lava', 'new_clients', 'new_policies', 'total_clients', 'total_policies', 'total_notifications', 'total_premium', 'total_vat', 'total_income', 'monthName'));
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generate(ReportGenerateRequest $request)
    {
        $reports = DB::table('policies')
                        ->where('policies.tenant_id', Sentinel::getUser()->tenant->id)
                        ->whereBetween('policies.registration_date', [$request->report_start, $request->report_end])
                        ->join('clients', 'policies.client_id', '=', 'clients.id')
                        ->join('policy_alerts', 'policies.id', '=', 'policy_alerts.policy_id')
                        ->join('insuarance_types', 'policies.type_id', '=', 'insuarance_types.id')
                        ->select('policies.registration_date', 
                                    'policy_alerts.expiration_date',
                                    'clients.first_name',
                                    'clients.last_name',
                                    'insuarance_types.name',
                                    'policies.addition_detail',
                                    'policies.cover_number',
                                    'policies.sticker_number',
                                    'policies.total',
                                    'policies.premium',
                                    'policies.vat',
                                    'policies.receipt_number'
                                )->get();

        $reports = $reports->toArray();
        $data= json_decode( json_encode($reports), true);
        
        $time = Carbon::now()->toDateString();
        $name = 'Report: '.$request->report_start . ' - ' . $request->report_end;
        $tenant = Sentinel::getUser()->tenant->name;
   
        Excel::create($name, function($excel) use($data, $time, $name) {

            // Set the title
            $excel->setTitle($name);

            $excel->sheet($time, function($sheet) use($data) {
                $sheet->fromArray($data);
                $sheet->row(1, function ($row) {
                    $row->setFontFamily('Comic Sans MS');
                    $row->setFontSize(14);
                });

                $sheet->setColumnFormat(array(
                    'I' => '#,##0',
                    'J' => '#,##0',
                    'K' => '#,##0',
                ));
            });
        })->download('xlsx');

        Alert::sucess('Report Generated Successfully')->autoclose(3000);
        return back();
    }
}
