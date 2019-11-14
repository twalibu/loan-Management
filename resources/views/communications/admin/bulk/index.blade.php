<!-- Blank Boilplate -->
@extends('app')

<!-- Page Title -->
@section('title')Bulk SMS @stop

<!-- Head Styles -->
@section('styles')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css') }}">
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('bower_components/AdminLTE/plugins/select2/select2.min.css') }}">
@stop

<!-- Page Header -->
@section('header')Bulk SMS @stop

<!-- Page Description -->
@section('desc')Bulk SMS Dashboard @stop

<!-- Active Link -->
@section('active')Bulk SMS @stop

<!-- Page Content -->
@section('content')
<div class="row">
    <div class="col-lg-12 col-xs-12">
        <!-- Quick SMS Widget -->
        <form action="{{ url('bulks/send') }}" method="POST" accept-charset="UTF-8">
            <div class="box box-info">
                <div class="box-header">
                    <i class="fa fa-envelope"></i>
                    <h3 class="box-title">Quick SMS</h3>
                    <!-- tools box -->
                    <div class="pull-right box-tools" id="sms-counter">
                        <i class="fa fa-spinner"></i> Remaining Characters <span class="label label-warning remaining">:</span>
                    </div><!-- /. tools -->
                </div>
                <div class="box-body">                    
                    <input name="_token" value="{{ csrf_token() }}" type="hidden">
                    <input name="tenant" value="{{ Sentinel::getUser()->tenant->id }}" type="hidden">
                    <div class="form-group">
                        <select name="receiver[]" class="form-control select2" multiple="multiple" data-placeholder="Select Client...">
                            <option value="all">Select All</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }} | Contacts in Group: {{ number_format($group->contacts->count(), 0) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <textarea name="message" id="message" class="form-control" placeholder="Enter Messege Here" style="width: 100%; height: 110px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
                    </div>                        
                </div>
                <div class="box-footer clearfix">
                    <button class="pull-right btn btn-default" type="submit">Send <i class="fa fa-arrow-circle-right"></i></button>
                </div>
            </div>
        </form>
    </div><!-- end of top row -->

    <div class="col-xs-12">
		<div class="box">
            <div class="box-header">
                <h3 class="box-title">List of All Contact Groups</h3> 
                <a href="{{ url('bulk/create') }}" class="btn btn-primary btn-sm pull-right"">Add Contact Group</a>
            </div><!-- /.box-header -->
            <div class="box-body">
                <table id="xa" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Group Name</th>
                            <th>Contact Count</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>                
                        @foreach ($groups as $group)                            
                            <tr>
                                <td><a href="{{ route('bulk.show', array($group->id)) }}">{{ $group->name }}</a></td>
                                <td>{{ number_format($group->contacts->count(), 0) }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-info">Actions</button>
                                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="{{ route('bulk.show', array($group->id)) }}">View Details</a></li>
                                            <li class="divider"></li>
                                            <li><a href="{{ route('bulks.add', array($group->id)) }}">Add Contacts</a></li>
                                            <li class="divider"></li>
                                            <li><a href="{{ route('bulk.edit', array($group->id)) }}">Edit Details</a></li>
                                            <li class="divider"></li>
                                            <li>
                                                <form id="deleteform" action="{{ route('bulk.destroy', array($group->id)) }}" method="POST">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button id="delete" class="btn btn-danger btn-block">Delete Group</button>
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
                            <th>Group Name</th>
                            <th>Contact Count</th>
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
<!-- Select2 -->
<script src="{{ asset('bower_components/AdminLTE/plugins/select2/select2.full.min.js') }}"></script>
<script>
    $(function () {
        $('#xa').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "aaSorting": [[0, 'desc']],
            "info": true,
            "autoWidth": true
        });

        //Initialize Select2 Elements
        $(".select2").select2();

        $('#message').countSms('#sms-counter');
    });
</script>
<!-- SMS Counter -->
<script src="{{ asset('bower_components/sms-counter/sms_counter.min.js') }}"></script>
@stop