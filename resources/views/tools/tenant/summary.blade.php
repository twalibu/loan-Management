@extends('masters.tenant.app')

<!-- Page Title -->
@section('title')Executive Summary @stop

<!-- Head Styles -->
@section('styles')
    <!-- Date Range Picker -->
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
@stop

<!-- Page Header -->
@section('header')Executive Summary @stop

<!-- Page Description -->
@section('desc')Executive Summary @stop

<!-- Active Link -->
@section('active')Executive Summary @stop

<!-- Page Content -->
@section('content')
    <!-- First Row -->
    <div class="row">
        <div class="col-lg-4 col-xs-12">
        <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>{{ $tenant->clients->count() }} {{ str_plural('Client', $tenant->clients->count()) }}</h3>
                    <p>Total Clients</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-stalker"></i>
                </div>
                <a href="{{ url('clients') }}" class="small-box-footer">Manage Clients <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div><!-- ./col -->

        <div class="col-lg-4 col-xs-12">
        <!-- small box -->
            <div class="small-box bg-teal">
                <div class="inner">
                    <h3>{{ $tenant->staff->count() }} {{ str_plural('Staff', $tenant->staff->count()) }}</h3>
                    <p>Total Students</p>
                </div>
                <div class="icon">
                    <i class="ion ion-ios-people"></i>
                </div>
                <a href="{{ url('staff') }}" class="small-box-footer">Manage Staff <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div><!-- ./col -->

        <div class="col-lg-4 col-xs-12">
        <!-- small box -->
            <div class="small-box bg-orange">
                <div class="inner">
                    <h3>{{ $tenant->loans->count() }} {{ str_plural('Loan', $tenant->loans->count()) }}</h3>
                    <p>Total Loans</p>
                </div>
                <div class="icon">
                    <i class="ion ion-cube"></i>
                </div>
                <a href="{{ url('loans')  }}" class="small-box-footer">Manage Loans <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div><!-- ./col -->
    </div><!-- /.row -->

    <!-- Second Row -->
    <div class="row">
        <div class="col-lg-6 col-xs-12">
            <!-- Total Loan Given -->
            <div class="info-box bg-maroon">
                <span class="info-box-icon"><i class="ion ion-cube"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Loan Amount</span>
                    <span class="info-box-number">Tsh {{ number_format($total_principal, 2) }}/-</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 100%"></div>
                    </div>
                    <span class="progress-description">
                        Total Amount Given as of today {{ Carbon::now()->toFormattedDateString() }}
                    </span>
                </div>
            </div>

            <!-- Total Payment Expected -->
            <div class="info-box bg-orange">
                <span class="info-box-icon"><i class="ion ion-ionic"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Payment Expected</span>
                    <span class="info-box-number">Tsh {{ number_format($total_expected, 2) }}/-</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 100%"></div>
                    </div>
                    <span class="progress-description">
                        Total Payment Expected as of today {{ Carbon::now()->toFormattedDateString() }}
                    </span>
                </div>
            </div>

            <!-- Total Paid -->
            <div class="info-box bg-teal">
                <span class="info-box-icon"><i class="ion ion-checkmark-circled"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Paid Amount </span>
                    <span class="info-box-number">Tsh {{ number_format($total_paid, 2) }}/-</span>
                    <div class="progress">
                        @if($total_expected == 0)
                            <div class="progress-bar" style="width: 0%"></div>
                        @else
                            <div class="progress-bar" style="width: {{ ($total_paid/$total_expected)*100 }}%"></div>
                        @endif
                    </div>
                    <span class="progress-description">
                        @if($total_expected == 0)
                            0% of Outstanding Payment Remains
                        @else
                            {{ number_format(($total_paid/$total_expected)*100) }}% of Outstanding Balance Paid
                        @endif
                    </span>
                </div>
            </div>

            <!-- Total Balance -->
            <div class="info-box bg-navy">
                <span class="info-box-icon"><i class="ion ion-alert-circled"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Balance Amount</span>
                    <span class="info-box-number">Tsh {{ number_format($total_expected - $total_paid, 2) }}/-</span>
                    <div class="progress">
                        @if($total_expected == 0)
                            <div class="progress-bar" style="width: 0%"></div>
                        @else
                            <div class="progress-bar" style="width: {{ (($total_expected - $total_paid)/$total_expected)*100 }}%"></div>
                        @endif
                    </div>
                    <span class="progress-description">
                        @if($total_expected == 0)
                            0% of Outstanding Payment Remains
                        @else
                            {{ number_format((($total_expected - $total_paid)/$total_expected)*100) }}% of Outstanding Payment Remains
                        @endif
                    </span>
                </div>
            </div>

            <!-- General Report -->
            <form action="{{ url('tools/report/generate') }}" method="POST" accept-charset="UTF-8">
                <div class="box box-info">
                    <div class="box-header">
                        <i class="fa fa-clone"></i>
                        <h3 class="box-title">Print Report</h3>
                        <!-- tools box -->
                        <div class="pull-right box-tools">
                            <i class="fa fa-bolt"></i> Report Generetor
                        </div><!-- /. tools -->
                    </div>
                    <div class="box-body">                    
                        <input name="_token" value="{{ csrf_token() }}" type="hidden">
                        <input type="hidden" name="report_start" id="report_start" value="">
                        <input type="hidden" name="report_end" id="report_end" value="">
                        <div class="form-group">
                            <div class="input-group">
                                <button type="button" class="btn btn-default pull-right" name="daterange" id="daterange">
                                    <span>
                                        <i class="fa fa-calendar"></i> Please Select Start and End Dates
                                    </span>
                                    <i class="fa fa-caret-down"></i>
                                </button>
                            </div>
                        </div>                   
                    </div>
                    <div class="box-footer clearfix">
                        <button class="pull-right btn btn-default" disabled="" type="submit">Generate Report <i class="fa fa-arrow-circle-right"></i></button>
                    </div>
                </div>
            </form>
            <div class="callout callout-success">
                <h4>{{ $tenant->name }}</h4>
                <p><strong>{{ config('app.name') }}</strong> | {{ config('app.version', 'Version 1.0') }}</p>
            </div>
        </div>

        <div class="col-lg-6 col-xs-12">
            <!-- About Me Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Summary Per Loan Type</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    @foreach($tenant->loanTypes as $type)
                        <!-- 
                            {{ $loan_amount = 0 }}
                            {{ $total_interest = 0 }}
                            {{ $total_paid = 0 }}
                            {{ $total_penalt = 0 }}
                            {{ $total_overwrite = 0 }}

                            @foreach($type->loans as $loan)
                                @if($loan->summary->status == 'Active')
                                    {{ $loan_amount += $loan->summary->principal }}
                                    {{ $total_interest += $loan->summary->interest }}
                                    {{ $total_paid += $loan->summary->paid }}
                                    {{ $total_penalt += $loan->summary->penalt }}
                                    {{ $total_overwrite += $loan->summary->overwrite }}
                                @endif
                            @endforeach 
                        -->

                        <u><strong><i class="fa fa-square margin-r-5"></i>{{ $type->name }}</strong></u>
                        <p class="text-muted"><b>Total Loans:</b> {{ number_format($type->loans->count()) }} {{ str_plural('Loan', $type->loans->count()) }} ~ <small><b>Duration: {{ number_format($type->duration) }} {{ str_plural('Month', $type->duration) }} | Interest Rate: {{ $type->interest * 100 }}%</small></p>
                        <p class="text-muted"><b>Total Loan Amount:</b> Tsh {{ number_format($loan_amount, 2) }}/-</p>
                        <p class="text-muted"><b>Total Payment Expected :</b> Tsh {{ number_format(($loan_amount + $total_interest + $total_penalt), 2) }}/-</p>
                        <p class="text-muted"><b>Total Paid Amount:</b> Tsh {{ number_format($total_paid, 2) }}/-</p>
                        <p class="text-muted"><b>Total Balance Amount:</b> Tsh {{ number_format(($loan_amount + $total_interest + $total_penalt) - ($total_paid + $total_overwrite), 2) }}/-</p>
                        @if($total_penalt > 0)
                            <p class="text-muted"><b>Total Penalt Amount:</b> Tsh {{ number_format($total_penalt, 2) }}/-</p>
                        @endif
                        @if($total_overwrite > 0)
                            <p class="text-muted"><b>Total Overwritten Amount:</b> Tsh {{ number_format($total_overwrite, 2) }}/-</p>
                        @endif
                        <hr>
                    @endforeach  
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
    </div><!-- /.row -->

    <!-- Third Row -->
    <div class="row">
        <div class="col-lg-12 col-xs-12">
            <div id="chart-div"></div>
            {!! $lava->render('PieChart', 'Loan Types', 'chart-div') !!}
        </div>
    </div><!-- /.row -->
    <br>
    <div class="row">
        <div class="col-lg-6 col-xs-12">
            <div class="small-box bg-maroon">
                    <div class="inner">
                        <h3>TZS {{ number_format($tenant->sms->balance,2) }}/-</h3>
                        <p>SMS Balance</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-social-usd"></i>
                    </div>
                    <a href="{{ url('communications') }}" class="small-box-footer">Communication Center <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        <div class="col-lg-6 col-xs-12">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ number_format($tenant->sms->balance/$tenant->sms->price) }} SMS Left</h3>
                    <p>SMS Balance (In Numbers)</p>
                </div>
                <div class="icon">
                    <i class="ion ion-email"></i>
                </div>
                <a href="{{ url('communications') }}" class="small-box-footer">Communication Centers <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
    
@stop

<!-- Page Scripts -->
@section('scripts')
    <!-- daterangepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
    <script src="{{ asset('bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script>
        $(function () {
            $('#daterange').daterangepicker(
                {
                    ranges: {
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                        'Last Three Month': [moment().subtract(3, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment()
                },
                function (start, end) {
                    $('#daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    $('#report_start').val(start.format('YYYY-MM-DD'));
                    $('#report_end').val(end.format('YYYY-MM-DD'));
                }
            );
        });
    </script>
@stop