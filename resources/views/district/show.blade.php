@extends ('layouts.backend')
@section('title',' Local Body List ')
@section('heading','Local Body')
@section('small_heading','List')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li class="active"><strong>View All Districts</strong></li>
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
    <div class="row">
        <div class="col-md-6">
            <div class="box box-default ">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <a class="btn btn-success btn-xs" href={{route('district.index')}}>Available
                            Districts</a>
                    </h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">

                    {!! Form::open(['route' => ['district.local_body_store',$id,],'method'=>'post','class'=>'form-horizontal']) !!}
                    <div class="form-group">
                        {!! form::label('name','Local Body Name',['class'=>'col-sm-2 control-label']) !!}

                        <div class="col-sm-10">
                            {!! form::text('name','',['class'=>'form-control input-sm','autocomplete'=>'off','id'=>'name','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}
                            @if($errors->has('name'))
                                <span class="label label-danger">{{$errors->first('name')}} </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        {!! form::label('body_type',':yflgosf] k|sf/',['class'=>'col-sm-2 control-label','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;'])!!}
                        <div class="col-sm-10">
                            {!! form::select('body_type',['===================================='=>'=========================','dxfgu/kflnsf' => 'dxfgu/kflnsf', 'pkdxfgu/kflnsf' => 'pkdxfgu/kflnsf','gu/kflnsf'=>'gu/kflnsf','ufp¤kflnsf'=>'ufp¤kflnsf','uf=la=;'=>'uf=la=;'], 'gu/kflnsf',['class'=>'form-control input-sm','id'=>'localtype','placeholder'=>'','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}
                            @if($errors->has('body_type'))
                                <span class="label label-danger">{{$errors->first('body_type')}} </span>
                            @endif
                        </div>
                    </div>
                    <div class="box-footer">
                        {!! Form::submit('Save',['class'=>'btn btn-primary btn-md pull-right']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="col-md-6">
        </div>
    </div>
    <div class="row">

        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Local Bodies of <b
                                style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; font-size: 16px; color: blue; ">{{App\District::find($id)->name}}</b>
                        District</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th>SN</th>
                            <th>Local Body</th>
                            <th>Body Type</th>
                            <th>Created By</th>
                            <th>Updated By</th>
                            <th>Created Date</th>
                            <th>Updated Date</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php($i=1)
                        @foreach($local_body as $l)
                            <tr>
                                <td>{{$i++}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;">{{$l->name}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;">{{$l->body_type}}</td>
                                <td>{{App\User::find($l->created_by)->name}}</td>
                                <td>@if(isset($l->updated_by)) {{App\User::find($l->updated_by)->name}}
                                    @else
                                        Wasn't Updated Yet !!
                                    @endif</td>
                                <td>{{$l->created_at}}</td>
                                <td>{{$l->updated_at}}</td>
                                <td>
                                    <div class="btn-group">
                                        <a class="btn btn-primary  btn-xs dropdown-toggle" data-toggle="dropdown"
                                           href="#">
                                            <i class="fa fa-user fa-fw"></i>
                                            Action <span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="{{route('local_body.edit',[$l->id,$id])}}"
                                                   class=" btn-xs"> <i class="fa fa-pencil fa-fw"></i>Edit</a>
                                            </li>
                                            <li>
                                                <a href="">
                                                    <form action="{{route('local_body.destroy',[$l->id,$l->district_id])}}"
                                                          method="post">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="_method" value="delete">
                                                        <button type="submit" class="btn btn-default btn-xs"
                                                                style="padding: 0; outline:none;border: none;background: none;"
                                                                onclick="return confirm('Are you sure to delete?')">
                                                            <i class="fa fa-trash fa-fw"></i> Delete
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
                        <th>SN</th>
                        <th>Local Body</th>
                        <th>Body Type</th>
                        <th>Created By</th>
                        <th>Updated By</th>
                        <th>Created Date</th>
                        <th>Updated Date</th>
                        <th>Action</th>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
