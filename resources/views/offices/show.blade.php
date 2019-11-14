@extends('masters.tenant.app')

<!-- Page Title -->
@section('title')Offices @stop

<!-- Head Styles -->
@section('styles')
	<!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@stop

<!-- Page Header -->
@section('header')Office @stop

<!-- Page Description -->
@section('desc')Office Details @stop

<!-- Active Link -->
@section('active')Offices @stop

<!-- Page Content -->
@section('content')
<div class="row">
	<div class="col-md-3">
  	<!-- Profile Image -->
	  	<div class="box box-primary">
	    	<div class="box-body box-profile">
	      		<img class="profile-user-img img-responsive img-circle" src="{{ Gravatar::src($office->id) }}" alt="User profile picture">
	      		<h3 class="profile-username text-center">{{ $office->name }}</h3>
	      		<p class="text-muted text-center">{{ $office->tenant->name }}</p>

	      		<ul class="list-group list-group-unbordered">
	        		<li class="list-group-item">
	          			<b>Loans</b>
	          			<p class="text-muted">{{ $office->loans->count() }} {{ str_plural('Loan', $office->loans->count())}}</p>
	        		</li>
		    		<li class="list-group-item">
	          			<b>Clients</b>
	          			<p class="text-muted">{{ $office->clients->count() }} {{ str_plural('Client', $office->clients->count())}}</p>
	        		</li>
	        		<li class="list-group-item">
	          			<b>Staff</b>
	          			<p class="text-muted">{{ $office->staff->count() }} {{ str_plural('Staff', $office->staff->count())}}</p>
	        		</li>
	      		</ul>

	      		<a href="{{ route('offices.edit', array($office->id)) }}" class="btn btn-primary btn-block"><b>Edit Office</b></a>
	      		<hr>
		      	<form id="deleteform" action="{{ route('offices.destroy', array($office->id)) }}" method="POST">
		            <input type="hidden" name="_method" value="DELETE">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
		                <button id="delete" class="btn btn-danger btn-block">Remove Office</button>
		        </form>
	    	</div><!-- /.box-body -->
	  	</div><!-- /.box -->
	</div><!-- /.col -->
	
	<div class="col-md-9">
	  	<div class="nav-tabs-custom">
	    	<ul class="nav nav-tabs">
	      		<li class="active"><a href="#loans" data-toggle="tab">Loans</a></li>
	      		<li><a href="#clients" data-toggle="tab">Clients</a></li>
	      		<li><a href="#staff" data-toggle="tab">Staff</a></li>
	    	</ul>
	    	<div class="tab-content">
	    		<!-- Cases Tab -->
	    		<div class="tab-pane active" id="loans">
		    	 	<table id="xa" class="table table-bordered table-striped table-hover">
	                	<thead>
		                  	<tr>
			                  	<th>Loan Details</th>
	                            <th>Status Details</th>
		                  	</tr>
	                	</thead>
	                	<tbody>        
		                	@foreach ($office->loans as $loan)
		                		<tr>
		                			<td>
		                				<a href="{{ route('loans.show', array($loan->id)) }}">Loan ID: <b>{{ $loan->loan_identity }}</b></a><br>
		                				Loan Amount: <b>Tsh {{ number_format($loan->amount, 2) }}</b>
		                			</td>
		                			<td>
		                				Date Issued: <b>{{ Carbon::parse($loan->date_issued)->toFormattedDateString() }}</b><br>
	                                    Loan Status: <b>{{ $loan->summary->status }}</b>
		                			</td>
		                		</tr>          
		                 	@endforeach
		                </tbody>
		                <tfoot>
		                  	<tr>
			                    <th>Loan Details</th>
	                            <th>Status Details</th>
		                  	</tr>
		                </tfoot>
	            	</table>
	        	</div>

	        	<div class="tab-pane" id="clients">
		    	 	<table id="xb" class="table table-bordered table-striped table-hover">
	                	<thead>
		                  	<tr>
			                  	<th>Client Details</th>
	                            <th>Contacts Details</th>
		                  	</tr>
	                	</thead>
	                	<tbody>        
		                	@foreach ($office->clients as $client)
		                		<tr>
		                			<td>
		                				<a href="{{ route('clients.show', array($client->id)) }}">{{ $client->first_name }} {{ $client->last_name}}</a><br>
		                				Total Loans: <b>{{ $client->loans->count() }} {{ str_plural('Loan', $client->loans->count())}}</b>
		                			</td>
		                			<td>
		                				Phone Number: <b>{{ $client->phone_number }}</b><br>
		                				@if($client->phone_number_2)
			                				Phone Number: <b>{{ $client->phone_number_2 }}</b><br>
			                			@endif
		                				@if($client->email)
			                				Email: <b>{{ $client->email }}</b>
			                			@endif
		                			</td>
		                		</tr>          
		                 	@endforeach
		                </tbody>
		                <tfoot>
		                  	<tr>
			                    <th>Client Details</th>
	                            <th>Contacts Details</th>
		                  	</tr>
		                </tfoot>
	            	</table>
	        	</div>

	        	<div class="tab-pane" id="staff">
		    	 	<table id="xc" class="table table-bordered table-striped table-hover">
	                	<thead>
		                  	<tr>
			                  	<th>Staff Name</th>                                      
	                            <th>Contacts</th>
		                  	</tr>
	                	</thead>
	                	<tbody>        
		                	@foreach ($office->staff as $staff)
		                		<tr>
		                			<td>
		                				{{ $staff->first_name }} {{ $staff->last_name }}<br>
		                				<b>{{ $staff->type->name }}</b>
		                			</td>
		                			<td>
		                				Email: <b>{{ $staff->user->email }}</b><br>
		                                Phone Number: <b>+{{ $staff->phone_number }}</b>
		                			</td>
		                		</tr>          
		                 	@endforeach
		                </tbody>
		                <tfoot>
		                  	<tr>
			                    <th>Staff Name</th>                                      
	                            <th>Contacts</th>
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

            $('#xc').DataTable({
                'paging'      : true,
                'lengthChange': false,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : false
            });

			$('button#delete').on('click', function(e){
				e.preventDefault();
				swal({
				  title: "Are you sure Remove Office",
				  text: "You will not be able to recover the Office!",
				  type: "warning",
				  showCancelButton: true,
				  confirmButtonColor: '#3085d6',
				  cancelButtonColor: '#d33',
				  confirmButtonText: 'Yes, Remove Office!',
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
					    swal("Cancelled", "Office Not Removed :)", "error");
					    e.preventDefault();
					  }
					})
			});

	    });
	</script>
@stop