@extends('masters.tenant.app')

<!-- Page Title -->
@section('title')Clients @stop

<!-- Head Styles -->
@section('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@stop

<!-- Page Header -->
@section('header')Clients @stop

<!-- Page Description -->
@section('desc')Clients Dashboard @stop

<!-- Active Link -->
@section('active')Clients @stop

<!-- Page Content -->
@section('content')
<div class="row">
    <div class="col-xs-12">
		<div class="box">
            <div class="box-header">
                <h3 class="box-title">List of All Clients</h3>              
                <a href="{{ url('clients/create')  }}" class="btn btn-primary btn-sm pull-right">Add New Client</a>
            </div><!-- /.box-header -->
            <div class="box-body">
                <table id="xa" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Client Name</th>                                      
                            <th>Contacts</th>
                            <th>Loans Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>                
                        @foreach ($clients as $client)
                        <tr>
                            <td>
                                <a href="{{ route('clients.show', array($client->id)) }}">{{ $client->first_name }} {{ $client->middle_name }} {{ $client->last_name }}</a>
                            </td>
                            <td>
                                Phone Number: <b>+{{ $client->phone_number }}</b><br>
                                @if($client->phone_number_2)
                                    Phone Number: <b>+{{ $client->phone_number_2 }}</b><br>
                                @endif
                                @if($client->email)
                                    Email: <b>{{ $client->email }}</b>
                                @endif
                            </td>
                            <td>
                                Total Loans: <b>{{ $client->loans->count() }} {{ str_plural('Loan', $client->loans->count()) }}</b><br>
                                Active Loans: <b>{{ $client->loans->count() }} {{ str_plural('Loan', $client->loans->count()) }}</b>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info">Actions</button>
                                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="{{ route('clients.show', array($client->id)) }}">View Details</a></li>
                                        <li class="divider"></li>
                                        <li><a href="{{ route('clients.edit', array($client->id)) }}">Edit Details</a></li>
                                    </ul>
                                </div>
                            </td> 
                        </tr>                
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Client Name</th>                                   
                            <th>Contacts</th>
                            <th>Loans Count</th>
                            <th>Actions</th>
                        </tr>
                    </tfoot>
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
</div>
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
                "columnDefs": [
                    { "width": 300, targets: 0 }
                ],
                "fixedColumns": true
            });
        });
    </script>
@stop