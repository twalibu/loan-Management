@extends('masters.admin.app')

<!-- Page Title -->
@section('title')Communications @stop

<!-- Head Styles -->
@section('styles')
  	<!-- Select2 -->
  	<link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
@stop

<!-- Page Header -->
@section('header')Communications @stop

<!-- Page Description -->
@section('desc')Communications Dashboard @stop

<!-- Active Link -->
@section('active')Communications @stop

<!-- Page Content -->
@section('content')
<div class="row">
	<!-- Left Col -->
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
	<div class="col-md-3">              
		<div class="box box-primary">
			<div class="box-header with-border">
		  		<h3 class="box-title">Account Summary</h3>
			</div>
			<div class="box-body no-padding">
		  		<ul class="nav nav-pills nav-stacked" id="sms-counter">
		    		<li><a href="#"><i class="fa fa-inbox"></i> Account Balance <span class="label label-primary pull-right">{{ $currency }} {{ number_format($amount,2) }} </span></a></li>
		    		<li><a href="#"><i class="fa fa-envelope"></i> Total Recepients <span class="label label-warning pull-right">0</span></a></li>
		    		<li><a href="#"><i class="fa fa-commenting"></i> SMS Length <span class="label label-warning pull-right length"></span></a></li>
		    		<li><a href="#"><i class="fa fa-sort-amount-desc"></i> Messages <span class="label label-warning pull-right messages"></span></a></li>
		    		<li><a href="#"><i class="fa fa-sliders"></i> Per Message <span class="label label-warning pull-right per_message"></span></a></li>
		    		<li><a href="#"><i class="fa fa-spinner"></i> Remaining Characters <span class="label label-warning pull-right remaining"></span></a></li>
		  		</ul>
			</div><!-- /.box-body -->
		</div><!-- /. box -->
	</div><!-- /.col -->
	<!-- Right Col -->
	<div class="col-md-9">
		<div class="box box-primary">
			<div class="box-header with-border">
		  		<h3 class="box-title">Composer SMS</h3>
			</div><!-- /.box-header -->
			<form action="{{ url('admin/send') }}" method="POST" accept-charset="UTF-8">
                <input name="_token" value="{{ csrf_token() }}" type="hidden">
				<div class="box-body">
			  		<div class="form-group">
			    		<select name="receiver[]" class="form-control select2" multiple="multiple" data-placeholder="Select Tenant Contact's">
			    			@if($tenants->count() > 0)
			    				<option value="all">Select All</option>
			    			@endif
			    			@foreach($tenants as $tenant)
	                  			@foreach($tenant->contacts as $contact)
	                  				<option value="{{ $contact->phone_number }}">{{ $contact->first_name }} {{ $contact->last_name }} | {{ $contact->position }} ~ {{ $contact->tenant->name }}</option>
	                  			@endforeach
	                  		@endforeach
			    		</select>
			  		</div>
			  		<div class="form-group">
			    		<input class="form-control" value="Sender: {{ $sender }}" disabled="true">
			  		</div>
			  		<div class="form-group">
			    		<textarea name="message" id="message" class="form-control" placeholder="Enter Message Here..." style="height: 200px"></textarea>
			  		</div>		  		
				</div><!-- /.box-body -->
				<div class="box-footer">
			  		<div class="pull-right">
			    		<button type="submit" class="btn btn-primary"><i class="fa fa-envelope-o"></i> Send</button>
			  		</div>
				</div><!-- /.box-footer -->
			</form>
		</div><!-- /. box -->
	</div><!-- /.col -->
</div>
@stop

<!-- Page Scripts -->
@section('scripts')
	<!-- Select2 -->
    <script src="{{ asset('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2();

            // SMS Counter
        	$('#message').countSms('#sms-counter');
        })
    </script>
	<script src="{{ asset('bower_components/sms-counter/sms_counter.min.js') }}"></script>

@stop