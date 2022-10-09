@inject('request', 'Illuminate\Http\Request')
<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <ul class="sidebar-menu">

            <li class="{{ $request->segment(2) == 'home' ? 'active' : '' }}">
                <a href="{{ url('/') }}">
                    <div class="dashboard_icon"></div>
                    <span class="title">@lang('global.app_dashboard')</span>
                </a>
            </li>

            
            @can('lesson_access')
            <li class="{{ $request->segment(2) == 'lessons' ? 'active' : '' }}">
                <a href="{{ route('admin.lessons.index') }}">
                    <i class="fa fa-gears"></i>
                    <span class="title">@lang('global.lessons.title')</span>
                </a>
            </li>
            @endcan
            
            @can('association_view')
            <li class="{{ $request->segment(2) == 'associations' ? 'active' : '' }}">
                <a href="{{ route('admin.associations.index') }}">
                    <div class="associations_icon"></div>
                    <span class="title">Manage Associations</span>
                </a>
            </li>
            @endcan
            
            @can('client_view')
            <li class="{{ $request->segment(2) == 'clients' ? 'active' : '' }}">
                <a href="{{ route('admin.clients.index') }}">
                    <div class="corporations_icon"></div>
                    <span class="title">Manage Clients</span>
                </a>
            </li>
            @endcan

            @can('client_view')
            <li  class="{{ $request->segment(2) == 'divisions' ? 'active' : '' }}">
                <a href="{{ route('admin.divisions.index') }}">
                    <div class="division_icon"></div>
                    <span class="title">Manage Divisions</span>
                </a>
            </li>
            @endcan


            @can('district_view')
            <li class="{{ $request->segment(2) == 'districts' ? 'active' : '' }}">
                <a href="{{ route('admin.districts.index') }}">
                    <div class="district_icon"></div>
                    <span class="title">Manage Districts</span>
                </a>
            </li>
            @endcan


            @can('store_view')
            <li class="{{ $request->segment(2) == 'stores' ? 'active' : '' }}">
                <a href="{{ route('admin.stores.index') }}">
                    <div class="store_icon"></div>
                    <span class="title">Manage Stores</span>
                </a>
            </li>
            @endcan


            @can('manage_blackout_dates')
            <li class="{{ $request->segment(2) == 'blackoutdates' ? 'active' : '' }}">
                <a href="{{ route('admin.blackoutdates.index') }}">
                    <div class="event_icon"></div>
                    <span class="title">Manage Blackout Dates</span>
                </a>
            </li>
            @endcan

            @can('area_view')
            <li class="{{ $request->segment(2) == 'areas' ? 'active' : '' }}">
                <a href="{{ route('admin.areas.index') }}">
                    <div class="area_icon"></div>
                    <span class="title">Manage Area</span>
                </a>
            </li>
            @endcan

            @can('mileage_view')
