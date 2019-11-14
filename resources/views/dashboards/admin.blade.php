@extends('masters.admin.app')

<!-- Page Title -->
@section('title')Super Admin @stop

<!-- Head Styles -->
@section('styles')
    <!-- Date Picker -->
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
@stop

<!-- Page Header -->
@section('header')Dashboard @stop

<!-- Page Description -->
@section('desc')Super Admin Dashboard @stop

<!-- Active Link -->
@section('active')Dashboard @stop

<!-- Page Content -->
@section('content')
    <!-- Small boxes Section -->
    <div class="row">
        <div class="col-lg-4 col-xs-12">
        <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{ $tenants }} Tenants</h3>
                    <p>Tenants Management</p>
                </div>
                <div class="icon">
                    <i class="ion ion-ios-albums"></i>
                </div>
                <a href="{{ url('admin/tenants') }}" class="small-box-footer">Tenants Info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div><!-- ./col -->

        <div class="col-lg-4 col-xs-12">
        <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>{{ $clients }} Clients</h3>
                    <p>Total Clients</p>
                </div>
                <div class="icon">
                    <i class="ion ion-ios-people"></i>
                </div>
                <a href="{{ url('admin/tenants')  }}" class="small-box-footer">Tenants Info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div><!-- ./col -->

        <div class="col-lg-4 col-xs-12">
        <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>{{ $loans }} Loans</h3>
                    <p>Total Loans</p>
                </div>
                <div class="icon">
                    <i class="ion ion-ionic"></i>
                </div>
                <a href="{{ url('admin/tenants') }}" class="small-box-footer">Tenants Info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div><!-- ./col -->
    </div><!-- /.row -->

    <!-- Second row -->
    <div class="row">
        <div class="col-lg-5 col-xs-12">
        <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
                    <h3> Tsh</h3>
                    <p>SMS Balance</p>
                </div>
                <div class="icon">
                    <i class="ion ion-social-usd"></i>
                </div>
                <a href="{{ url('admin/dashboard') }}" class="small-box-footer">Courtesy of Infobip <i class="fa fa-arrow-circle-right"></i></a>
            </div>

            <div class="small-box bg-orange">
                <div class="inner">
                    <h3> SMS Left</h3>
                    <p>SMS Balance (In Numbers)</p>
                </div>
                <div class="icon">
                    <i class="ion ion-ios-email"></i>
                </div>
                <a href="{{ url('admin/dashboard') }}" class="small-box-footer">Courtesy of Infobip <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div><!-- ./col -->

        <div class="col-lg-7 col-xs-12">
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
    </div><!-- End of second row -->
@stop

<!-- Page Scripts -->
@section('scripts')
    <!-- daterangepicker -->
    <script src="{{ asset('bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <!-- datepicker -->
    <script src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
    $(function () {
        //The Calender
        $('#calendar').datepicker();
    });
</script>
@stop