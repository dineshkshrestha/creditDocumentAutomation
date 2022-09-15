@extends('layouts.backend')
@section('title',' Change Password')
@section('heading','Change Password')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
@endsection
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li class="active"><strong>Change Password</strong></li>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">

                <div class="box-body">
                {!! Form::open(['route' => ['user.update_password',$user->id],'method'=>'post','novalidate'=>'novalidate','enctype'=>'multipart/form-data'],['class'=>'form-horizontal']) !!}
                    <input type="hidden" name="_method" value="put">
                    <input type="hidden" name="id" value="{{$user->id}}">


                <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Change Password</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->

                        <div class="box-body">
                            <div class="form-group">
                                {!! form::label('old_password','Old Password') !!}
                                <input type="password" name="old_password" id="old_password"  class="form-control input-sm" autocomplete="off" style="width:350px;" >

                            @if($errors->has('old_password'))
                                    <span class="label label-danger">{{$errors->first('old_password')}} </span>
                                @endif

                            </div>
                            <div class="form-group">
                                {!! form::label('new_password','New Password') !!}
                                <input type="password" name="new_password" id="new_password"  class="form-control input-sm" autocomplete="off" style="width:350px;" >

                            @if($errors->has('new_password'))
                                    <span class="label label-danger">{{$errors->first('new_password')}} </span>
                                @endif

                            </div>



                        <div class="box-footer">


                            <button type="reset" class="btn btn-warning ">
                                Reset
                            </button>

                            {!! Form::submit('Save',['class'=>'btn btn-primary pull-right']) !!}

                        </div>






                        {!! Form::close() !!}

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
