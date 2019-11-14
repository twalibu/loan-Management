@extends('masters.tenant.app')

<!-- Page Title -->
@section('title')Armotisation @stop

<!-- Head Styles -->
@section('styles')
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />

    <!-- Material Wizard CSS Files -->
    <link href="{{ asset('bower_components/material_bootstrap_wizard/assets/css/material-bootstrap-wizard.css') }}" rel="stylesheet" />
@stop

<!-- Page Header -->
@section('header')Armotisation @stop

<!-- Page Description -->
@section('desc')Loan Alert Armotisation @stop

<!-- Active Link -->
@section('active')Armotisation @stop

<!-- Page Content -->
@section('content')
<div class="row">
	<div class="col-md-4">
        <!-- Widget: user widget style 1 -->
        <div class="box box-widget widget-user-2">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-yellow">
              	<div class="widget-user-image">
                	<img class="img-circle" src="{{ Gravatar::src(Sentinel::getUser()->id) }}" alt="User Avatar">
              	</div>
              	<!-- /.widget-user-image -->
              	<h3 class="widget-user-username">{{ $type->name }}</h3>
              	<h5 class="widget-user-desc">Tsh {{ number_format($pv,2) }}/-</h5>
            </div>
            <div class="box-footer no-padding">
              	<ul class="nav nav-stacked">
                	<li><a href="#">Total Repayment <span class="pull-right badge bg-blue">Tsh {{ number_format($total_repayment,2) }}/-</span></a></li>
                	<li><a href="#">Total Principal <span class="pull-right badge bg-aqua">Tsh {{ number_format($total_principal,2) }}/-</span></a></li>
                	<li><a href="#">Total Interest <span class="pull-right badge bg-green">Tsh {{ number_format($total_interest,2) }}/-</span></a></li>
                	<li><a href="#">Monthly Payment <span class="pull-right badge bg-red">Tsh {{ number_format($monthly,2) }}/-</span></a></li>
                	<li><a href="#">Loan Insurance <span class="pull-right badge bg-orange">Tsh {{ number_format($insurance,2) }}/-</span></a></li>
                    <li><a class="btn btn-danger" href="{{ url('dashboard') }}">Calculate Another Armotisation</a></li>
              	</ul>
            </div>
        </div>
    </div>
    <div class="col-md-8">
    	<div class="callout callout-info">
            <h4>Loan Duration: {{ $type->duration }} {{ str_plural('Month', $type->duration) }}</h4>
    		<p>Loan Payment is Monthly Based. Payment Schedule is Below...</p>
        </div>
		<div class="box">
            <div class="box-header">
              	<h3 class="box-title">Payment Schedule Details</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
              	<table class="table table-striped">
                	<tr>
                  		<th>Month</th>
                        <th>Payment</th>
                        <th>Interest</th>
                        <th>Principal</th>
                	</tr>
                	@foreach($schedules as $schedule)
                        <tr>
                            <td>Month {{ $schedule['month'] }}</td>
                            <td>Tsh {{ number_format($schedule['payment'], 2) }}</td>
                            <td>Tsh {{ number_format($schedule['interest'], 2) }}</td>
                            <td>Tsh {{ number_format($schedule['principal'], 2) }}</td>
                        </tr>
                    @endforeach
              	</table>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>

<div class="row">
	<div class="col-md-12"> 
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
        <!--      Wizard container        -->   
        <div class="wizard-container">
            <div class="card wizard-card" data-color="blue" id="wizard">
                <form action="{{ url('projection') }}" method="POST" accept-charset="UTF-8">
                    <input name="_token" value="{{ csrf_token() }}" type="hidden">
                    <input name="type" value="{{ $type->id }}" type="hidden">
                    <input name="amount" value="{{ $pv }}" type="hidden">
                    
                    <div class="wizard-header">
                        <h3 class="wizard-title">
                            Loan Projection
                        </h3>
                        <h5>To Send this Projection to Client's Number, Please fill the details below</h5>
                    </div>

                    <div class="wizard-navigation">
                        <ul>
                            <li><a href="#details" data-toggle="tab">Details</a></li>
                        </ul>
                    </div>
                    
                    <div class="tab-content">
                        <!-- Details -->
                        <div class="tab-pane" id="details">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 class="info-text"> Client's Information</h4>
                                </div>
                                <div class="col-sm-8 col-md-offset-2">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">perm_identity</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Full Name</label>
                                            <input name="full_name" value="{{ old('full_name') }}" type="text" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">phone</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Phone Number</label>
                                            <input name="phone_number" value="{{ old('phone_number') }}" type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                     
                    </div>

                    <div class="wizard-footer">
                        <div class="pull-right">
                            <input type='button' class='btn btn-next btn-fill btn-danger btn-wd' name='next' value='Next' />
                            <input type='submit' class='btn btn-finish btn-fill btn-danger btn-wd' name='finish' value='Send Projection' />
                        </div>
                        <div class="pull-left">
                            <input type='button' class='btn btn-previous btn-fill btn-default btn-wd' name='previous' value='Previous' />
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </form>
            </div>
        </div> <!-- wizard container --> 
    </div>        
</div>
@stop

<!-- Page Scripts -->
@section('scripts')
    <!--   Material Wizard JS Files   -->
    <script src="{{ asset('bower_components/material_bootstrap_wizard/assets/js/jquery.bootstrap.js') }}" type="text/javascript"></script>
    <!--  Plugin for the Wizard -->
    <script src="{{ asset('bower_components/material_bootstrap_wizard/assets/js/material-bootstrap-wizard.js') }}"></script>
@stop