<!--            <li class="{{ $request->segment(2) == 'mileages' ? 'active' : '' }}">
                <a href="{{ route('admin.mileages.index') }}">
                    <div class="mileage_icon"></div>
                    <span class="title">Manage Mileage</span>
                </a>
            </li>-->
            @endcan

            @can('excused-employee')
            <li class="{{ $request->segment(2) == 'excusedemployee' ? 'active' : ''  }}">
                <a href="{{ route('admin.excusedemployee.index') }}">
                    <div class="employee_icon"></div>
                    <span class="title">Scheduled employees</span>
                </a>
            </li>
            @endcan

          
            @if($request->user()->can('event_view') || $request->user()->can('view_client_event'))
            <li class="<?php if($request->segment(2) == 'events' || $request->segment(2) == 'schedule-event') echo 'active';?>">
                <a href="#">
                    <i class="event_icon" aria-hidden="true"></i>
                    <span class="title">Events</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
               
                <ul class="treeview-menu">
                    <li class="{{ $request->segment(2) == 'events' ? 'active active-sub' : '' }}">
                    <a href="{{ route('admin.events.index') }}">
                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                        <span class="title">Upcoming Events</span>
                    </a>
                    </li>
                    <li class="{{ $request->segment(2) == 'prior-events' ? 'active active-sub' : '' }}">
                    <a href="{{ route('admin.events.completedevents') }}">
                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                        <span class="title">Prior Events</span>
                    </a>
                    </li>
                    @can('show_inactive_events')
                    <li class="{{ $request->segment(2) == 'inactive' ? 'active active-sub' : '' }}">
                    <a href="{{ route('admin.events.get_inactive') }}">
                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                        <span class="title">Inactive Events</span>
                    </a>
                    </li>
                    @endcan
                    
                    @can('event_create')
                    <li class="{{ $request->segment(2) == 'import' ? 'active active-sub' : '' }}">
                    <a href="{{ route('admin.events.import') }}">
                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                        <span class="title">Import Events</span>
                    </a>
                    </li>
                    <li class="{{ $request->segment(2) == 'import-invoice' ? 'active active-sub' : '' }}">
                    <a href="{{ route('admin.events.invoice') }}">
                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                        <span class="title">Import Event Invoice</span>
                    </a>
                    </li>
                    <li class="{{ $request->segment(2) == 'import-meal' ? 'active active-sub' : '' }}">
                    <a href="{{ route('admin.events.meal') }}">
                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                        <span class="title">Import Meal Invoice</span>
                    </a>
                    </li>
                    <li class="{{ $request->segment(2) == 'import-lodging' ? 'active active-sub' : '' }}">
                    <a href="{{ route('admin.events.lodging') }}">
                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                        <span class="title">Import Lodging Invoice</span>
                    </a>
                    </li>
                    
                    @endcan
                </ul>
            </li>
            @endif

            @can('employee_view')
            <li class="{{ $request->segment(2) == 'employees' ? 'active' : '' }}">
                <a href="{{ route('admin.employees.index') }}">
                    <div class="employee_icon"></div>
                    <span class="title">Manage Employee</span>
                </a>
            </li>
            @endcan
            
            @can('timesheet_access')
             <li class="treeview <?php if($request->segment(2) == 'timesheets' || $request->segment(2) == 'timesheets')echo 'active';?>">
                 <a href="#">
                    <i class="event_icon" aria-hidden="true"></i>
                    <span class="title">Manage Timesheets</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">

                    <li class="{{ $request->segment(2) == 'timesheets' ? 'active active-sub' : '' }}">
                    <a href="{{ route('admin.timesheets.index') }}">
                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                        <span class="title">Pending Timesheets</span>
                    </a>
                    </li>

                    <li class="{{ $request->segment(2) == 'approved-timesheet' ? 'active active-sub' : '' }}">
                    <a href="{{ route('admin.timesheets.approved') }}">
                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                        <span class="title">Approved Timesheets</span>
                    </a>
                    </li>

                    <li class="{{ $request->segment(2) == 'rejected-timesheet' ? 'active active-sub' : '' }}">
                    <a href="{{ route('admin.timesheets.rejected') }}">
                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                        <span class="title">Rejected Timesheets</span>
                    </a>
                    </li>

                    <li class="{{ $request->segment(2) == 'submitted-timesheet' ? 'active active-sub' : '' }}">
                    <a href="{{ route('admin.timesheets.submitted') }}">
                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                        <span class="title">Submitted Timesheets</span>
                    </a>
                    </li>

                    
                    <li class="{{ $request->segment(2) == 'absent_employees' ? 'active active-sub' : '' }}">
                    <a href="{{ route('admin.timesheets.absent_employees') }}">
                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                        <span class="title">Absent Employee</span>
                    </a>
                    </li>

                    <li class="{{ $request->segment(2) == 'import_list' ? 'active active-sub' : '' }}">
                    <a href="{{ route('admin.timesheets.import_list') }}">
                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                        <span class="title">Import Time Entries</span>
                    </a>
                    </li>
                </ul>
             </li>
             @endcan
            
            
             @can('import_data')
            <li class="{{ $request->segment(2) == 'import' ? 'active' : '' }}">
                <a href="{{ route('admin.import.index') }}">
                <i class="fa fa-download" aria-hidden="true"></i>
                    <span class="title">Import</span>
                </a>
            </li>
            @endcan

            
