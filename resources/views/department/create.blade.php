@extends ('layouts.backend')

@section('title',' Department Create ')
@section('heading','Department')
@section('small_heading','Create')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li><a href="{{route('department.index')}}">View All Departments</a></li>
    <li class="active"><strong>Create Department</strong></li>
@endsection
@section('content')
    <div class="col-md-6">
        <div class="box box-default ">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <a class="btn btn-success btn-xs" href={{route('department.index')}}>Available
                        Departments</a>
                </h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">


                {!! Form::open(['route' => 'department.store','method'=>'post','class'=>'form-horizontal']) !!}
                <div class="form-group">
                    {!! form::label('name','Department Name',['class'=>'col-sm-2 control-label']) !!}

                    <div class="col-sm-10">
                        {!! form::text('name','',['class'=>'form-control input-sm','autocomplete'=>'off','id'=>'name','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}

                        @if($errors->has('name'))
                            <span class="label label-danger">{{$errors->first('name')}} </span>
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

@endsection