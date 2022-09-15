@extends ('layouts.backend')
@section('title',' Registered Company Edit ')
@section('heading','Registered Company')
@section('small_heading','Edit')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li><a href="{{route('registered_company.index')}}">View All Registered Company</a></li>
    <li class="active"><strong>Edit Registered Company</strong></li>
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
            {!! Form::open(['route' =>['registered_company.update',$registered_company->id],'method'=>'post','class'=>'form-horizontal','enctype'=>'multipart/form-data','novalidate'=>'novalidate']) !!}
            <input type="hidden" name="_method" value="put">
            <input type="hidden" name="id" value="{{$registered_company->id}}">


            <form class="form-horizontal">
                <div class="box-body">

                    <div class="form-group">
                        {!! form::label('name','Registered Company Name',['class'=>'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! form::text('name',$registered_company->name,['class'=>'form-control input-sm','id'=>'name','placeholder'=>'','autocomplete'=>'off']) !!}

                            @if($errors->has('name'))
                                <span class="label label-danger">{{$errors->first('name')}} </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        {!! form::label('isin','ISIN',['class'=>'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! form::text('isin',$registered_company->isin,['class'=>'form-control input-sm','id'=>'isin','placeholder'=>'','autocomplete'=>'off']) !!}

                            @if($errors->has('isin'))
                                <span class="label label-danger">{{$errors->first('isin')}} </span>
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