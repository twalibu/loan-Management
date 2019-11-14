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
@section('desc')SMS Balance Check Tools @stop

<!-- Active Link -->
@section('active')Tools @stop

<!-- Page Content -->
@section('content')
<div class="row">
    <div class="col-xs-12">
		<div class="box">
            <div class="box-header">
                <h3 class="box-title">List of All Tenants With Low Balance</h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <table id="xa" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Tenant Name</th>                                      
                            <th>Balance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>                
                        @foreach ($sms as $sms)
                        <tr>
                            <td><a href="{{ route('tenants.show', array($sms->tenant->id)) }}">{{ $sms->tenant->name }}</a></td>
                            <td>
                                Balance: <b>{{ number_format($sms->balance) }}</b><br>
                                SMS Count: <b>{{ number_format($sms->balance/$sms->price) }}</b><br>
                                SMS Price: <b>TZS {{ $sms->price }}</b><br>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info">Actions</button>
                                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="{{ route('tenants.show', array($sms->tenant->id)) }}">View Tenant</a></li>
                                        <li class="divider"></li>
                                        <li><a href="{{ route('tenant.balance.topup', array($sms->tenant->id)) }}">Balance Top-Up</a></li>
                                    </ul>
                                </div>
                            </td> 
                        </tr>                
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Tenant Name</th>                                      
                            <th>Balance</th>
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