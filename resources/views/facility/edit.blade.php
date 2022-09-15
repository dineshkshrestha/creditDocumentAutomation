@extends ('layouts.backend')

@section('title',' Facility Edit ')
@section('heading','Facility')
@section('small_heading','Edit')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li><a href="{{route('facility.index')}}">View All Facility</a></li>
    <li class="active"><strong>Edit Facility</strong></li>
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
            {!! Form::open(['route' =>['facility.update',$facility->id],'method'=>'post','class'=>'form-horizontal','enctype'=>'multipart/form-data']) !!}
            <input type="hidden" name="_method" value="put">
            <input type="hidden" name="id" value="{{$facility->id}}">


            <form class="form-horizontal">
                <div class="box-body">

                    <div class="form-group">
                        {!! form::label('name','Facility Name',['class'=>'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! form::text('name',$facility->name,['class'=>'form-control input-sm','id'=>'name','placeholder'=>'','autocomplete'=>'off','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}

                            @if($errors->has('name'))
                                <span class="label label-danger">{{$errors->first('name')}} </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        {!! form::label('status','Status',['class'=>'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            @if($facility->status==1)
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