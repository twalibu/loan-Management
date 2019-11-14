@extends('masters.admin.app')

<!-- Page Title -->
@section('title')Tenants @stop

<!-- Head Styles -->
@section('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@stop

<!-- Page Header -->
@section('header')Tenants @stop

<!-- Page Description -->
@section('desc')Tenants Details @stop

<!-- Active Link -->
@section('active')Tenants @stop

<!-- Page Content -->
@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="box box-info">
        	<div class="box-body">
        		<a class="btn btn-app" href="{{ route('tenant.subscription', array($tenant->subscription->id)) }}">
	                <i class="fa fa-barcode"></i> Update Subscription Plan
	            </a>
              	
              	<a class="btn btn-app" href="{{ route('tenant.admin', array($tenant->id)) }}">
              		<span class="badge bg-yellow">{{ $tenant->users->count() }}</span>
                	<i class="fa fa-user-plus"></i> Add Tenant System Administrator
              	</a>
              
              	<a class="btn btn-app" href="{{ route('tenant.contact', array($tenant->id)) }}">
              		<span class="badge bg-teal">{{ $tenant->contacts->count() }}</span>
                	<i class="fa fa-cubes"></i> Add Tenant Contact Person
              	</a>

              	<a class="btn btn-app">
              		<span class="badge bg-purple">{{ $tenant->clients->count() }}</span>
                	<i class="fa fa-check-square"></i> Tenant Total Clients
              	</a>

              	<a class="btn btn-app">
              		<span class="badge bg-green">{{ $tenant->loans->count() }}</span>
                	<i class="fa fa-check-circle"></i> Tenant Total Loans
              	</a>
              	
              	<a class="btn btn-app" href="{{ route('tenants.edit', array($tenant->id)) }}">
                	<i class="fa fa-edit"></i> Edit Tenant Details
              	</a>

              	<a id="deleteTenant" class="btn btn-app">
                	<i class="fa fa-remove"></i> Remove Tenant
              	</a>
            	<!-- /.box-body -->
          	</div>
    	</div>
	</div>
</div>
<div class="row">
	<div class="col-md-3">

	  	<!-- About Tenant Box -->
		<div class="box box-primary">
		    <div class="box-body">
		    	<img class="profile-user-img img-responsive img-circle" src="{{ Gravatar::src($tenant->name) }}" alt="User profile picture">
	      		<h3 class="profile-username text-center">{{ $tenant->name }}</h3>
	      		<p class="text-muted text-center">{{ $tenant->subscription->type->name }}</p><hr>
		      	<strong><i class="fa fa-gg margin-r-5"></i>Subscription</strong>
		      		<p class="text-muted">{{ $tenant->subscription->type->name }}</p>
		      	<hr>
		      	<strong><i class="fa fa-spinner margin-r-5"></i>Subscription Details</strong>
		      		<p class="text-muted">Amount: <b>Tsh {{ number_format($tenant->subscription->type->amount) }}/-</b><br>
		      		Duration: <b>{{ $tenant->subscription->type->duration }} {{ str_plural('Month', $tenant->subscription->type->duration) }}</b></p>
		      	<hr>
		      	<strong><i class="fa fa-hourglass-start margin-r-5"></i>Start of Subscription</strong>
		      		<p class="text-muted">{{ Carbon::parse($tenant->subscription->start_date)->toFormattedDateString() }}</p>
		      	<hr>
		      	<strong><i class="fa fa-hourglass-end margin-r-5"></i>End of Subscription</strong>
		      		<p class="text-muted">{{ Carbon::parse($tenant->subscription->end_date)->toFormattedDateString() }}</p>
		    </div><!-- /.box-body -->
		</div><!-- /.box -->

		<!-- About Tenant SMS Box -->
		<div class="box box-warning">
		    <div class="box-header with-border">
		      	<h3 class="box-title">Tenant SMS Summary</h3>
		    </div><!-- /.box-header -->
		    <div class="box-body">
		      	<strong><i class="fa fa-square margin-r-5"></i>Sender Name</strong>
		      		<p class="text-muted">{{ $tenant->sms->sender_name }}</p>
		      	<hr>
		      	<strong><i class="fa fa-money margin-r-5"></i>SMS Balance</strong>
		      		<p class="text-muted">TZS {{ number_format($tenant->sms->balance,2) }}/-</p>
		      	<hr>
		      	<strong><i class="fa fa-tags margin-r-5"></i>Cost per SMS</strong>
		      		<p class="text-muted">TZS {{ number_format($tenant->sms->price,2) }}/-</p>
		      	<hr>
		      	<strong><i class="fa fa-envelope margin-r-5"></i>SMS Count</strong>
		      		<p class="text-muted">{{ number_format($tenant->sms->balance/$tenant->sms->price) }} SMS Left</p>
		      	<hr>
		      	<a class="btn btn-warning btn-block" href="{{ route('tenant.balance.topup', array($tenant->id)) }}">
                	Balance Top-Up
              	</a>
		    </div><!-- /.box-body -->
		</div><!-- /.box -->

		<!-- About Tenant Address -->
		<div class="box box-success">
		    <div class="box-header with-border">
		      	<h3 class="box-title">Tenant Address</h3>
		    </div><!-- /.box-header -->
		    <div class="box-body">
		      	<strong><i class="fa fa-location-arrow margin-r-5"></i>Region</strong>
		      		<p class="text-muted">{{ $tenant->address->region->name }}</p>
		      	@if($tenant->address->address)
			      	<hr>
			      	<strong><i class="fa fa-map margin-r-5"></i>Address Details</strong>
			      		<p class="text-muted">{{ $tenant->address->address }}</p>
			    @endif
		    </div><!-- /.box-body -->
		</div><!-- /.box <--></-->
	</div><!-- /.col -->

	<div class="col-md-9">
	  	<div class="nav-tabs-custom">
	    	<ul class="nav nav-tabs">
	      		<li class="active"><a href="#contatcs" data-toggle="tab">Tenant Contacts</a></li>
	      		<li><a href="#admins" data-toggle="tab">Tenant Administrators</a></li>
	    	</ul>

		    <div class="tab-content">
		    	<!-- Clients Tab -->
		    	<div class="tab-pane active" id="contatcs">
			    	 <table id="xa" class="table table-bordered table-striped table-hover">
		                <thead>
		                  	<tr>
			                    <th>Full Name</th>
			                    <th>Position</th>
			                    <th>Phone Number</th>
			                    <th>Email</th>
			                    <th></th>
		                  	</tr>
		                </thead>
		                
		                <tbody>                
		                	@foreach ($tenant->contacts as $contact)
			                  	<tr>
				                    <td>{{ $contact->first_name }} {{ $contact->last_name}}</td>
				                    <td>{{ $contact->position}}</td>
				                    <td>+{{ $contact->phone_number }}</td>
				                    <td>{{ $contact->email }}</td>
				                    <td>
				                    	<form id="deleteform" action="{{ route('tenant.contact', array($contact->id)) }}" method="POST">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <button id="delete" class="btn btn-danger btn-block">Remove</button>
                                        </form>
					              	</td>
			                  	</tr>                
		                 	@endforeach
		                </tbody>
		                
		                <tfoot>
		                  	<tr>
			                    <th>Full Name</th>
			                    <th>Position</th>
			                    <th>Phone Number</th>
			                    <th>Email</th>
			                    <th></th>
		                  	</tr>
		                </tfoot>
		            </table>
		        </div>

		        <!-- Admins Tab -->
		    	<div class="tab-pane" id="admins">
			    	<table id="xb" class="table table-bordered table-striped table-hover">
		                <thead>
		                  	<tr>
			                    <th>Name</th>
			                    <th>Email</th>
			                    <th>Last Login</th>
			                    <th></th>
		                  	</tr>
		                </thead>
		                
		                <tbody>                
		                	@foreach ($tenant->users as $admin)
			                  	<tr>
				                    <td>{{ $admin->staff->first_name }} {{ $admin->staff->last_name }}</td>
				                    <td>{{ $admin->email }}</td>
				                    <td>{{ $admin->last_login }}</td>
				                    <td>
				                    	<form id="deleteform" action="{{ route('tenant.admin', array($admin->id)) }}" method="POST">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <button id="delete" class="btn btn-danger btn-block">Remove</button>
                                        </form>
					              	</td>
			                  </tr>                
		                 	@endforeach
		                </tbody>
		                
		                <tfoot>
		                  	<tr>
			                    <th>Name</th>
			                    <th>Email</th>
			                    <th>Last Login</th>
			                    <th></th>
		                  	</tr>
		                </tfoot>
		            </table>
		        </div>
		    </div><!-- /.tab-content -->
	  	</div><!-- /.nav-tabs-custom -->
	</div><!-- /.col -->
</div><!-- /.row -->
@stop

<!-- Page Scripts -->
@section('scripts')
    <!-- DataTables -->
    <script src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script>
        $(function () {
            $('#xa').DataTable({
                'paging'      : true,
                'lengthChange': false,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : false
            });

            $('#xb').DataTable({
                'paging'      : true,
                'lengthChange': false,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : false
            });

            $('#deleteTenant').on('click', function(e){
				e.preventDefault();
				var $this = $(this);
				swal({
				  title: "Are you sure Remove Tenant",
				  text: "You will not be able to recover the Tenant!",
				  type: "warning",
				  showCancelButton: true,
				  confirmButtonColor: '#3085d6',
				  cancelButtonColor: '#d33',
				  confirmButtonText: 'Yes, Remove Tenant!',
				  cancelButtonText: 'No, Cancel Please!',
				  confirmButtonClass: 'btn btn-success',
				  cancelButtonClass: 'btn btn-danger',
				  buttonsStyling: false
				  }).then(function () {
					swal("Processed", "Please Delete from Tenant List", "info");
					    e.preventDefault();
					}, function (dismiss) {
					  // dismiss can be 'cancel', 'overlay',
					  // 'close', and 'timer'
					  if (dismiss === 'cancel') {
					    swal("Cancelled", "Tenant Not Removed :)", "info");
					    e.preventDefault();
					  }
					})
			});
        });
    </script>
@stop