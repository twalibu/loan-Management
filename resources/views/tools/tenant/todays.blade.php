@extends('masters.tenant.app')

<!-- Page Title -->
@section('title')Today's Activities @stop

<!-- Head Styles -->
@section('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@stop

<!-- Page Header -->
@section('header')Today's Activities @stop

<!-- Page Description -->
@section('desc')Today's Activities @stop

<!-- Active Link -->
@section('active')Today's Activities @stop

<!-- Page Content -->
@section('content')
<div class="row">
    <div class="col-xs-12">
		<div class="box">
            <div class="box-header">
                <h3 class="box-title">List of Loans To Be Paid Today</h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <table id="xa" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Client Details</th>
                            <th>Loan Details</th>
                            <th>Payment Schedule</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>                
                        @foreach ($schedules as $schedule)
                            <tr>
                                <td>
                                    <b>Full Name: <a href="{{ route('clients.show', array($schedule->loan->client->id)) }}">{{ $schedule->loan->client->first_name }} {{ $schedule->loan->client->last_name }}</a></b><br>
                                    Phone Number: <b>+{{ $schedule->loan->client->phone_number }}</b><br>
                                    @if($schedule->loan->client->phone_number_2)
                                        Phone Number: <b>+{{ $schedule->loan->client->phone_number_2 }}</b><br>
                                    @endif
                                    @if($schedule->loan->client->email)
                                        Email: <b>{{ $schedule->loan->client->email }}</b>
                                    @endif
                                </td>
                                <td>
                                    <b>Loan ID: <a href="{{ route('loans.show', array($schedule->loan->id)) }}">{{ $schedule->loan->loan_identity }}</a></b><br>
                                    Loan Amount: <b>Tsh {{ number_format($schedule->loan->amount, 2) }}/-</b><br>
                                    Loan Type: <b>{{ $schedule->loan->type->name }}</b><br>
                                    Date Issued: <b>{{ Carbon::parse($schedule->loan->date_issued)->toFormattedDateString() }}</b>
                                </td>
                                <td>
                                    Payment Due: <b>Tsh {{ number_format($schedule->amount, 2) }}/-</b><br>
                                    Status: <b>{{ $schedule->status }}</b><br>
                                    Paid Amount: <b>Tsh {{ number_format($schedule->paid, 2) }}/-</b><br>
                                    Date: <b>{{ Carbon::parse($schedule->date)->toFormattedDateString() }}</b><br>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-info">Actions</button>
                                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="{{ route('pay.loan', array($schedule->loan->id)) }}">Make Payment</a></li>
                                            <li class="divider"></li>
                                            <li><a href="{{ route('loans.show', array($schedule->loan->id)) }}">Loan Details</a></li>
                                            <li class="divider"></li>
                                            <li><a href="{{ route('clients.show', array($schedule->loan->client->id)) }}">Client Details</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>         
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Client Details</th>
                            <th>Loan Details</th>
                            <th>Payment Schedule</th>
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