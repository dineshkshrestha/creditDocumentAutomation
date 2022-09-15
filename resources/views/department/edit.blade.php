@extends ('layouts.backend')

@section('title',' Department Edit ')
@section('heading','Department')
@section('small_heading','Edit')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li><a href="{{route('department.index')}}">View All Departments</a></li>
    <li class="active"><strong>Edit Department</strong></li>
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
            {!! Form::open(['route' =>['department.update',$department->id],'method'=>'post','class'=>'form-horizontal','enctype'=>'multipart/form-data']) !!}
            <input type="hidden" name="_method" value="put">
            <input type="hidden" name="id" value="{{$department->id}}">
            @if(session('success'))
                <div class="alert alert-success">{{session('success')}}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{session('error')}}</div>
            @endif

            <form class="form-horizontal">
                <div class="box-body">

                    <div class="form-group">
                        {!! form::label('name','Department Name',['class'=>'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! form::text('name',$department->name,['class'=>'form-control input-sm','autocomplete'=>'off','id'=>'name','placeholder'=>'','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}

                            @if($errors->has('name'))
                                <span class="label label-danger">{{$errors->first('name')}} </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        {!! form::label('status','Status',['class'=>'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            @if($department->status==1)
                                {!! Form::radio('status', '1', true) !!}Active
                                {!! Form::radio('status', '0') !!}
                                Inactive
                            @else
                                {!! Form::radio('status', '1') !!}Active
                                {!! Form::radio('status', '0', true) !!} Inactive
                            @endif

                        </div>
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