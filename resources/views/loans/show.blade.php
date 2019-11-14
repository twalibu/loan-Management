@extends('masters.tenant.app')

<!-- Page Title -->
@section('title')Loans @stop

<!-- Head Styles -->
@section('styles')
	<!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@stop

<!-- Page Header -->
@section('header')Loan @stop

<!-- Page Description -->
@section('desc')Loan Details @stop

<!-- Active Link -->
@section('active')Loans @stop

<!-- Page Content -->
@section('content')
<div class="row">
	<div class="col-md-6">
		<div class="box box-info">
        	<div class="box-body">              	
              	@if($loan->summary->status != 'Completely Paid')
	              	<a class="btn btn-app" href="{{ route('pay.loan', array($loan->id)) }}">
	                	<i class="fa fa-money"></i> Make Payment
	              	</a>
	            @endif
              	
              	@if($loan->summary->status != 'Completely Paid')
	              	<a class="btn btn-app" href="{{ route('pay.overwrite', array($loan->id)) }}">
	                	<i class="fa fa-cubes"></i> Overwrite Payment
	              	</a>
	            @endif

	            @if($loan->payments->count() == 0 && $loan->penalts->count() == 0)              	
	              	<a class="btn btn-app" href="{{ route('loans.edit', array($loan->id)) }}">
	                	<i class="fa fa-edit"></i> Edit Loan Details
	              	</a>
	            @endif

              	<a id="deleteLoan" class="btn btn-app">
                	<i class="fa fa-remove"></i> Delete Loan
              	</a>
              	<form id="deleteform" action="{{ route('loans.destroy', array($loan->id)) }}" method="POST">
		            <input type="hidden" name="_method" value="DELETE">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
		        </form>
            	<!-- /.box-body -->
          	</div>
    	</div>
	</div>

	<div class="col-md-6">
		<div class="box box-success">
			<div class="box-body">
				<div class="col-xs-12">
		          	<p class="lead">Client Details</p>

		          	<div class="table-responsive">
		            	<table class="table">
		              		<tr>
			                	<th style="width:50%">Full name:</th>
			                	<td><a href="{{ route('clients.show', array($loan->client->id)) }}">{{ $loan->client->first_name }} {{ $loan->client->middle_name }} {{ $loan->client->last_name }}</a></td>
			              	</tr>
		              		<tr>
		                		<th>Phone Number:</th>
		                		<td>+{{ $loan->client->phone_number }}</td>
		              		</tr>
		              		@if($loan->client->email)
		              			<tr>
			                		<th>Email:</th>
			                		<td>{{ $loan->client->email }}</td>
			              		</tr>
		              		@endif
		            	</table>
		          	</div>
		        </div>
		    </div>
		</div>		
	</div>
