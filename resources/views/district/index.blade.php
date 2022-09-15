@extends ('layouts.backend')
@section('title',' District List ')
@section('heading','district')
@section('small_heading','List')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li class="active"><strong>View All District</strong></li>
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
            <h3 class="box-title"><a class="btn btn-success btn-xs" href={{route('district.create')}}>Create
                    District</a></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table id="permission" class="table table-striped table-bordered table-hover dataTables-example">
                <thead>
                <tr>
                    <th>SN</th>
                    <th>District Name</th>
                    <th>Province</th>
                    <th>Created By</th>
                    <th>Updated By</th>
                    <th>Created Date</th>
                    <th>Updated Date</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @php($i=1)
                @foreach($district as $a)
                    <tr>
                        <td>{{$i++}}</td>

                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->name}}</td>

                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{App\Province::find($a->province_id)->name}}</td>
                        <td> {{App\User::find($a->created_by)->name}}</td>
                        <td>@if(isset($a->updated_by)) {{App\User::find($a->updated_by)->name}}
                            @else
                                Wasn't Updated Yet !!
                            @endif
                        </td>
                        <td>{{$a->created_at}}</td>
                        <td>{{$a->updated_at}}</td>
                        <td width="150">
                            <div class="btn-group">
                                <a class="btn btn-primary  btn-xs dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-user fa-fw"></i>
                                    Action <span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
                                </a>
                                <ul class="dropdown-menu">
                                  <li><a href="{{route('district.show',$a->id)}}" class=" btn-xs">View Local Bodies</a></li>
                                    <li>
                                        <a href="{{route('district.edit',$a->id)}}" class="btn-xs"> <i class="fa fa-pencil fa-fw"></i>Edit</a>
                                    </li>
                                    <li>

                                        <a href="">
                                            <form action="{{route('district.destroy',$a->id)}}" method="post">
                                                {{csrf_field()}}

                                                <input type="hidden" name="_method" value="delete">
                                                <button type="submit" class="btn btn-default btn-xs"
                                                        style="padding: 0; outline:none;border: none;background: none;"
                                                        onclick="return confirm('Are you sure to delete?')"> <i class="fa fa-trash fa-fw"></i>Delete
                                                </button>
                                            </form>
                                        </a>


                                    </li>
                                </ul>
                            </div>


                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th>SN</th>
                    <th>District Name</th>
                    <th>Province</th>
                    <th>Created By</th>
                    <th>Updated By</th>
                    <th>Created Date</th>
                    <th>Updated Date</th>
                    <th>Action</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <!-- /.box-body -->

@endsection