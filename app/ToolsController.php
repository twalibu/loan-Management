<?php

namespace App\Http\Controllers;

use DB;
use Lava;
use Excel;
use Alert;
use Carbon;
use Sentinel;
use Redirect;

use App\LoanType;
use App\SMSReport;
use App\TenantSMS;
use App\LoanSummary;
use Illuminate\Http\Request;
use Khill\Lavacharts\Lavacharts;
use App\Http\Requests\ReportGenerateRequest as ReportGenerateRequest;

class ToolsController extends Controller
{
    public function __construct()
    {
        // Middleware
        $this->middleware('sentinel.access:tools.access');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function smsReports()
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $sms = TenantSMS::where('tenant_id', $tenant)->first();
        $reports = SMSReport::where('tenant_id', $tenant)->orderBy('created_at', 'DESC')->get();

        return view('tools.tenant.smsReport', compact('reports', 'sms'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function summary()
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant;

        // Fee Summary
        $total_principal = LoanSummary::where([['tenant_id', $tenant->id],['status', 'Active']])->sum('principal');
        $total_overwrite = LoanSummary::where([['tenant_id', $tenant->id],['status', 'Active']])->sum('overwrite');
        $total_interest = LoanSummary::where([['tenant_id', $tenant->id],['status', 'Active']])->sum('interest');
        $total_penalt = LoanSummary::where([['tenant_id', $tenant->id],['status', 'Active']])->sum('penalt');
        $total_paid = LoanSummary::where([['tenant_id', $tenant->id],['status', 'Active']])->sum('paid');

        $total_expected = ($total_principal + $total_interest + $total_penalt);

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
                ->addNumberColumn('Loans');

                for ($i=0; $i < $items; $i++) {
                    $reasons->addRow([$combined['name'][$i], $combined['count'][$i]]);
                }

        $lava->PieChart('Loan Types', $reasons, [
            'title' => 'Loan Types Distribution'
        ]);

        return view('tools/tenant/summary', compact('lava', 'tenant', 'total_principal', 'total_paid', 'total_expected'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateReport(ReportGenerateRequest $request)
    {
        $reports = DB::table('payments')
                        ->where('payments.tenant_id', Sentinel::getUser()->tenant->id)
                        ->whereBetween('payments.date', [$request->report_start, $request->report_end])
                        ->join('payment_summaries', 'payments.student_id', '=', 'payment_summaries.student_id')
                        ->join('payment_methods', 'payments.method_id', '=', 'payment_methods.id')
                        ->join('students', 'payments.student_id', '=', 'students.id')
                        ->join('levels', 'payments.level_id', '=', 'levels.id')
                        ->select('payments.date', 
                                    'payments.amount',
                                    'payment_methods.account_number',
                                    'students.first_name',
                                    'students.last_name',
                                    'levels.name',
                                    'payment_summaries.paid'
                                )->get();

        $reports = $reports->toArray();
        $data= json_decode( json_encode($reports), true);
        
        $name = 'Report: '.$request->report_start . ' - ' . $request->report_end;
        $tenant = Sentinel::getUser()->tenant->name;
   
        Excel::create($name, function($excel) use($data, $name) {

            // Set the title
            $excel->setTitle($name);

            $excel->sheet('School Fees', function($sheet) use($data) {
                $sheet->fromArray($data);
                $sheet->row(1, function ($row) {
                    $row->setFontFamily('Comic Sans MS');
                    $row->setFontSize(14);
                });            });
        })->download('xlsx');

        Alert::sucess('Report Generated Successfully')->autoclose(3000);
        return back();
    }
}
