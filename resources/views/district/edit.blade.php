@extends ('layouts.backend')

@section('title',' District Edit ')
@section('heading','District')
@section('small_heading','Edit')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li><a href="{{route('district.index')}}">View All District</a></li>
    <li class="active"><strong>Edit District</strong></li>
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
            {!! Form::open(['route' =>['district.update',$district->id],'method'=>'post','class'=>'form-horizontal','enctype'=>'multipart/form-data']) !!}
            <input type="hidden" name="_method" value="put">
            <input type="hidden" name="id" value="{{$district->id}}">

            <form class="form-horizontal">
                <div class="box-body">


                    <div class="form-group">
                        {!! form::label('name','District',['class'=>'col-sm-2 control-label']) !!}


                        <div class="col-sm-10">
                            {!! form::text('name',$district->name,['autocomplete'=>'off','class'=>'form-control','id'=>'name','placeholder'=>'','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}

                            @if($errors->has('name'))
                                <span class="label label-danger">{{$errors->first('name')}} </span>
                            @endif
                        </div>

                    </div>
                    <div class="form-group">
                        {!! form::label('Province','Province',['class'=>'col-sm-2 control-label']) !!}

                        <div class="col-sm-10">
                            {!! form::select('province_id',$province,$district->province_id,['class'=>'form-control input-sm','id'=>'localtype','placeholder'=>'','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}

                        </div>

                    </div>




                    <div class="form-group">
                        <div class="col-sm-10">
                            {!! Form::submit('Save',['class'=>'btn btn-primary pull-right']) !!}
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>


            <!-- /.box-header -->

        </div>


    </div>

@endsection