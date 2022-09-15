@extends('layouts.backend')
@section('title',' Edit User')
@section('heading','Edit User')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
@endsection
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li class="active"><strong>Edit User</strong></li>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">

                <div class="box-body">
                {!! Form::open(['route' => ['userregister.update',$user->id],'method'=>'post','novalidate'=>'novalidate','enctype'=>'multipart/form-data'],['class'=>'form-horizontal']) !!}
                    <input type="hidden" name="_method" value="put">
                    <input type="hidden" name="id" value="{{$user->id}}">


                <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Please Fill all the details</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->

                        <div class="box-body">
                            <div class="form-group">
                                {!! form::label('name','Name') !!}
                                {!! form::text('name',$user->name,['class'=>'form-control input-sm','id'=>'name','autocomplete'=>'off','style'=>"width:350px; font-size:14px;",'required'=>'required']) !!}
                                @if($errors->has('name'))
                                    <span class="label label-danger">{{$errors->first('name')}} </span>
                                @endif

                            </div>
                            <div class="form-group">
                                {!! form::label('username','User Name') !!}
                                {!! form::text('username',$user->username,['class'=>'form-control input-sm','id'=>'username','autocomplete'=>'off','style'=>"width:350px; font-size:14px;",'required'=>'required']) !!}
                                @if($errors->has('username'))
                                    <span class="label label-danger">{{$errors->first('username')}} </span>
                                @endif

                            </div>

                            <div class="form-group">
                                {!! form::label('email','Email') !!}
                                {!! form::text('email',$user->email,['class'=>'form-control input-sm','id'=>'email','autocomplete'=>'off','style'=>"width:350px; font-size:14px;",'required'=>'required']) !!}
                                @if($errors->has('email'))
                                    <span class="label label-danger">{{$errors->first('email')}} </span>
                                @endif
                            </div>
                            <div class="form-group">
                                {!! form::label('post','Post') !!}
                                {!! form::text('post',$user->post,['class'=>'form-control input-sm','id'=>'post','autocomplete'=>'off','style'=>"width:350px; font-size:14px;",'required'=>'required']) !!}
                                @if($errors->has('post'))
                                    <span class="label label-danger">{{$errors->first('post')}} </span>
                                @endif
                            </div>

                            <div class="form-group">
                                {!! form::label('branch','Branch') !!}
                                {!! form::select('branch',$branch,$user->branch,['class'=>'form-control input-sm','id'=>'branch','autocomplete'=>'off','style'=>"width:350px; font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;",'required'=>'required']) !!}
                                @if($errors->has('branch'))
                                    <span class="label label-danger">{{$errors->first('branch')}} </span>
                                @endif
                            </div>
                            <div class="form-group">
                                {!! form::label('usertype','User Type') !!}
                                {!! form::select('usertype',['user'=>'User','checker'=>'Checker'],$user->user_type,['class'=>'form-control input-sm','id'=>'branch','autocomplete'=>'off','style'=>"width:350px; font-size:14px;",'required'=>'required']) !!}
                                @if($errors->has('usertype'))
                                    <span class="label label-danger">{{$errors->first('usertype')}} </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="image">Image</label>
                                <input type="file" id="image" name="image">
                                @if($errors->has('image'))
                                    <span class="label label-danger">{{$errors->first('image')}} </span>
                                @endif
                                <p class="help-block">Profile picture of user is optional</p>
                            </div>
                            <div class="form-group">
                                {!! form::label('status','Status',['class'=>'col-sm-2 control-label']) !!}

                                @if($user->status==1)
                                    {!! Form::radio('status', '1', true) !!}Active
                                    {!! Form::radio('status', '0') !!}
                                    Inactive
                                @else
                                    {!! Form::radio('status', '1') !!}Active
                                    {!! Form::radio('status', '0', true) !!} Inactive
                                @endif

                            </div>
                        </div>
                        <!-- /.box-body -->



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
