<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title')|DAS||Civil Bank LTD</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('assets/image/icon.png')}}"/>
    <meta name="csrf-token" content="{{csrf_token()}} ">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{asset('assets/backend/bower_components/bootstrap/dist/css/bootstrap.min.css')}}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('assets/backend/bower_components/font-awesome/css/font-awesome.min.css')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{asset('assets/backend/bower_components/Ionicons/css/ionicons.min.css')}}">
    <!-- Theme style -->
    {{--<link rel="stylesheet" href="{{asset('assets/backend/plugins/iCheck/all.css')}}">--}}
    <link rel="stylesheet" href="{{asset('assets/backend/dist/css/AdminLTE.min.css')}}">
    <script type="application/x-javascript"> addEventListener("load", function () {
            setTimeout(hideURLbar, 0);
        }, false);
        function hideURLbar() {
            window.scrollTo(0, 1);
        } </script>

    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{asset('assets/backend/dist/css/skins/_all-skins.min.css')}}">
    <!-- Morris chart -->
    {{--<link rel="stylesheet" href="{{asset('assets/backend/bower_components/morris.js/morris.css')}}">--}}
<!-- jvectormap -->
    {{--<link rel="stylesheet" href="{{asset('assets/backend/bower_components/jvectormap/jquery-jvectormap.css')}}">--}}
<!-- Date Picker -->
    {{--<link rel="stylesheet"--}}
    {{--href="{{asset('assets/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')}}">--}}
    {{--<!-- Daterange picker -->--}}
    {{--<link rel="stylesheet"--}}
    {{--href="{{asset('assets/backend/bower_components/bootstrap-daterangepicker/daterangepicker.css')}}">--}}
<!-- bootstrap wysihtml5 - text editor -->
    {{--<link rel="stylesheet" href="{{asset('assets/backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')}}">--}}
    <link rel="stylesheet" href="{{asset('assets/backend/plugins/pace/pace.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/plugins/toastr/build/toastr.min.css')}}">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>


    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <style>
        .borrower {
            color: orangered;
        }
    </style>

    @yield('css')
