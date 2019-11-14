@extends('masters.tenant.app')

<!-- Page Title -->
@section('title')Dashboard @stop

<!-- Head Styles -->
@section('styles')
    <!-- Date Picker -->
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@stop

<!-- Page Header -->
@section('header')Dashboard @stop

<!-- Page Description -->
@section('desc')Loan Alert Dashboard @stop

<!-- Active Link -->
@section('active')Dashboard @stop

<!-- Page Content -->
@section('content')
    <!-- Small boxes Section -->
    <div class="row">
        <div class="col-lg-6 col-xs-12">
        <!-- small box -->
            <div class="small-box bg-teal">
                <div class="inner">
                    <h3>{{ $tenant->loans->count() }} Loans</h3>
                    <p>Loan Management</p>
                </div>
                <div class="icon">
                    <i class="ion ion-cube"></i>
                </div>
                <a href="{{ url('/loans') }}" class="small-box-footer">Manage Loans <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div><!-- ./col -->

        <div class="col-lg-6 col-xs-12">
        <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>{{ $tenant->clients->count() }} Clients</h3>
                    <p>Total Clients</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person"></i>
                </div>
                <a href="{{ url('clients')  }}" class="small-box-footer">Manage Clients <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div><!-- ./col -->
    </div><!-- /.row -->
    
    <!-- Second row -->
    <div class="row">
        <div class="col-lg-6 col-xs-12">
            <!-- About Me Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Today's Summary</h3>
                    <a href="{{ url('today/payments') }}" class="btn btn-warning btn-sm pull-right"><i class="fa fa-plus"></i> See Details</a>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <strong><i class="fa fa-square margin-r-5"></i>{{ $tenant->name }}</strong>
                    <a href="{{ url('today/payments') }}"><span class="label label-danger pull-right">{{ Carbon::today()->toFormattedDateString() }}</span></a>
                    <p class="text-muted"><b>Total Loans:</b> {{ $tenant->loans->count() }} {{ str_plural('Loan', $tenant->loans->count()) }}</p>
                    <p class="text-muted"><b>To Be Paid Today:</b> {{ $to_pay_today }} {{ str_plural('Loan', $to_pay_today) }}</p>
                    <p class="text-muted"><b>Loans with Yesterday's Penalt:</b> {{ $yesterday_penalts }} {{ str_plural('Loan', $yesterday_penalts) }}</p>
                </div><!-- /.box-body -->
            </div><!-- /.box -->   
        </div><!-- end of left col -->

        <div class="col-lg-6 col-xs-12">
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <p><strong>Whoops!</strong> There were some problems with your input.</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- General Report -->            
            <div class="box box-info">
                <div class="box-header">
                    <i class="fa fa-clone"></i>
                    <h3 class="box-title">Loan Alert Armotisation</h3>
                    <!-- tools box -->
                    <div class="pull-right box-tools">
                        <i class="fa fa-bolt"></i> Loan Projection
                    </div><!-- /. tools -->
                </div>
                <div class="box-body"> 
                    <form action="{{ url('armotisation') }}" method="POST" accept-charset="UTF-8">
                        <input name="_token" value="{{ csrf_token() }}" type="hidden">
                        <div class="box-body">
                            <div class="form-group">
                                <select name="type" class="form-control">
                                    <option disabled="" selected="">Select Loan type</option>
                                    @foreach($tenant->loanTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('type') == $type->id ? 'selected' : '' }}> {{ $type->name }} | {{ $type->duration }} {{ str_plural('Month', $type->duration) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <input name="amount" class="form-control" value="{{ old('amount') }}" placeholder="Loan Amount">
                            </div>
                        </div><!-- /.box-body -->
                        <div class="box-footer">
                            <div class="pull-right">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-calculator"></i> Calculate</button>
                            </div>
                        </div><!-- /.box-footer -->
                    </form>                    
                </div>
            </div>
        </div>
    </div>

        <!-- Third row -->
    <div class="row">
        <div class="col-lg-12 col-xs-12">
            <!-- Calendar -->
                <div class="box box-solid bg-green-gradient">
                    <div class="box-header">
                        <i class="fa fa-calendar"></i>
                        <h3 class="box-title">Calendar</h3>                    
                    </div><!-- /.box-header -->
                    <div class="box-body no-padding">
                        <!--The calendar -->
                        <div id="calendar" style="width: 100%"></div>
                    </div><!-- /.box-body -->                
                </div><!-- /.box -->
        </div><!-- End of right col -->
    </div>
@stop

<!-- Page Scripts -->
@section('scripts')
    <!-- datepicker -->
    <script src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script>
        $(function () {
            //The Calender
            $('#calendar').datepicker({
                todayHighlight: true
            });
        });
    </script>
@stop