<!--            <li class="{{ $request->segment(2) == 'reports' ? 'active' : '' }}">
                <a href="{{ route('admin.reports.index') }}">
                    <i class="fa fa fa-bar-chart" aria-hidden="true"></i>
                    <span class="title">Manage Reports</span>
                </a>
            </li>-->



            @can('report_view')
            <li class="treeview <?php if($request->segment(2) == 'reports' || $request->segment(2) == 'timesheets11')echo 'active';?>">
                 <a href="#">
                    <i class="fa fa fa-bar-chart" aria-hidden="true"></i>
                    <span class="title">Manage Reports</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>


                <ul class="treeview-menu">
                    @can('report_view')
                    <li class="{{ $request->segment(2) == 'reports' ? 'active active-sub' : '' }}">
                    <a href="{{ route('admin.reports.index') }}">
                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                        <span class="title">Custom Reports</span>
                    </a>
                    </li>

                    <li class="{{ $request->segment(2) == 'reports' ? 'active active-sub' : '' }}">
                    <a href="{{ route('admin.reports.weekly_projected_hours') }}">
                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                        <span class="title">Weekly Projected Hours</span>
                    </a>
                    </li>
                    @endcan
                    @if (Gate::allows('isAdmin') || Gate::allows('isCorporate'))
                        <li class="{{ $request->segment(2) == 'unscheduled-stores' ? 'active active-sub' : '' }}">
                        <a href="{{ route('admin.reports.unscheduled_store_list_view') }}">
                            <i class="fa fa-angle-right" aria-hidden="true"></i>
                            <span class="title">Stores Not Scheduled</span>
                        </a>
                        </li>
                    @endcan
                </ul>
            </li>
            @endcan

            
            @can('user_management_access')
            <li class="treeview <?php if($request->segment(2) == 'users' || $request->segment(2) == 'permissions' || $request->segment(2) == 'roles') echo 'active';?>">
                <a href="#">
                    <i class="fa fa-user user_icon" aria-hidden="true"></i>
                    <span class="title">@lang('global.user-management.title')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                @can('role_access')
                <li class="{{ $request->segment(2) == 'roles' ? 'active active-sub' : '' }}">
                        <a href="{{ route('admin.roles.index') }}">
                            <i class="fa fa-angle-right" aria-hidden="true"></i>
                            <span class="title">
                                @lang('global.roles.title')
                            </span>
                        </a>
                    </li>
                @endcan

                @can('user_access')
                <li class="{{ $request->segment(2) == 'users' ? 'active active-sub' : '' }}">
                        <a href="{{ route('admin.users.index') }}">
                            <i class="fa fa-angle-right" aria-hidden="true"></i>
                            <span class="title">
                                @lang('global.users.title')
                            </span>
                        </a>
                    </li>
                @endcan

                @can('permission_access')
                <li class="{{ $request->segment(2) == 'permissions' ? 'active active-sub' : '' }}">
                        <a href="{{ route('admin.permissions.index') }}">
                            <i class="fa fa-angle-right" aria-hidden="true"></i>
                            <span class="title">
                                @lang('global.permissions.title')
                            </span>
                        </a>
                    </li>
                @endcan
                </ul>
            </li>
            @endcan
            
<!--            @can('city_view')
            <li class="{{ $request->segment(2) == 'cities' ? 'active' : '' }}">
                <a href="{{ route('admin.cities.index') }}">
                    <div class="division_icon"></div>
                    <span class="title">Manage City</span>
                </a>
            </li>
            @endcan-->

            <li class="{{ $request->segment(1) == 'change_password' ? 'active' : '' }}">
                <a href="{{ route('auth.change_password') }}">
                    <div class="password_icon"></div>
                    <span class="title">Change password</span>
                </a>
            </li>

            <li>
                <a href="#logout" onclick="$('#logout').submit();">
                    <div class="logout_icon"></div>
                    <span class="title">@lang('global.app_logout')</span>
                </a>
            </li>
            
        </ul>
    </section>
<!--    <div style=" background-color:#3c8dbc;padding:15px 5px;width: 100%; position: fixed;bottom:0;left: 0;">
        <span style=" font-weight: bold; color: #FFF; font-size: 14px;">MSI</span>
    </div>-->
</aside>
{!! Form::open(['route' => 'auth.logout', 'style' => 'display:none;', 'id' => 'logout']) !!}
<button type="submit">@lang('global.logout')</button>
{!! Form::close() !!}
