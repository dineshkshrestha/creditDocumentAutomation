@extends ('layouts.backend')

@section('title',' District Create ')
@section('heading','District')
@section('small_heading','Create')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li><a href="{{route('district.index')}}">View All Districts</a></li>
    <li class="active"><strong>Create District</strong></li>
@endsection
@section('css')
    <style>
        td {
            padding-right: 5px;
        }

        th {
            padding-right: 5px;
        }
    </style>

@endsection

@section('content')
    <div class="col-md-6">
        <div class="box box-default ">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <a class="btn btn-success btn-xs"
                       href={{route('district.index')}}>Available Districts</a>
                </h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                {!! Form::open(['route' => 'district.store','method'=>'post','class'=>'form-horizontal']) !!}
                <table>
                    <tr>
                        <td> {!! form::label('province_id','प्रदेश काे नाम',['class'=>'control-label']) !!}</td>
                        <td> {!! form::label('name','जिल्लाकाे नाम',['class'=>'control-label']) !!}</td>
                    </tr>
                    <tr>
                        <td>  {!! Form::select('province_id',$province,'', ['class'=>'form-control input-sm','id'=>'province_id','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}

                        </td>
                        <td> {!! form::text('name','',['class'=>'form-control input-sm','id'=>'name','placeholder'=>'','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}
                            @if($errors->has('name'))
                                <span class="label label-danger">{{$errors->first('name')}} </span>
                            @endif
                        </td>
                    </tr>
                </table>
                <div class=" after-add-more ">
                    <table>
                        <tr>
                            <td>Local Type</td>
                            <td>Local Body Name</td>
                        </tr>
                        <tr>
                            <td>
                                {!! form::select('local_type[]',['==============================='=>'=============================','dxfgu/kflnsf' => 'dxfgu/kflnsf', 'pkdxfgu/kflnsf' => 'pkdxfgu/kflnsf','gu/kflnsf'=>'gu/kflnsf','ufpFkflnsf'=>'ufpFkflnsf'], 'gu/kflnsf',['class'=>'form-control input-sm','id'=>'localtype','placeholder'=>'','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}
                            </td>
                            <td>
                                {!! form::text('local_name[]','',['class'=>'form-control input-sm','id'=>'local_name','placeholder'=>'','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}

                            </td>
                            <td><a class="btn btn-success add-more" title="Add field"><i
                                            class="glyphicon glyphicon-plus"></i> </a>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- Copy Fields-These are the fields which we get through jquery and then add after the above input,-->
                <div class="copy-fields hide">
                    <div class="control-group">
                    {{--{!! form::label('','',['class'=>'col-sm-2 control-label']); !!}--}}
                    <table>
                        <tr>
                            <td>Local Type</td>
                            <td>Local Body Name</td>
                        </tr>
                        <tr>
                            <td>
                                {!! form::select('local_type[]',['dxfgu/kflnsf' => 'dxfgu/kflnsf', 'pkdxfgu/kflnsf' => 'pkdxfgu/kflnsf','gu/kflnsf'=>'gu/kflnsf','ufpFkflnsf'=>'ufpFkflnsf'], 'gu/kflnsf',['class'=>'form-control input-sm','id'=>'localtype','placeholder'=>'','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}
                            </td>
                            <td>
                                {!! form::text('local_name[]','',['class'=>'form-control input-sm','id'=>'local_name','placeholder'=>'','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}
                            </td>
                            <td><a class="btn btn-danger remove" title="Remove field"><i
                                            class="glyphicon glyphicon-remove"></i> </a>
                            </td>


                        </tr>
                    </table>
                    </div>
                </div>

                <br><br>
                <div class="form-group">
                    <div class="col-sm-10">
                        {!! Form::submit('Save',['class'=>'btn btn-primary pull-right']) !!}
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

@endsection

@section('js')



@endsection