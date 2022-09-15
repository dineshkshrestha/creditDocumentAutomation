@extends ('layouts.backend')

@section('title',' Province Edit ')
@section('heading','Province')
@section('small_heading','Edit')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li><a href="{{route('province.index')}}">View All Province</a></li>
    <li class="active"><strong>Edit Province</strong></li>
@endsection
@section('content')
    <div class="col-md-6">
        <!-- Horizontal Form -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"></h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            {!! Form::open(['route' =>['province.update',$province->id],'method'=>'post','class'=>'form-horizontal','enctype'=>'multipart/form-data']) !!}
            <input type="hidden" name="_method" value="put">
            <input type="hidden" name="id" value="{{$province->id}}">

            <form class="form-horizontal">
                <div class="box-body">

                    <div class="form-group">
                        {!! form::label('name','Province Name',['class'=>'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! form::text('name',$province->name,['class'=>'form-control input-sm','autocomplete'=>'off','id'=>'name','placeholder'=>'','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}

                        </div>
                        @if($errors->has('name'))
                            <span class="label label-danger">{{$errors->first('name')}} </span>
                        @endif
                    </div>

                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    {!! Form::submit('Save',['class'=>'btn btn-primary pull-right']) !!}

                </div>
                <!-- /.box-footer -->
            </form>
            {!! Form::close() !!}
        </div>
    </div>
@endsection