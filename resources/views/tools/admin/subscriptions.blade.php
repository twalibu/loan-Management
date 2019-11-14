@extends('masters.admin.app')

<!-- Page Title -->
@section('title')Tools @stop

<!-- Head Styles -->
@section('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@stop

<!-- Page Header -->
@section('header')Tools @stop

<!-- Page Description -->
@section('desc')Tenant Subscription Check Tools @stop

<!-- Active Link -->
@section('active')Tools @stop

<!-- Page Content -->
@section('content')
<div class="row">
    <div class="col-xs-12">
		<div class="box">
            <div class="box-header">
                <h3 class="box-title">List of All Tenants with Renewal Approaching</h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <table id="xa" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Tenant Name</th>                                      
                            <th>Subscrption Details</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>                
                        @foreach ($subscriptions as $subscription)
                        <tr>
                            <td><a href="{{ route('tenants.show', array($subscription->tenant->id)) }}">{{ $subscription->tenant->name }}</a></td>
                            <td>
                                Subscription Type: <b>{{ $subscription->type->name }}</b><br>
                                Subscription Duration: <b>{{ $subscription->type->duration }} Months</b><br>
                                Subscription Price: <b>TZS {{ number_format($subscription->type->amount) }}/-</b><br>
                                Subscription Start Date: <b>{{ Carbon::parse($subscription->start_date)->toFormattedDateString() }}</b><br>
                                Subscription End Date: <b>{{ Carbon::parse($subscription->end_date)->toFormattedDateString() }}</b><br>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info">Actions</button>
                                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="{{ route('tenants.show', array($subscription->tenant->id)) }}">View Tenant</a></li>
                                        <li class="divider"></li>
                                        <li><a href="{{ route('tenant.subscription', array($subscription->tenant->id)) }}">Balance Top-Up</a></li>
                                    </ul>
                                </div>
                            </td> 
                        </tr>                
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Tenant Name</th>                                      
                            <th>Subscrption Details</th>
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
                'autoWidth'   : false
            });
        });
    </script>
@stop