</head>
<body class="hold-transition skin-purple-light sidebar-mini">
<div class="wrapper">

    @php
        if(Auth::user()->user_type=='admin' || Auth::user()->user_type=='checker' ){
$spersonal=App\PersonalLoan::where([
['document_status','Submitted']
])->get();

$scorporate=App\CorporateLoan::where([
['document_status','Submitted']
])->get();
$sjoint=App\JointLoan::where([
['document_status','Submitted']
])->get();
        }else{
        $check_branch=Auth::user()->branch;

$apersonal=App\PersonalLoan::where([
['document_status','Approved'],['branch_id',$check_branch]
])->get();
$acorporate=App\CorporateLoan::where([
['document_status','Approved'],['branch_id',$check_branch]
])->get();
$ajoint=App\JointLoan::where([
['document_status','Approved'],['branch_id',$check_branch]
])->get();

$rpersonal=App\PersonalLoan::where([
['document_status','Rejected'],['branch_id',$check_branch]
])->get();
$rcorporate=App\CorporateLoan::where([
['document_status','Rejected'],['branch_id',$check_branch]
])->get();
$rjoint=App\JointLoan::where([
['document_status','Rejected'],['branch_id',$check_branch]
])->get();
        }
    @endphp


    <header class="main-header">
        <!-- Logo -->
        <a href="{{route('home')}}" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>D</b>AS</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>Document</b>AS</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                @if(Auth::user()->user_type=='user')
                    <!-- Notifications: style can be found in dropdown.less -->
                        <li class="dropdown notifications-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bell-o"></i>
                                @if($apersonal || $acorporate || $ajoint || $rjoint || $rpersonal ||$corporate)
                                    <span class="label label-warning">

                                        {{count($apersonal)+count($rpersonal)+count($acorporate)+count($rcorporate)+count($ajoint)+count($rjoint)}}

                            </span>
                                @endif
                            </a>
                            <ul class="dropdown-menu">

                                <li class="header">You have
                                    @if($apersonal || $acorporate || $ajoint || $rjoint || $rpersonal ||$corporate)
                                        {{count($apersonal)+count($rpersonal)+count($acorporate)+count($rcorporate)+count($ajoint)+count($rjoint)}}
                                    @else
                                        0
                                    @endif

                                    notifications
                                </li>


                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">

                                        @if($apersonal || $acorporate || $ajoint || $rjoint || $rpersonal ||$corporate)
                                            @foreach($apersonal as $d)
                                                <li>
                                                    <a style="color:forestgreen;"
                                                       href="{{route('personal.choose',$d->borrower_id)}}">
                                                        <i class="fa fa-user text-aqua"></i>
                                                        {{App\PersonalBorrower::find($d->borrower_id)->english_name}}
                                                        's Documents Approved.
                                                        Download Now

                                                    </a>
                                                </li>
                                            @endforeach
                                            @foreach($acorporate as $d)
                                                <li>
                                                    <a href="{{route('corporate.choose',$d->borrower_id)}}">
                                                        <i class="fa fa-user text-aqua"></i>
                                                        {{App\CorporateBorrower::find($d->borrower_id)->english_name}}
                                                        's Documents Approved.
                                                        Download Now

                                                    </a>
                                                </li>
                                            @endforeach
                                            @foreach($ajoint as $d)
                                                <li>
                                                    <a href="{{route('joint.choose',$d->borrower_id)}}">
                                                        <i class="fa fa-user text-aqua"></i>
                                                        Joint Borrower Documents Approved.
                                                        Download Now

                                                    </a>
                                                </li>
                                            @endforeach
                                            @foreach($rpersonal as $d)
                                                <li>
                                                    <a style="color:red;"
                                                       href="{{route('personal.rejected',$d->borrower_id)}}">
                                                        <i class="fa fa-user text-aqua"></i>
                                                        {{App\PersonalBorrower::find($d->borrower_id)->english_name}}
                                                        's Documents Rejected. click to view details.

                                                    </a>
                                                </li>
                                            @endforeach
                                            @foreach($rcorporate as $d)
                                                <li>
                                                    <a style="color:red;"
                                                       href="{{route('corporate.rejected',$d->borrower_id)}}">
                                                        <i class="fa fa-user text-aqua"></i>
                                                        {{App\CorporateBorrower::find($d->borrower_id)->english_name}}
                                                        's Documents Rejected. click to view details.

                                                    </a>
                                                </li>
                                            @endforeach
                                            @foreach($rjoint as $d)
                                                <li>
                                                    <a style="color:red;"
                                                       href="{{route('joint.rejected',$d->borrower_id)}}">
                                                        <i class="fa fa-user text-aqua"></i>
                                                        Joint Document Documents Rejected. click to view details.
                                                    </a>
                                                </li>
                                            @endforeach

                                        @endif
                                    </ul>
                                </li>
                                {{--<li class="footer"><a href="#">View all</a></li>--}}
                            </ul>

                        </li>
                @endif
                @if(Auth::user()->user_type=='admin'|| Auth::user()->user_type=='checker')
                    <!-- Tasks: style can be found in dropdown.less -->
                        <li class="dropdown tasks-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-flag-o"></i>
                                @if($spersonal || $scorporate || $sjoint)
                                    <span class="label label-danger">
                                        {{count($spersonal) + count($scorporate) + count($sjoint)}}
                                    </span>
                                @endif

                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">
                                    You have
                                    @if($spersonal || $scorporate || $sjoint)
                                        {{count($spersonal) + count($scorporate) + count($sjoint)}}
                                    @else 0
                                    @endif tasks
                                </li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                        @foreach ($spersonal as $d)
                                            <li><!-- Task item -->
                                                <a style="font-size: 11px; color:black;"
                                                   href="{{route('personal.approve',$d->borrower_id)}}">
                                                    <i class="fa fa-user text-aqua"></i>
                                                    {{App\PersonalBorrower::find($d->borrower_id)->english_name}},
                                                    {{App\PersonalLoan::where('borrower_id',$d->borrower_id)->first()->updated_at}}
                                                   

                                                </a>
                                            </li>
                                        @endforeach
                                        @foreach ($scorporate as $d)
                                            <li><!-- Task item -->
                                                <a style="font-size: 11px; color:black;"
                                                   href="{{route('corporate.approve',$d->borrower_id)}}">
                                                    <i class="fa fa-users text-aqua"></i>
                                                    {{App\CorporateBorrower::find($d->borrower_id)->english_name}}
  {{App\CorporateLoan::where('borrower_id',$d->borrower_id)->first()->updated_at}}
                                                                                                      

                                                </a>
                                            </li>
                                        @endforeach
                                        @foreach ($sjoint as $d)
                                            <li><!-- Task item -->
                                                <a style="font-size: 11px; color:black;"
                                                   href="{{route('joint.approve',$d->borrower_id)}}">
                                                    <i class="fa fa-user text-aqua"></i>
                                                    Joint Borrower Documents Approved.
                                                    's Documents approval request.Click to view details.
                                                </a>
                                            </li>
                                    @endforeach
                                    <!-- end task item -->
                                        <!-- end task item -->
                                    </ul>
                                </li>
                                <li class="footer">
                                    {{--<a href="#">View all tasks</a>--}}
                                </li>
                            </ul>
                        </li>
                    @endif

                    <li class="dropdown messages-menu">
                        <a href="{{route('document.make_document')}}">
                            <i class="fa fa-download"></i>
                        </a>
                    </li>


                    {{--<!-- User Account: style can be found in dropdown.less -->--}}
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="{{asset('assets/backend/dist/img/'.Auth::user()->image)}}" class="user-image"
                                 alt="User Image">
                            <span class="hidden-xs">{{Auth::user()->name}}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="{{asset('assets/backend/dist/img/'.Auth::user()->image)}}" class="img-circle"
                                     alt="User Image">

                                <p>
                                    {{Auth::user()->name}} - {{Auth::user()->post}}
                                    <small style="font-family: Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{\App\Branch::find(Auth::user()->branch)->location}}
                                        zfvf
                                    </small>
                                </p>
                            </li>
                            <!-- Menu Body -->
                            <li class="user-body">
                                <div class="row">
                                    <div class="col-xs-6 text-right">
                                        <b> User Type:
                                        </b></div>
                                    <div class="col-xs-6 text-left">
                                    <span style="color: brown">    {{strtoupper(Auth::user()->user_type)}}
                                   </span></div>
                                    {{--<div class="col-xs-4 text-center">--}}
                                    {{--<a href="#">Friends</a>--}}
                                    {{--</div>--}}
                                </div>
                                <!-- /.row -->
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="{{route('user.profile')}}"><i class="fa fa-user"></i>Profile </a>
                                </div>
                                <div class="pull-right">
                                    <a href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fa fa-sign-out"></i>Log out
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                          style="display: none;">
                                        {{ csrf_field() }}
                                    </form>


                                </div>
                            </li>
                        </ul>
                    </li>
                    <!-- Control Sidebar Toggle Button -->
                    <li>
                        <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="{{asset('assets/backend/dist/img/'.Auth::user()->image)}}" class="img-circle"
                         alt="User Image">
                </div>
                <div class="pull-left info">
                    <p>{{Auth::user()->name}}</p>
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>
            <!-- search form -->
            <form action="#" method="get" class="sidebar-form">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Search...">
                    <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
                </div>
            </form>
            <!-- /.search form -->
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu" data-widget="tree">

                <li class="header">BORROWER</li>

                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-child"></i> <span>New Borrower</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">

                        <li><a href="{{route('personal_borrower.index')}}"> <i class="fa fa-users"></i>
                                <span>Personal Borrower</span></a></li>
                        <li><a href="{{route('joint_borrower.index')}}"> <i class="fa fa-cubes"></i>
                                <span>Personal Joint Borrower</span></a></li>
                        <li><a href="{{route('corporate_borrower.index')}}"> <i class="fa fa-laptop"></i>
                                <span>Corporate Borrower</span></a></li>

                    </ul>
                </li>


                <li class="header">QUICK DOCUMENT</li>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-wheelchair"></i> <span>Borrower Type</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="{{route('review.personal_index')}}"><i class="fa fa-user"></i></i>Personal</a>
                        </li>
                        <li><a href="{{route('review.corporate_index')}}"><i class="fa fa-laptop"></i>Corporate</a>
                        </li>
                        <li><a href="{{route('review.joint_index')}}"><i class="fa fa-users"></i>Joint</a>
                        </li>
                    </ul>
                </li>


                <li class="header">VIEW, EDIT, UPDATE, DELETE</li>

         <!--        <li class="treeview">
                    <a href="#">
                        <i class="fa fa-share"></i> <span>Quick Edit</span>
                        <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">

                        <li class="treeview">
                            <a href="#"><i class="fa fa-users"></i> Personal
                                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="{{route('PersonalBorrower.index')}} " target="_blank">
                                        <i class="fa fa-circle-o"></i> Borrower</a></li>
                                <li><a href="{{route('PersonalGuarantor.index')}}" target="_blank"><i
                                                class="fa fa-circle-o"></i> Guarator</a></li>
                                <li><a href="{{route('PersonalPropertyOwner.index')}}" target="_blank"><i
                                                class="fa fa-circle-o"></i> Property Owner</a></li>
                            </ul>
                        </li>


                        <li class="treeview">
                            <a href="#"><i class="fa fa-laptop"></i> Corporate
                                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="{{route('CorporateBorrower.index')}}" target="_blank"><i
                                                class="fa fa-circle-o"></i> Borrower</a></li>
                                <li><a href="{{route('CorporateGuarantor.index')}}" target="_blank"><i
                                                class="fa fa-circle-o"></i> Guarator</a></li>
                                <li><a href="{{route('CorporatePropertyOwner.index')}}" target="_blank"><i
                                                class="fa fa-circle-o"></i> Property Owner</a></li>

                            </ul>
                        </li>


                    </ul> -->


                </li>
                <li><a href="{{route('document.index')}}"> <i class="fa fa-download"></i>
                        <span>Download</span></a></li>
               <!--  <li><a href="{{route('dispatch.index')}}"> <i class="fa  fa-external-link"></i>
                        <span>Dispatch Book</span></a></li>
 -->

                @if(Auth::user()->user_type=='admin')
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-file-pdf-o"></i> <span>Reports</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="{{route('report.create','approved')}}" target="_blank"><i class="fa fa-circle-o"></i>Approved</a>
                            </li>
                            <li><a href="{{route('report.create','submitted')}}" target="_blank"><i class="fa fa-circle-o"></i>Submitted</a>
                            </li>
                            <li><a href="{{route('report.create','rejected')}}" target="_blank"><i class="fa fa-circle-o"></i>Rejected</a>
                            </li>


                        </ul>
                    </li>
                @endif
                @if(Auth::user()->user_type=='admin')
                    <li class="header">SETTINGS</li>
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-cog"></i> <span>General Settings</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="{{route('branch.index')}}" target="_blank"><i class="fa fa-circle-o"></i>Branch</a>
                            </li>
                            <li><a href="{{route('province.index')}}" target="_blank"><i class="fa fa-circle-o"></i>Province</a>
                            </li>
                            <li><a href="{{route('district.index')}}" target="_blank"><i class="fa fa-circle-o"></i>District</a>
                            </li>
                            <li><a href="{{route('department.index')}}" target="_blank"><i class="fa fa-circle-o"></i>Department</a>
                            </li>
                            <li><a href="{{route('ministry.index')}}" target="_blank"><i class="fa fa-circle-o"></i>Ministry</a>
                            </li>
                            <li><a href="{{route('facility.index')}}" target="_blank"><i class="fa fa-circle-o"></i>Facility</a>
                            </li>
                            <li><a href="{{route('registered_company.index')}}" target="_blank"><i
                                            class="fa fa-circle-o"></i>Registered Company</a>
                            </li>

                        </ul>
                    </li>
                @endif

                {{--<li class="header">EXTRA FEATURES</li>--}}

                {{--<li class="treeview">--}}
                {{--<a href="#">--}}
                {{--<i class="fa fa-file-pdf-o"></i> <span>PDF</span>--}}
                {{--<i class="fa fa-angle-left pull-right"></i>--}}
                {{--</a>--}}
                {{--<ul class="treeview-menu">--}}
                {{--<li><a href="{{route('dinesh_shrestha_pdf_merge.create')}}"><i class="fa fa-compress"></i>Merge--}}
                {{--PDF</a>--}}
                {{--</li>--}}
                {{--<li><a href="{{route('province.index')}}" target="_blank"><i class="fa fa-circle-o"></i>Province</a>--}}
                {{--</li>--}}

                {{--</ul>--}}
                {{--</li>--}}


            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                @yield('heading')
                <small>@yield('small_heading')</small>
                <small class="borrower">@yield('borrower')</small>
            </h1>
            <ol class="breadcrumb">

                @yield('breadcrumb')
                {{--<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>--}}
                {{--<li class="active">Dashboard</li>--}}
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            @yield('content')

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            {{--<strong>Copyright &copy; 2014-2016 <a href="https://adminlte.io">Almsaeed Studio</a>.</strong> All rights--}}
            {{--reserved.--}}
            <strong>&copy 2018 Document Automation System v.1.2. All Rights Reserved | Developed by <a
                        href="https://www.shresthadinesh.com.np" target="_blank">Dinesh K. Shrestha</a>

            </strong>
        </div>
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Create the tabs -->

