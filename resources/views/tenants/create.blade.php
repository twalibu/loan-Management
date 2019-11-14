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
@section('header')Add Tenant @stop

<!-- Page Description -->
@section('desc')Create new tenant @stop

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
                <form action="{{ route('tenants.store') }}" method="POST" accept-charset="UTF-8">
                    <input name="_token" value="{{ csrf_token() }}" type="hidden">
                    
                    <div class="wizard-header">
                        <h3 class="wizard-title">
                            New Tenant Registration
                        </h3>
                        <h5>Please fill the information accurately.</h5>
                    </div>

                    <div class="wizard-navigation">
                        <ul>
                            <li><a href="#details" data-toggle="tab">Tenant Details</a></li>
                            <li><a href="#sms" data-toggle="tab">Tenant SMS</a></li>
                            <li><a href="#contact" data-toggle="tab">Tenant Contact Details</a></li>
                        </ul>
                    </div>
                    
                    <div class="tab-content">
                        <!-- Details -->
                        <div class="tab-pane" id="details">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 class="info-text"> Let's start with the basic details.</h4>
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">input</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Tenant Name</label>
                                            <input name="name" value="{{ old('name') }}" type="text" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">explore</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Tenant Slug</label>
                                            <input name="slug" value="{{ old('slug') }}" type="text" class="form-control">
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
                                                        @if($type->id == old('subscription'))
                                                            selected=""
                                                        @endif
                                                        >{{ $type->name }} - Tsh {{ number_format($type->amount) }} | {{ $type->duration }} {{ str_plural('Month', $type->duration) }} </option>
                                                @endforeach                                            
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">location_on</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Select Region</label>
                                            <select class="form-control" name="region">
                                                <option disabled="" selected=""></option>
                                                @foreach($regions as $region)
                                                    <option value="{{ $region->id }}"
                                                        @if($region->id == old('region'))
                                                            selected=""
                                                        @endif
                                                        >{{ $region->name }}</option>
                                                @endforeach                                            
                                            </select>
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">location_searching</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Address In Details (Optional)</label>
                                            <textarea name="address" class="form-control" rows="5">{{ old('address') }}</textarea>
                                        </div>
                                    </div>                                    
                                </div>
                            </div>
                        </div>

                        <!-- SMS -->
                        <div class="tab-pane" id="sms">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 class="info-text"> SMS System Details.</h4>
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">input</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Tenant Username</label>
                                            <input name="smsusername" value="{{ old('smsusername') }}" type="text" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">info</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Tenant Password</label>
                                            <input name="smspassword" type="password" class="form-control">
                                        </div>
                                    </div>

                                </div>

                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">label</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Tenant Sender Name</label>
                                            <input name="sender" value="{{ old('sender') }}" type="text" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">play_for_work</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Tenant SMS Price</label>
                                            <input name="price" value="{{ old('price') }}" type="number" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">blur_on</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Tenant SMS Balance</label>
                                            <input name="balance" value="{{ old('balance') }}" type="number" class="form-control">
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>

                        <!-- Contact -->
                        <div class="tab-pane" id="contact">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 class="info-text"> Tenant Contact Details.</h4>
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">account_box</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">First Name</label>
                                            <input name="first_name" value="{{ old('first_name') }}" type="text" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">account_circle</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Last Name</label>
                                            <input name="last_name" value="{{ old('last_name') }}" type="text" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">home</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Office Name</label>
                                            <input name="office" value="{{ old('office') }}" type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">extension</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Position</label>
                                            <input name="position" value="{{ old('position') }}" type="text" class="form-control">
                                        </div>
                                    </div>
                                    
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">phone</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Phone Number</label>
                                            <input name="phone_number" value="{{ old('phone_number') }}" type="number" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">email</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Email Address</label>
                                            <input name="email" value="{{ old('email') }}" type="email" class="form-control">
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