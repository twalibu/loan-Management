@extends('masters.tenant.app')

<!-- Page Title -->
@section('title')Staff @stop

<!-- Head Styles -->
@section('styles')
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />

    <!-- Material Wizard CSS Files -->
    <link href="{{ asset('bower_components/material_bootstrap_wizard/assets/css/material-bootstrap-wizard.css') }}" rel="stylesheet" />
@stop

<!-- Page Header -->
@section('header')Edit Staff @stop

<!-- Page Description -->
@section('desc')Edit Staff's Details @stop

<!-- Active Link -->
@section('active')Staff @stop

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
                <form action="{{ route('staff.update', array($staff->id)) }}" method="POST" accept-charset="UTF-8">
                    <input type="hidden" name="_method" value="PUT">
                    <input name="_token" value="{{ csrf_token() }}" type="hidden">
                    <input name="staff" value="{{ $staff->user->id }}" type="hidden">
                    
                    <div class="wizard-header">
                        <h3 class="wizard-title">
                            Edit Staff's Details
                        </h3>
                        <h5>Please fill the information accurately.</h5>
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
                                    <h4 class="info-text"> Let's start with the basic details.</h4>
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">person_pin</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">First Name</label>
                                            <input name="first_name" value="{{ $staff->first_name }}" type="text" class="form-control">
                                        </div>
                                    </div>
                                    
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">perm_identity</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Middle Name (Optional)</label>
                                            <input name="middle_name" value="{{ $staff->middle_name }}" type="text" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">person_pin_circle</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Last Name</label>
                                            <input name="last_name" value="{{ $staff->last_name }}" type="text" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">contact_phone</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Phone Number</label>
                                            <input name="phone_number" value="{{ $staff->phone_number }}" type="number" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">email</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Email</label>
                                            <input name="email" value="{{ $staff->user->email }}" type="email" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">home</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Office</label>
                                            <select class="form-control" name="office">
                                                <option disabled="" selected=""></option>
                                                @foreach($tenant->offices as $office)
                                                    <option value="{{ $office->id }}" {{ $staff->office->id == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">all_out</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Staff Type</label>
                                            <select class="form-control" name="type">
                                                <option disabled="" selected=""></option>
                                                @foreach($tenant->types as $type)
                                                    <option value="{{ $type->id }}" {{ $staff->type->id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">lock</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Password <small>(Fill if you want to Change, else Leave Blank)</small></label>
                                            <input name="password" type="password" class="form-control">
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">lock_outline</i>
                                        </span>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Confirm Password <small>(Confirm if you have fill Password Above)</small></label>
                                            <input name="password_confirmation" type="password" class="form-control">
                                        </div>
                                    </div>
                                
                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        <br><strong>Please Select User Role(s) Below</strong>
                                        @foreach($tenant->roles as $role)
                                            <div class="form-group label-floating checkbox">
                                                <label>
                                                    <input class="control-label" type="checkbox" name="roles[{{ $role->slug }}]" value="{{ $role->id }}" {{ $staff->user->inRole($role) ? 'checked' : '' }}>
                                                    {{ $role->name }}
                                                </label>
                                            </div>
                                        @endforeach
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