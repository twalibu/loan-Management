@extends('masters.tenant.app')

<!-- Page Title -->
@section('title')Loan Types @stop

<!-- Head Styles -->
@section('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@stop

<!-- Page Header -->
@section('header')Loan Types @stop

<!-- Page Description -->
@section('desc')Loan Types @stop

<!-- Active Link -->
@section('active')Loan Types @stop

<!-- Page Content -->
@section('content')
    <div class="row">
        <div class="col-xs-12">
    		<div class="box">
                <div class="box-header">
                    <h3 class="box-title">List of All Loan Types</h3>              
                    <a href="{{ url('loanTypes/create')  }} " class="btn btn-primary btn-sm pull-right">Add New Loan Type</a>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <table id="xa" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Loan Name</th>
                                <th>Details</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>                
                            @foreach ($types as $type)
                            <tr>
                                <td>
                                    <a href="{{ route('loanTypes.edit', array($type->id)) }}">{{ $type->name }}</a>
                                </td>
                                <td>
                                    <b>Interest Rate:</b> {{ $type->interest * 100 }}% <br>
                                    <b>Duration:</b> {{ $type->duration }} {{ str_plural('Month', $type->duration )}}
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-info">Actions</button>
                                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="{{ route('loanTypes.edit', array($type->id)) }}">Edit Details</a></li>
                                            <li class="divider"></li>
                                            <li>
                                                <form id="deleteform" action="{{ route('loanTypes.destroy', array($type->id)) }}" method="POST">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button id="delete" class="btn btn-danger btn-block">Remove Loan Type</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td> 
                            </tr>                
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Loan Name</th>
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
                    { "width": 300, targets: 0 }
                ],
                "fixedColumns": true
            });
        });
    </script>
@stop