</div>
<div class="row">
	<div class="col-md-3">
	  	<!-- Profile Image -->
	  	<div class="box box-warning">
	    	<div class="box-body box-profile">
	      		<img class="profile-user-img img-responsive img-circle" src="{{ Gravatar::src($loan->id) }}" alt="User profile picture">
	      		<h3 class="profile-username text-center">{{ $loan->summary->status }}</h3>
	      		<p class="text-muted text-center"><a href="{{ route('offices.show', array($loan->office->id)) }}">{{ $loan->office->name }} </a></p>

	      		<ul class="list-group list-group-unbordered">
	        		<li class="list-group-item">
	          			<b>Loan Type</b>
	          			<p class="text-muted">{{ $loan->type->name }}</p>
	        		</li>
        			<li class="list-group-item">
          				<b>Issued Date</b> 
          				<p class="text-muted">{{ Carbon::parse($loan->date_issued)->toFormattedDateString() }}</p>
        			</li>
	        		<li class="list-group-item">
	          			<b>Interest Rate</b> 
	          			<p class="text-muted">{{ ($loan->type->interest)*100 }}% Per Annual</p>
	        		</li>
	        		<li class="list-group-item">
	          			<b>Monthly Payment</b> 
	          			<p class="text-muted">Tsh {{ number_format($loan->summary->monthly, 2) }}</p>
	        		</li>
	        		<li class="list-group-item">
	          			<b>Total Principal</b> 
	          			<p class="text-muted">Tsh {{ number_format($loan->summary->principal, 2) }}</p>
	        		</li>
	        		<li class="list-group-item">
	          			<b>Total Interest</b> 
	          			<p class="text-muted">Tsh {{ number_format($loan->summary->interest, 2) }}</p>
	        		</li>
	        		<li class="list-group-item">
	          			<b>Total Repayment</b> 
	          			<p class="text-muted">Tsh {{ number_format($loan->summary->principal + $loan->summary->interest, 2) }}</p>
	        		</li>
	        		@if($loan->summary->penalt > 0)
		        		<li class="list-group-item">
		          			<b>Total Penalt</b> 
		          			<p class="text-muted">Tsh {{ number_format($loan->summary->penalt, 2) }}</p>
		        		</li>
		        	@endif
		        	@if($loan->summary->overwrite > 0)
		        		<li class="list-group-item">
		          			<b>Overwritten Amount</b> 
		          			<p class="text-muted">Tsh {{ number_format($loan->summary->overwrite, 2) }}</p>
		        		</li>
		        	@endif
	        		<li class="list-group-item">
	          			<b>Total Paid</b> 
	          			<p class="text-muted">Tsh {{ number_format($loan->summary->paid, 2) }}</p>
	        		</li>
	        		<li class="list-group-item">
	          			<b>Total Balance</b> 
	          			<p class="text-muted">Tsh {{ number_format($loan->summary->principal + $loan->summary->interest + $loan->summary->penalt - $loan->summary->overwrite - $loan->summary->paid),2 }}</p>
	        		</li>
	        		<div class="callout callout-info">
	                	<h4>{{ $loan->tenant->name }}</h4>
	                	<p>{{ $loan->office->name }}</p>
	              	</div>
	      		</ul>
	    	</div><!-- /.box-body -->
	  	</div><!-- /.box -->
	</div><!-- /.col -->
	
	<div class="col-md-9">
	  	<div class="nav-tabs-custom">
	    	<ul class="nav nav-tabs">
	      		<li class="active"><a href="#schedule" data-toggle="tab">Loan Payment Schedule</a></li>
	      		<li><a href="#payments" data-toggle="tab">Loan Payments</a></li>
	      		<li><a href="#penalts" data-toggle="tab">Loan Penalts</a></li>
	    	</ul>
	    	<div class="tab-content">
	    		<!-- Loan Schedule Tab -->
	    		<div class="tab-pane active" id="schedule">
		    	 	<table id="xa" class="table table-bordered table-striped table-hover">
	                	<thead>
		                  	<tr>
		                  		<th>Sort</th>
		                  		<th>Due Date</th>
			                    <th>Principal</th>
			                    <th>Interest</th>
			                    <th>Paid</th>
			                    <th>Balance</th>
			                    <th>Status</th>
		                  	</tr>
	                	</thead>
	                	<tbody>
		                  	@foreach($loan->schedules as $schedule)
		                  		<tr>
		                  			<td>{{ $schedule->id }}</td>
		                  			<td>{{ Carbon::parse($schedule->date)->toFormattedDateString() }}</td>
									<td>Tsh {{ number_format($schedule->principal),2 }}</td>
									<td>Tsh {{ number_format($schedule->interest),2 }}</td>
									<td>Tsh {{ number_format($schedule->paid),2 }}</td>
									<td>Tsh {{ number_format($schedule->balance),2 }}</td>
									<td>{{ $schedule->status }}</td>
								</tr>
							@endforeach
		                </tbody>
		                <tfoot>
		                  	<tr>
		                  		<th>Sort</th>
		                  		<th>Due Date</th>
			                    <th>Principal</th>
			                    <th>Interest</th>
			                    <th>Paid</th>
			                    <th>Balance</th>
			                    <th>Status</th>
		                  	</tr>
		                </tfoot>
	            	</table>
	        	</div>

	        	<!-- Loan Payment Tab -->
	    		<div class="tab-pane" id="payments">
		    	 	<table id="xb" class="table table-bordered table-striped table-hover">
	                	<thead>
		                  	<tr>
		                  		<th>Payment Date</th>
			                    <th>Amount</th>
			                    <th>Payment Method</th>
		                  	</tr>
	                	</thead>
	                	<tbody>
		                  	@foreach($loan->payments as $payment)
		                  		<tr>
		                  			<td>{{ Carbon::parse($payment->date)->toFormattedDateString() }}</td>
									<td>Tsh {{ number_format($payment->amount),2 }}</td>
									<td>{{ $payment->method->name }}</td>
								</tr>
							@endforeach
		                </tbody>
		                <tfoot>
		                  	<tr>
		                  		<th>Payment Date</th>
			                    <th>Amount</th>
			                    <th>Payment Method</th>
		                  	</tr>
		                </tfoot>
	            	</table>
	        	</div>

	        	<!-- Loan Penalt Tab -->
	    		<div class="tab-pane" id="penalts">
		    	 	<table id="xc" class="table table-bordered table-striped table-hover">
	                	<thead>
		                  	<tr>
		                  		<th>Month</th>
			                    <th>Penalt Amount</th>
			                    <th>Date</th>
		                  	</tr>
	                	</thead>
	                	<tbody>
		                  	@foreach($loan->penalts as $penalt)
		                  		<tr>
		                  			<td>Month {{ $penalt->month }}</td>
									<td>Tsh {{ number_format($penalt->amount),2 }}</td>
									<td>{{ Carbon::parse($penalt->date)->toFormattedDateString() }}</td>
								</tr>
							@endforeach
		                </tbody>
		                <tfoot>
		                  	<tr>
		                  		<th>Month</th>
			                    <th>Penalt Amount</th>
			                    <th>Date</th>
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
                'autoWidth'   : false,
                "columnDefs": [
		            {
		                "targets": [ 0 ],
		                "visible": false,
		                "searchable": false
		            }
		        ]
            });

            $('#xb').DataTable({
                'paging'      : true,
                'lengthChange': false,
                'searching'   : true,
                'ordering'    : true,
                "aaSorting": [[0, 'asc']],
                'info'        : true,
                'autoWidth'   : false
            });

            $('#xc').DataTable({
                'paging'      : true,
                'lengthChange': false,
                'searching'   : true,
                'ordering'    : true,
                "aaSorting": [[0, 'asc']],
                'info'        : true,
                'autoWidth'   : false
            });

			$('#deleteLoan').on('click', function(e){
				e.preventDefault();
				swal({
				  title: "Are you sure Remove Loan",
				  text: "You will not be able to recover the Loan!",
				  type: "warning",
				  showCancelButton: true,
				  confirmButtonColor: '#3085d6',
				  cancelButtonColor: '#d33',
				  confirmButtonText: 'Yes, Remove Loan!',
				  cancelButtonText: 'No, Cancel Please!',
				  confirmButtonClass: 'btn btn-success',
				  cancelButtonClass: 'btn btn-danger',
				  buttonsStyling: false
				  }).then(function () {
					  $("#deleteform").submit();
					  return true;
					}, function (dismiss) {
					  // dismiss can be 'cancel', 'overlay',
					  // 'close', and 'timer'
					  if (dismiss === 'cancel') {
					    swal("Cancelled", "Loan Not Removed :)", "info");
					    e.preventDefault();
					  }
					})
			});

	    });
	</script>
@stop