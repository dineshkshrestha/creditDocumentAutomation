@extends ('layouts.backend')

@section('title',' Registered Company Create ')
@section('heading','Registered Company')
@section('small_heading','Create')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li><a href="{{route('registered_company.index')}}">View All Registered Company</a></li>
    <li class="active"><strong>Create Registered Company</strong></li>
@endsection
@section('content')
    <div class="col-md-6">
    <div class="box box-default ">
        <div class="box-header with-border">
            <h3 class="box-title">
                <a class="btn btn-success btn-xs" href={{route('registered_company.index')}}>Available
                    Registered Company</a>
</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
                     {!! Form::open(['route' => 'registered_company.store','method'=>'post','class'=>'form-horizontal','novalidate'=>'novalidate']) !!}
                <div class="form-group">
                    {!! form::label('name','Registered Company Name',['class'=>'col-sm-2 control-label']) !!}

                    <div class="col-sm-10">
                        {!! form::text('name','',['class'=>'form-control input-sm','autocomplete'=>'off','id'=>'name','required'=>'required']) !!}

                        @if($errors->has('name'))
                            <span class="label label-danger">{{$errors->first('name')}} </span>
                        @endif
                    </div>
                </div>
            <div class="form-group">
                {!! form::label('isin','ISIN',['class'=>'col-sm-2 control-label']) !!}

                <div class="col-sm-10">
                    {!! form::text('isin','',['class'=>'form-control input-sm','autocomplete'=>'off','id'=>'isin','required'=>'required']) !!}

                    @if($errors->has('isin'))
                        <span class="label label-danger">{{$errors->first('isin')}} </span>
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