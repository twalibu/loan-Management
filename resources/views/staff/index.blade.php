@extends('masters.tenant.app')

<!-- Page Title -->
@section('title')Staff @stop

<!-- Head Styles -->
@section('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@stop

<!-- Page Header -->
@section('header')Staff @stop

<!-- Page Description -->
@section('desc')Staff Dashboard @stop

<!-- Active Link -->
@section('active')Staff @stop

<!-- Page Content -->
@section('content')
<div class="row">
    <div class="col-xs-12">
		<div class="box">
            <div class="box-header">
                <h3 class="box-title">List of All Staff</h3>              
                <a href="{{ url('staff/create')  }} " class="btn btn-primary btn-sm pull-right">Add New Staff</a>
            </div><!-- /.box-header -->
            <div class="box-body">
                <table id="xa" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Staff Name</th>                                      
                            <th>Contacts</th>
                            <th>Details</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>                
                        @foreach ($staff as $staff)
                        <tr>
                            <td>
                                <a href="{{ route('staff.edit', array($staff->id)) }}">{{ $staff->first_name }} {{ $staff->middle_name }} {{ $staff->last_name }}</a>
                            </td>
                            <td>
                                Email: <b>{{ $staff->user->email }}</b><br>
                                Phone Number: <b>+{{ $staff->phone_number }}</b>
                            </td>
                            <td>
                                <b>Title: </b>{{ $staff->type->name }}<br>
                                <b>Office: </b>{{ $staff->office->name }}
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info">Actions</button>
                                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li>
                                            <a href="{{ route('staff.edit', array($staff->id)) }}">Edit Details</a>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <form id="deleteform" action="{{ route('staff.destroy', array($staff->id)) }}" method="POST">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button id="deleteStaff" class="btn btn-danger btn-block">Remove Staff</button>
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
                            <th>Staff Name</th>                                      
                            <th>Contacts</th>
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
                    { "width": 200, targets: 0 }
                ],
                "fixedColumns": true
            });

            $('#deleteStaff').on('click', function(e){
                e.preventDefault();
                var $this = $(this);
                swal({
                  title: "Are you sure Remove Staff",
                  text: "You will not be able to recover the Staff!",
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes, Remove Staff!',
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
                        swal("Cancelled", "Staff Not Removed :)", "error");
                        e.preventDefault();
                      }
                    })
            });
        });
    </script>
@stop