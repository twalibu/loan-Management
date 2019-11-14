<!-- Blank Boilplate -->
@extends('app')

<!-- Page Title -->
@section('title')Loans @stop

<!-- Head Styles -->
@section('styles')
<!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css') }}">
@stop

<!-- Page Header -->
@section('header')Loans @stop

<!-- Page Description -->
@section('desc')Active Loans @stop

<!-- Active Link -->
@section('active')Loans @stop

<!-- Page Content -->
@section('content')
<div class="row">
    <div class="col-xs-12">
		<div class="box">
            <div class="box-header">
                <h3 class="box-title">List of All Active Loans</h3>              
                <a href="{{ url('loans/create') }} " class="btn btn-primary btn-sm pull-right">Add New Loan</a>
            </div><!-- /.box-header -->
            <div class="box-body">
                <table id="xa" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Loan ID</th>
                            <th>Client Name</th>
                            <th>Loan Type</th>
                            <th>Amount</th>
                            <th>Issued Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>                
                        @foreach ($loans as $loan)
                            @if($loan->summary->status != 'Completely Paid')
                                <tr>
                                    <td><a href="{{ route('loans.show', array($loan->id)) }}">ID: {{ $loan->loan_identity }}</a></td>
                                    <td><a href="{{ route('clients.show', array($loan->client->id)) }}">{{ $loan->client->first_name }} {{ $loan->client->last_name }}</a></td>
                                    <td>{{ $loan->type->name }}</td>
                                    <td>Tsh {{ number_format($loan->amount, 2) }}</td>
                                    <td>{{ Carbon::parse($loan->date_issued)->toFormattedDateString() }}</td>
                                    <td>{{ $loan->summary->status }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-info">Actions</button>
                                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a href="{{ route('loans.show', array($loan->id)) }}">View Details</a></li>
                                                @if(!$loan->contract)
                                                    <li class="divider"></li>
                                                    <li><a href="{{ route('contract.loan', array($loan->id)) }}">Contract Ready</a></li>
                                                @endif
                                                @if($loan->summary->status != 'Completely Paid')
                                                    <li class="divider"></li>
                                                    <li><a href="{{ route('pay.loan', array($loan->id)) }}">Make Payment</a></li>
                                                @endif
                                                @if($loan->payments->count() == 0 && $loan->penalts->count() == 0)
                                                    <li class="divider"></li>
                                                    <li><a href="{{ route('loans.edit', array($loan->id)) }}">Edit Details</a></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endif       
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Loan ID</th>
                            <th>Client Name</th>
                            <th>Loan Type</th>
                            <th>Amount</th>
                            <th>Issued Date</th>
                            <th>Status</th>
                            <th>Action</th>
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
<script src="{{ asset('bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
<script>
    $(function () {
        $('#xa').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "aaSorting": [],
            "info": true,
            "autoWidth": true
        });
    });
</script>
@stop