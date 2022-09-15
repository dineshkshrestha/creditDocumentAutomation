
@extends ('layouts.backend')
@section('title','Downloads')
@section('heading','Download the Documents')
{{--@section('small_heading','')--}}
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li class="active"><strong>Download Document</strong></li>
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
            $('#document').DataTable();
        });
    </script>
@endsection
@section('content')

    <div class="col-sm-10">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Here are the list of generated documents, you can download from here.
                </h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <table id="document" class="table table-striped table-bordered table-hover dataTables-example">
                    <thead>     <tr>
                        <th>S.N</th>
                        <th>File Name</th>
                        <th>Created At</th>
                        <th>Action</th>
                        <th>Download</th>
                    </tr>
                    </thead>
                    @php($i=1)
                @foreach($files as $f)
                        <tr>
                            <td>{{$i++}}</td>
                            <td><a href="{{route('document.download',basename($f))}}">
                                    {{ basename($f)}}
                                </a></td>
                            <td>{{date ("F d Y", filemtime($f))}}</td>
                            <td>
                                <div class="btn-group">
                                    <a class="btn btn-primary  btn-xs dropdown-toggle" data-toggle="dropdown" href="#">
                                        <i class="fa fa-user fa-fw"></i>
                                        Action <span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="">
                                                <form action="{{route('document.delete',basename($f))}}" method="post">
                                                    {{csrf_field()}}

                                                    <input type="hidden" name="_method" value="delete">
                                                    <button type="submit" value="Delete Document" class="btn btn-default btn-xs"
                                                            style="padding: 0; outline:none;border: none;background: none;"
                                                            onclick="return confirm('Are you sure to remove?')"><i
                                                                class="fa fa-trash fa-fw"></i>Delete Document
                                                    </button>
                                                </form>
                                            </a>


                                        </li>
                                    </ul>
                                </div>


                            </td>
                            <td width="110"><a href="{{route('document.download',basename($f))}}"><i
                                            class="fa fa-download fa-fw"></i>Download</a></td>
                        </tr>
                    @endforeach


                </table>
                <div class="box-footer">


                </div>


            </div>
        </div>
    </div>
@endsection
