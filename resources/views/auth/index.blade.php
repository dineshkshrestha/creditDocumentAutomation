@extends ('layouts.backend')

@section('title',' Users List ')
@section('heading','Users')
@section('small_heading','List')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li class="active"><strong>View All Users</strong></li>

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
            <h3 class="box-title"><a class="btn btn-success btn-xs" href={{route('userregister.create')}}>Create
                    User</a></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table id="permission" class="table table-striped table-bordered table-hover dataTables-example">
                <thead>
                <tr>
                    <th>SN</th>
                    <th>Name</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Post</th>
                    <th>Branch</th>
                    <th>Role</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @php($i=1)
                @foreach($user as $c)
                    <tr>
                        <td>{{$i++}}</td>
                        <td>{{$c->name}}</td>
                        <td>{{$c->username}}</td>
                        <td>{{$c->email}}</td>
                        <td>{{$c->post}}</td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{\App\Branch::find($c->branch)->location}}</td>
                        <td>{{ ucwords($c->user_type)}}</td>
                        <td><img height="50" weight="50" src="{{asset('assets/backend/dist/img/'.$c->image)}}"></td>
                        <td>

                            @if ($c->status==1)
                                <span class="label label-success">Active</span>
                            @else
                                <span class="label label-danger">Deactivated</span>
                            @endif

                        </td>
                        <td>
                      <span>
                          <a href="{{route('userregister.edit',$c->id)}}" class="btn btn-warning btn-xs">Edit</a>
                            <a href="{{route('userregister.show',$c->id)}}" class="btn btn-success btn-xs">View Details</a>
                      </span>

                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th>SN</th>
                    <th>Name</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Post</th>
                    <th>Branch</th>
                    <th>Role</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </tfoot>
            </table>

        </div>
    </div>
    <!-- /.box-body -->

@endsection