<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ Gravatar::src(Sentinel::getUser()->email) }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ Sentinel::getUser()->staff->first_name }} {{ Sentinel::getUser()->staff->last_name }}</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">{{ config('app.name') }} Menu</li>
            <!-- Dashboard -->
            <li class="{{ active_class(if_uri(['dashboard'])) }}"><a href="{{ url('dashboard') }}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>

            <!-- Apointment Links -->
            <li class="{{ active_class(if_uri(['payments', 'payments/create'])) }}"><a href="{{ url('payments') }}"><i class="fa fa-money"></i> <span>Payments</span></a></li>

            <!-- Cases Links -->
            <li class="{{ active_class(if_uri(['loans', 'loans/create'])) }}"><a href="{{ url('loans') }}"><i class="fa fa-cube"></i> <span>Loans</span></a></li>

            <!-- Clients Links -->
            <li class="{{ active_class(if_uri(['clients', 'clients/create'])) }}"><a href="{{ url('clients') }}"><i class="fa fa-users"></i> <span>Clients</span></a></li>

            <!-- Staff Links -->
            <li class="{{ active_class(if_uri(['staff', 'staff/create'])) }}"><a href="{{ url('staff') }}"><i class="fa fa-drivers-license"></i> <span>Staff's</span></a></li>

            <!-- Offices Links -->
            <li class="{{ active_class(if_uri(['offices', 'offices/create'])) }}"><a href="{{ url('offices') }}"><i class="fa fa-home"></i> <span>Office's</span></a></li>

            <!-- Communications Links -->
            <li class="{{ active_class(if_uri(['communications'])) }}"><a href="{{ url('communications') }}"><i class="fa fa-envelope"></i> <span>SMS Portal</span></a></li>

            <!-- Settings Links -->
            <li class="treeview {{ active_class(if_uri(['sales', 'sales/create', 'loanTypes', 'loanTypes/create', 'loanOverdues', 'loanOverdues/create', 'paymentMethods', 'paymentMethods/create', 'types', 'types/create', 'schedules', 'roles', 'roles/create'])) }}">
                <a href="#"><i class="fa fa-cogs"></i> <span>Settings</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ url('sales') }}">Sales Contact</a></li>
                    <li><a href="{{ url('loanTypes') }}">Loan Types</a></li>
                    <li><a href="{{ url('loanOverdues') }}">Overdue Penalt</a></li>
                    <li><a href="{{ url('paymentMethods') }}">Payment Methods</a></li>
                    <li><a href="{{ url('schedules') }}">Notification Schedule</a></li>
                    <li><a href="{{ url('types') }}">Staff Types</a></li>
                    <li><a href="{{ url('roles') }}">System Roles</a></li> 
                </ul>
            </li>

            <!-- Tools Links -->
            <li class="header">{{ config('app.name') }} Tools</li>
            {{-- Today's Activities --}}            
            <li class="{{ active_class(if_uri(['today/payments'])) }}"><a href="{{ url('today/payments') }}"><i class="fa fa-circle-o text-brown"></i> <span>Today's Activities</span></a></li>
            {{-- Fast Track --}}            
            <li class="{{ active_class(if_uri(['fastTrack'])) }}"><a href="{{ url('fastTrack') }}"><i class="fa fa-circle-o text-orange"></i> <span>Fast Track</span></a></li>
            {{-- Reports --}}
            <li class="{{ active_class(if_uri(['tools/summary'])) }}"><a href="{{ url('tools/summary') }}"><i class="fa fa-circle-o text-green"></i> <span>Executive Summary</span></a></li>
            {{-- SMS Report --}}
            <li class="{{ active_class(if_uri(['tools/smsReports'])) }}"><a href="{{ url('tools/smsReports') }}"><i class="fa fa-circle-o text-red"></i> <span>SMS Reports</span></a></li>
            {{-- Bulk SMS --}}
            <li class="{{ active_class(if_uri(['bulk'])) }}"><a href="{{ url('bulk') }}"><i class="fa fa-circle-o text-blue"></i> <span>Bulk SMS</span></a></li>
        </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>