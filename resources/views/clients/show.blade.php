@extends('masters.tenant.app')

<!-- Page Title -->
@section('title')Clients @stop

<!-- Head Styles -->
@section('styles')
	<!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@stop

<!-- Page Header -->
@section('header')Client @stop

<!-- Page Description -->
@section('desc')Client Details @stop

<!-- Active Link -->
@section('active')Clients @stop

<!-- Page Content -->
@section('content')
<div class="row">
	<div class="col-md-3">
  	<!-- Profile Image -->
	  	<div class="box box-primary">
	    	<div class="box-body box-profile">
	      		<img class="profile-user-img img-responsive img-circle" src="{{ Gravatar::src($client->email) }}" alt="User profile picture">
	      		<h3 class="profile-username text-center">{{ $client->first_name }} {{ $client->middle_name }} {{ $client->last_name }}</h3>
	      		<p class="text-muted text-center">{{ $client->office->name }}</p>

	      		<ul class="list-group list-group-unbordered">
	      			@if($client->email)
		        		<li class="list-group-item">
		          			<b>Email</b>
		          			<p class="text-muted">{{ $client->email }}</p>
		        		</li>
		    		@endif
	        		<li class="list-group-item">
	          			<b>Phone Number</b>
	          			<p class="text-muted">+{{ $client->phone_number }}</p>
	        		</li>
	        		@if($client->phone_number_2)
	        			<li class="list-group-item">
	          				<b>Additional Phone Number</b> 
	          				<p class="text-muted">+{{ $client->phone_number_2 }}</p>
	        			</li>
	        		@endif
	      		</ul>

	      		<a href="{{ route('clients.edit', array($client->id)) }}" class="btn btn-primary btn-block"><b>Edit Client</b></a>
	      		<hr>
		      	<form id="deleteform" action="{{ route('clients.destroy', array($client->id)) }}" method="POST">
		            <input type="hidden" name="_method" value="DELETE">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
		                <button id="delete" class="btn btn-danger btn-block">Remove Client</button>
		        </form>
	    	</div><!-- /.box-body -->
	  	</div><!-- /.box -->
	</div><!-- /.col -->
	
	<div class="col-md-9">
	  	<div class="nav-tabs-custom">
	    	<ul class="nav nav-tabs">
	      		<li class="active"><a href="#loans" data-toggle="tab">Client Loans</a></li>
	    	</ul>
	    	<div class="tab-content">
	    		<!-- Policies Tab -->
	    		<div class="tab-pane active" id="loans">
		    	 	<table id="xa" class="table table-bordered table-striped table-hover">
	                	<thead>
		                  	<tr>
			                  	<th>Loan Details</th>
	                            <th>Status Details</th>
		                  	</tr>
	                	</thead>
	                	<tbody>        
		                	@foreach ($client->loans as $loan)
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

			$('button#delete').on('click', function(e){
				e.preventDefault();
				swal({
				  title: "Are you sure Remove Client",
				  text: "You will not be able to recover the Client!",
				  type: "warning",
				  showCancelButton: true,
				  confirmButtonColor: '#3085d6',
				  cancelButtonColor: '#d33',
				  confirmButtonText: 'Yes, Remove Client!',
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
					    swal("Cancelled", "Client Not Removed :)", "error");
					    e.preventDefault();
					  }
					})
			});

	    });
	</script>
@stop