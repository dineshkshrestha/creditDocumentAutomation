@extends ('layouts.backend')

@section('title',' Facility Create ')
@section('heading','Facility')
@section('small_heading','Create')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li><a href="{{route('facility.index')}}">View All Facility</a></li>
    <li class="active"><strong>Create Facility</strong></li>
@endsection
@section('content')
    <div class="col-md-6">
    <div class="box box-default ">
        <div class="box-header with-border">
            <h3 class="box-title">
                <a class="btn btn-success btn-xs" href={{route('facility.index')}}>Available
                    Facility</a>
</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
                     {!! Form::open(['route' => 'facility.store','method'=>'post','class'=>'form-horizontal']) !!}
                <div class="form-group">
                    {!! form::label('name','Facility Name',['class'=>'col-sm-2 control-label']) !!}

                    <div class="col-sm-10">
                        {!! form::text('name','',['class'=>'form-control input-sm','autocomplete'=>'off','id'=>'name','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}

                        @if($errors->has('name'))
                            <span class="label label-danger">{{$errors->first('name')}} </span>
                        @endif
                    </div>
                </div>

                <div class="box-footer">
                    {!! Form::submit('Save',['class'=>'btn btn-primary pull-right']) !!}
                </div>


                    </div>

                    {!! Form::close() !!}
                </div>
            </div>

@endsection