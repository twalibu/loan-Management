@extends('masters.tenant.app')

<!-- Page Title -->
@section('title')Loan Payments @stop

<!-- Head Styles -->
@section('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@stop

<!-- Page Header -->
@section('header')Loan Payments @stop

<!-- Page Description -->
@section('desc')Loan Payments Dashboard @stop

<!-- Active Link -->
@section('active')Loan Payments @stop

<!-- Page Content -->
@section('content')
<div class="row">
    <div class="col-xs-12">
		<div class="box">
            <div class="box-header">
                <h3 class="box-title">List of All Loan Payments</h3>              
                <a href="{{ url('payments/create') }} " class="btn btn-primary btn-sm pull-right">Make New Payment</a>
            </div><!-- /.box-header -->
            <div class="box-body">
                <table id="xa" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Client Name</th>
                            <th>For Loan</th>
                            <th>Payment Details</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>                
                        @foreach ($payments as $payment)
                            <tr>
                                <td>
                                    <a href="{{ route('clients.show', array($payment->loan->client->id)) }}">{{ $payment->loan->client->first_name }} {{ $payment->loan->client->last_name }}</a>
                                </td>
                                <td>
                                    <a href="{{ route('loans.show', array($payment->loan->id)) }}">Loan ID: <b>{{ $payment->loan->loan_identity }}</b></a><br>
                                    Loan Amount: <b>Tsh {{ number_format($payment->loan->amount, 2) }}</b><br>
                                </td>
                                <td>
                                    Payment Amount: <b>Tsh {{ number_format($payment->amount, 2) }}</b><br>
                                    Payment Date: <b>{{ Carbon::parse($payment->date)->toFormattedDateString() }}</b><br>
                                    Payment Method: <b>{{ $payment->method->name }}</b>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-info">Actions</button>
                                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="{{ route('payments.edit', array($payment->id)) }}">Edit Details</a></li>
                                            <li class="divider"></li>
                                            <form id="deleteform" action="{{ route('payments.destroy', array($payment->id)) }}" method="POST">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button id="delete" class="btn btn-danger btn-block">Delete Payment</button>
                                            </form>
                                        </ul>
                                    </div>
                                </td>
                            </tr>          
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Client Name</th>
                            <th>For Loan</th>
                            <th>Payment Details</th>
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
                    { "width": 200, targets: 0 }
                ],
                "fixedColumns": true
            });

            $('button#delete').on('click', function(e){
                    e.preventDefault();
                    swal({
                      title: "Are you sure Remove Payment",
                      text: "You will not be able to recover the Payment!",
                      type: "warning",
                      showCancelButton: true,
                      confirmButtonColor: '#3085d6',
                      cancelButtonColor: '#d33',
                      confirmButtonText: 'Yes, Remove Payment!',
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
                            swal("Cancelled", "Payment Not Removed :)", "error");
                            e.preventDefault();
                          }
                        })
                });
        });
    </script>
@stop