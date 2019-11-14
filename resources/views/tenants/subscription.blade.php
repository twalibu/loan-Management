@extends('masters.admin.app')

<!-- Page Title -->
@section('title')Tenants @stop

<!-- Head Styles -->
@section('styles')
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />

    <!-- Material Wizard CSS Files -->
    <link href="{{ asset('bower_components/material_bootstrap_wizard/assets/css/material-bootstrap-wizard.css') }}" rel="stylesheet" />
@stop

<!-- Page Header -->
@section('header')Tenant Subscription @stop

<!-- Page Description -->
@section('desc')Update Tenant Subscription @stop

<!-- Active Link -->
@section('active')Tenants @stop

<!-- Page Content -->
@section('content')
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
                <form action="{{ route('tenant.subscription.update', array($subscription->id)) }}" method="POST" accept-charset="UTF-8">
                    <input name="_token" value="{{ csrf_token() }}" type="hidden">
                    
                    <div class="wizard-header">
                        <h3 class="wizard-title">
                            Tenant Subscription
                        </h3>
                        <h5>Please fill the information accurately.</h5>
                    </div>

                    <div class="wizard-navigation">
                        <ul>
                            <li><a href="#admin" data-toggle="tab">Details</a></li>
                        </ul>
                    </div>
                    
                    <div class="tab-content">
                        <!-- Admin -->
                        <div class="tab-pane" id="admin">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 class="info-text"> Tenant Subscription Details</h4>
                                </div>
                                <div class="col-sm-4">
                                    <b>Current Subscription:</b><br>
                                    * {{ $subscription->type->name }} - Tsh {{ number_format($subscription->type->amount) }}
                                    <hr>
                                    <b>Starting Date:</b><br>
                                    * {{ Carbon::parse($subscription->start_date)->toFormattedDateString() }}
                                    <hr>
                                    <b>Expiration Date:</b><br>
                                    * {{ Carbon::parse($subscription->end_date)->toFormattedDateString() }}
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">event</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Starting Date</label>
                                            <input name="date" value="{{ old('date') }}" type="date" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">donut_small</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Select Subscription Type</label>
                                            <select class="form-control" name="subscription">
                                                <option disabled="" selected=""></option>
                                                @foreach($types as $type)
                                                    <option value="{{ $type->id }}"
                                                        @if($type->id == $subscription->subscription_id)
                                                            selected=""
                                                        @endif
                                                        >{{ $type->name }} - Tsh {{ number_format($type->amount) }} | {{ $type->duration }} {{ str_plural('Month', $type->duration) }} </option>
                                                @endforeach                                            
                                            </select>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>                  
                    </div>

                    <div class="wizard-footer">
                        <div class="pull-right">
                            <input type='button' class='btn btn-next btn-fill btn-danger btn-wd' name='next' value='Next' />
                            <input type='submit' class='btn btn-finish btn-fill btn-danger btn-wd' name='finish' value='Finish' />
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