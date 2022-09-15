@extends ('layouts.backend')
@section('title',' Corporate Property Owner List ')
@section('heading','Corporate Property Owner')
@section('small_heading','Property Owner')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li class="active"><strong>Corporate Property Owner</strong></li>
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
    <div class="col-sm-12">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">

                </h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table id="permission" class="table table-striped table-bordered table-hover dataTables-example">
                    <thead>
                    <tr>
                        <th>SN</th>
                        <th>English Name</th>
                        <th>Nepali name</th>
                        <th>clientId</th>
                        <th>Registration No.</th>
                        <th>Reg. Date</th>
                        <th>Authorised Person</th>
                        <th>Action</th>
                        <th>Status</th>

                    </tr>
                    </thead>
                    <tbody>
                    @php($i=1)
                    @foreach($propertyowner as $a)
                        <tr>@php($bid=$a->id)
                            <td>{{$i++}}</td>
                            <td>{{$a->english_name}}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->nepali_name}}</td>
                            <td>{{$a->client_id}}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->registration_number}}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->reg_day}}
                                ÷{{$a->reg_month}}÷{{$a->reg_year}}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">
                                {{$a->authorized_person_id}}
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a class="btn btn-primary  btn-xs dropdown-toggle" data-toggle="dropdown" href="#">
                                        <i class="fa fa-user fa-fw"></i>
                                        Action <span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{route('CorporatePropertyOwner.edit',$a->id)}}" class="btn-xs"> <i
                                                        class="fa fa-pencil fa-fw"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="">
                                                <form action="{{route('CorporatePropertyOwner.destroy',$a->id)}}"
                                                      method="post">
                                                    {{csrf_field()}}
                                                    <input type="hidden" name="_method" value="delete">
                                                    <button type="submit" class="btn btn-default btn-xs"
                                                            style="padding: 0; outline:none;border: none;background: none;"
                                                            onclick="return confirm('Are you sure to delete?')"><i
                                                                class="fa fa-trash fa-fw"></i>Delete
                                                    </button>
                                                </form>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td> @if($a->status==1)
                                    <label class="label label-success label-xs">Active</label>
                                @else
                                    <label class="label label-danger label-xs">Inactive</label>
                                @endif
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- /.box-body -->
@endsection
