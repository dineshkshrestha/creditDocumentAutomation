@extends ('layouts.backend')

@section('title',' Activity log ')
@section('heading','Activity')
@section('small_heading','Log')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li class="active"><strong>Activity Log</strong></li>
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
        <!-- /.box-header -->
        <div class="box-body">

            <table id="permission" class="table table-striped table-bordered table-hover dataTables-example">
                <thead>
                <tr>
                    <th>SN</th>
                    <th>Description</th>
                    <th>User</th>
                    <th>Date&time</th>
                    <th>Changes Details</th>
                </tr>
                </thead>
                <tbody>
                @php($i=1)
                @foreach($activity as $a)
                    <tr>
                        <td>{{$i++}}</td>
                        <td>{{$a->description}}</td>
                        <td> @if($a->causer_id==null)
                                Anonymous
                            @else
                                {{App\User::find($a->causer_id)->name}}
                            @endif
                        </td>


                        <td>{{$a->created_at}}</td>
                                              <td style="font-size: 10px;"> {{$a->properties}}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th>SN</th>
                    <th>Description</th>
                    <th>User</th>
                    <th>Date and time</th>
                    <th>Changes Details</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <!-- /.box-body -->

@endsection