<h3>User Settings</h3>



        <!-- Tab panes -->
        <div class="tab-content">
            <!-- Home tab content -->
            <div>
                @if(Auth::user()->user_type=='admin')
                    <a href="{{route('userregister.create')}}"><i class="fa fa-user-plus"></i> <span>Add New User</span></a>
                    <hr>
                    <a href="{{route('userregister.index')}}"><i class="fa fa-eye"></i> <span>View User Details </span></a>

                    <hr>
                    <a href="{{route('activity.view_all')}}"><i class="fa fa-eye"></i>
                        <span>View All Activity Log</span></a>
                    <hr>
                @endif
                <a href="{{route('activity.view',Auth::user()->id)}}"><i class="fa fa-eye"></i> <span>View Your Activity Log</span></a>
                <hr>
            </div>


            {{--<!-- /.tab-pane -->--}}
            <!-- Stats tab content -->
            <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
            <!-- /.tab-pane -->
            <!-- Settings tab content -->
            <!-- /.tab-pane -->
        </div>
    </aside>
    <!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg ">

    </div>
</div>
<!-- ./wrapper -->
<!-- jQuery 3 -->
<script src="{{asset('assets/backend/bower_components/jquery/dist/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{asset('assets/backend/bower_components/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="{{asset('assets/backend/bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>
<!-- Morris.js charts -->

<script src="{{asset('assets/backend/bower_components/PACE/pace.min.js')}}"></script>
<script src="{{asset('assets/backend/bower_components/raphael/raphael.min.js')}}"></script>
<script src="{{asset('assets/backend/bower_components/morris.js/morris.min.js')}}"></script>
<!-- Sparkline -->
{{--<script src="{{asset('assets/backend/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js')}}"></script>--}}
<!-- jvectormap -->
{{--<script src="{{asset('assets/backend/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>--}}
{{--<script src="{{asset('assets/backend/plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>--}}
<!-- jQuery Knob Chart -->
{{--<script src="{{asset('assets/backend/bower_components/jquery-knob/dist/jquery.knob.min.js')}}"></script>--}}
<!-- daterangepicker -->
{{--<script src="{{asset('assets/backend/bower_components/moment/min/moment.min.js')}}"></script>--}}
{{--<script src="{{asset('assets/backend/bower_components/bootstrap-daterangepicker/daterangepicker.js')}}"></script>--}}
<!-- datepicker -->
{{--<script src="{{asset('assets/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')}}"></script>--}}
<!-- Bootstrap WYSIHTML5 -->
{{--<script src="{{asset('assets/backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}"></script>--}}
<!-- Slimscroll -->
{{--<script src="{{asset('assets/backend/bower_components/jquery-slimscroll/jquery.slimscroll.min.js')}}"></script>--}}
<!-- FastClick -->
{{--<script src="{{asset('assets/backend/bower_components/fastclick/lib/fastclick.js')}}"></script>--}}
<!-- AdminLTE App -->
<script src="{{asset('assets/backend/dist/js/adminlte.min.js')}}"></script>
{{--<!-- AdminLTE dashboard demo (This is only for demo purposes) -->--}}
{{--<script src="{{asset('assets/backend/dist/js/pages/dashboard.js')}}"></script>--}}
{{--<!-- AdminLTE for demo purposes -->--}}
{{--<script src="{{asset('assets/backend/dist/js/demo.js')}}"></script>--}}
<script src="{{asset('assets/backend/plugins/toastr/build/toastr.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-bottom-full-width",
            "preventDuplicates": false,
            "showDuration": "4000",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
        @if(\Session::has('danger'))
toastr.error("{{ Session::get('danger') }}");
        @endif
        @if(\Session::has('warning'))
toastr.warning("{{ Session::get('warning') }}");
        @endif
        @if(\Session::has('success'))
toastr.success("{{ Session::get('success') }}");
        @endif

    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $('#search').keyup(function () {
            var key = $(this).val();
            $.ajax({
                url: '/selectModule',
                data: {'key': key},
                method: 'post',
                success: function (resp) {
                    $('.module').html(resp);
                }
            })
        })
    });
</script>


{{--add more--}}
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#search').keyup(function () {
            var key = $(this).val();
            $.ajax({
                url: '/selectModule',
                data: {'key': key},
                method: 'post',
                success: function (resp) {
                    $('.module').html(resp);
                }
            })
        })
    });
    $(document).ready(function () {
        //here first get the contents of the div with name class copy-fields and add it to after "after-add-more" div class.
        $(".add-more").click(function () {
            var html = $(".copy-fields").html();
            $(".after-add-more").after(html);
        });
//here it will remove the current value of the remove button which has been pressed
        $("body").on("click", ".remove", function () {
            $(this).parents(".control-group").remove();
        });
    });

    $(document).ready(function () {
        //here first get the contents of the div with name class copy-fields and add it to after "after-add-more" div class.
        $(".add-more1").click(function () {
            var html = $(".copy-fields1").html();
            var repid = $(html).attr('id');
            var n = parseInt(repid.substr(7));
            var nrep = 'attrep_' + (n + 1);
            var nattribute_id = 'attribute_id_' + (n + 1);
            var nattribute_value_id = 'attribute_value_id_' + (n + 1);
            $('#att>div').attr('id', nrep);
            $('#att>div>.col-xs-5>select').attr('id', nattribute_id);
            $('#att>div>.col-xs-6>select').attr('id', nattribute_value_id);
            $(".after-add-more1").after(html);
        });
//here it will remove the current value of the remove button which has been pressed
        $("body").on("click", ".remove", function () {
            $(this).parents(".control-group1").remove();
        });
    });
</script>


@yield('js')

</body>
</html>
