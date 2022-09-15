@extends ('layouts.backend')

@section('title',' Users  ')
@section('heading','Users')
@section('small_heading','List')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li class="active"><strong>View Profile</strong></li>

@endsection

@section('css')
    <link rel="stylesheet"
          href="{{asset('assets/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
@endsection

@section('js')
    <script src="{{asset('assets/backend/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
    <script>

        $(document).ready(function () {
            $('#permission').DataTable();
        });
    </script>
@endsection

@section('content')

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Your Profile</h3>
        </div>
        <section class="content">
            <div class="row">
                <div class="col-md-4">

                    <!-- Profile Image -->
                    <div class="box box-primary">
                        <div class="box-body box-profile">
                            <img class="profile-user-img img-responsive img-circle" src="{{asset('assets/backend/dist/img/'.$user->image)}}" alt="User profile picture">

                            <h3 class="profile-username text-center">{{$user->username}}</h3>

                            <p class="text-muted text-center">{{$user->post}}</p>

                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item">
                                    <b>Name</b> <a class="pull-right">{{$user->name}}</a>
                                </li> <li class="list-group-item">
                                    <b>Email</b> <a class="pull-right">{{$user->email}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Branch</b> <a class="pull-right" style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{\App\Branch::find($user->branch)->location}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>User Type</b> <a class="pull-right">{{ ucwords($user->user_type)}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Status</b> <a class="pull-right">                            @if ($user->status==1)
                                            <span class="label label-success">Active</span>
                                        @else
                                            <span class="label label-danger">Deactivated</span>
                                        @endif
                                    </a>
                                </li>

                            </ul>

                            <a href="{{route('user.change_password',$user->id)}}" class="btn btn-primary btn-block"><b>Change Password</b></a>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->

                    <!-- About Me Box -->

                    <!-- /.box -->
                </div>
                <!-- /.col -->

                <!-- /.col -->
            </div>
            <!-- /.row -->








</section></div>

    <!-- /.box-body -->

@endsection