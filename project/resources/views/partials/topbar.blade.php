<header class="main-header">
    <!-- Logo -->
    <a href="{{ url('/admin/home') }}" class="logo"
       style="font-size: 16px;">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span class="hidden-xs"><?php echo auth()->user()->name;?></span>
                        <img src="{{asset('uploads/images/dashboard_icons/user.png')}}" class="dropdown-userImg" alt="User Image">
                        
                            <img src="{{asset('uploads/images/dashboard_icons/dropArrow.png')}}" class="dropdown-dropArrow" alt="drop arrow">
                    </a>
                    <ul class="dropdown-menu custom-scroll-pegasus" style="width:50%">
                        <li>
                            
                            <ul class="menu" style="overflow: hidden;">
<!--                                <li>
                                    <a href="#">
                                        <span class="glyphicon glyphicon-user text-green"></span> View Profile
                                    </a>
                                </li>-->
<!--                                <li>
                                    <a href="#">
                                        <i class="fa fa-dashboard text-info"></i>
                                        <span class="title">Dashboard</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-user text-black"></i>
                                        <span class="title">Manage Users</span>
                                    </a>
                                </li>-->
                                <li>
                                    <a href="{{ route('auth.change_password') }}">
                                        <i class="fa fa-exchange text-yellow"></i>Change Password
                                    </a>
                                </li>
                                
                                
                                <li>
                                    <a href="#logout" id ="logout1" onclick="$('#logout').submit();">
                                        <i class="fa fa-sign-out text-warning"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        

    </nav>
    
</header>


