<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ Gravatar::src(Sentry::getUser()->email) }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ Sentry::getUser()->first_name }} {{ Sentry::getUser()->last_name }}</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">{{ config('app.name') }} Menu</li>
            <!-- Dashboard -->
            <li class="{{ active_class(if_uri(['admin/dashboard'])) }}"><a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            
            <!-- Tenants -->
            <li class="{{ active_class(if_uri(['admin/tenants', 'admin/tenants/create'])) }}"><a href="{{ url('admin/tenants') }}"><i class="fa fa-bank"></i> <span>Tenants</span></a></li>

            <!-- Clients Links -->
            <li class="{{ active_class(if_uri(['admin/communications'])) }}"><a href="{{ url('admin/communications') }}"><i class="fa fa-envelope"></i> <span>Communications</span></a></li>

            <!-- Settings -->
            <li class="treeview {{ active_class(if_uri([
                    'admin/subscriptions', 'admin/subscriptions/create',
                    ])) }}">
                <a href="#"><i class="fa fa-cogs"></i> <span>Settings</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ url('admin/subscriptions') }}">Subscription Types</a></li>
                    <li><a href="{{ url('admin/users') }}">System Users</a></li>
                </ul>
            </li>

            <!-- Tools Links -->
            <li class="header">{{ config('app.name') }} Tools</li>

            {{-- Subscription Reminders --}}            
            <li class="{{ active_class(if_uri(['admin/subscriptionCheck'])) }}">
                <a href="subscriptionCheck">
                    <i class="fa fa-circle-o text-green"></i> 
                    <span>Subscription Check</span>
                </a>
            </li>

            {{-- Bulk SMS --}}
            <li class="{{ active_class(if_uri('admin/smsBalanceCheck')) }}">
                <a href="smsBalanceCheck">
                    <i class="fa fa-circle-o text-blue"></i> 
                    <span>SMS Balance Check</span>
                </a>
            </li>
        </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>