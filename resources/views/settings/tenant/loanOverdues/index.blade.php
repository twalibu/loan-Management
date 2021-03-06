@extends('masters.tenant.app')

<!-- Page Title -->
@section('title')Loan Overdue Penalts @stop

<!-- Head Styles -->
@section('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@stop

<!-- Page Header -->
@section('header')Loan Overdue Penalts @stop

<!-- Page Description -->
@section('desc')Loan Overdue Penalts @stop

<!-- Active Link -->
@section('active')Loan Overdue Penalts @stop

<!-- Page Content -->
@section('content')
    <div class="row">
        <div class="col-xs-12">
    		<div class="box">
                <div class="box-header">
                    <h3 class="box-title">List of All Loan Overdue Penalts</h3>              
                    <a href="{{ url('loanOverdues/create')  }} " class="btn btn-primary btn-sm pull-right">Add New Loan Overdue Penalt</a>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <table id="xa" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Loan Overdue Penalt</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>                
                            @foreach ($penalts as $penalt)
                            <tr>
                                <td><a href="{{ route('loanOverdues.edit', array($penalt->id)) }}">{{ $penalt->penalt * 100 }}% Penalt</a></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-info">Actions</button>
                                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="{{ route('loanOverdues.edit', array($penalt->id)) }}">Edit Details</a></li>
                                            <li class="divider"></li>
                                            <li>
                                                <form id="deleteform" action="{{ route('loanOverdues.destroy', array($penalt->id)) }}" method="POST">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button id="delete" class="btn btn-danger btn-block">Remove Loan Overdue Penalt</button>
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
                                <th>Loan Overdue Penalt</th>
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