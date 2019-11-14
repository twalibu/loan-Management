@extends('masters.tenant.app')

<!-- Page Title -->
@section('title')Offices @stop

<!-- Head Styles -->
@section('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@stop

<!-- Page Header -->
@section('header')Offices @stop

<!-- Page Description -->
@section('desc')Offices Dashboard @stop

<!-- Active Link -->
@section('active')Offices @stop

<!-- Page Content -->
@section('content')
<div class="row">
    <div class="col-xs-12">
		<div class="box">
            <div class="box-header">
                <h3 class="box-title">List of All Offices</h3>              
                <a href="{{ url('offices/create')  }} " class="btn btn-primary btn-sm pull-right">Add New Office</a>
            </div><!-- /.box-header -->
            <div class="box-body">
                <table id="xa" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Office Name</th>
                            <th>Details</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>                
                        @foreach ($offices as $office)
                        <tr>
                            <td>
                                <a href="{{ route('offices.show', array($office->id)) }}">{{ $office->name }}</a>
                            </td>
                            <td>
                                <b>{{ $office->loans->count() }} {{ str_plural('Loan', $office->loans->count())}}</b><br>
                                <b>{{ $office->clients->count() }} {{ str_plural('Client', $office->clients->count())}}</b><br>
                                <b>{{ $office->staff->count() }} {{ str_plural('Staff', $office->staff->count())}}</b>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info">Actions</button>
                                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="{{ route('offices.show', array($office->id)) }}">View Details</a></li>
                                        <li class="divider"></li>
                                        <li><a href="{{ route('offices.edit', array($office->id)) }}">Edit Details</a></li>
                                    </ul>
                                </div>
                            </td> 
                        </tr>                
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Office Name</th>
                            <th>Details</th>
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
                    { "width": 400, targets: 0 }
                ],
                "fixedColumns": true
            });
        });
    </script>
@stop