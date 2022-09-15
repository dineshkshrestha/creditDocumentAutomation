@extends ('layouts.backend')
@section('title',' Edit Vehicle Property')
@section('heading','Edit Vehicle')
@section('small_heading','details.')
@section('borrower')
    @php
        $b=App\JointBorrower::find($bid)
    @endphp
    @if($b->joint1)
        {{App\PersonalBorrower::find($b->joint1)->english_name}}
    @endif
    @if($b->joint2)
        ,{{App\PersonalBorrower::find($b->joint2)->english_name}}
    @endif
    @if($b->joint3)
        ,{{App\PersonalBorrower::find($b->joint3)->english_name}}
    @endif
@endsection
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li><a href="{{route('joint_borrower.index')}}">Borrower</a></li>
    <li><a href="{{route('joint_guarantor.index',$bid)}}">Guarantor</a></li>
    <li><a href="{{route('joint_property.index',$bid)}}">Property</a></li>
    <li><a href="{{route('joint_facilities.index',$bid)}}">Facilities</a></li>
    <li><a href="{{route('joint_review.index',$bid)}}">Review</a></li>
    <li class="active"><strong>Vehicle Edit</strong></li>
@endsection

@section('content')
    <div class="col-md-10">
        <div class="box box-default ">
            <div class=" with-border">
                {{--<h3 class="box-title">--}}
                {{--</h3>--}}
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                {!! Form::open(['route' =>['joint_review.hirepurchase_update',$vehicle->id],'method'=>'post','novalidate'=>'novalidate','class'=>'form-horizontal','enctype'=>'multipart/form-data']) !!}
                <input type="hidden" name="_method" value="put">
                <input type="hidden" name="id" value="{{$vehicle->id}}">
                <input type="hidden" name="bid" value="{{$bid}}">

                <table>
                    <div class="widget-head">
                        <h4 class="form-control col-sm-10"
                            style="background-color: #2aabd2;">
                            Edit Vehicle Details</h4></div>
                    <tr>
                        <td width="150"
                            style="padding-right: 12px">  {!! form::label('model_number','Model Number') !!}</td>
                        <td width="100"
                            style="padding-right: 12px">  {!! form::label('registration_number','Registration Number') !!}</td>

                    </tr>
                    <tr>

                        <td style="padding-right: 12px">  {!! form::text('model_number',$vehicle->model_number,['class'=>'form-control input-sm','id'=>'model_number','required'=>'required','autocomplete'=>'off']) !!}
                            @if($errors->has('model_number'))
                                <span class="label label-danger">{{$errors->first('model_number')}} </span>
                            @endif</td>
                        <td style="padding-right: 12px"> {!! form::text('registration_number',$vehicle->registration_number,['class'=>'form-control input-sm col-sm-2','id'=>'registration_number','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                            @if($errors->has('registration_number'))
                                <span class="label label-danger">{{$errors->first('registration_number')}} </span>
                            @endif</td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td width="200"
                            style="padding-right: 12px">{!! form::label('engine_number','Engine Number') !!}</td>
                        <td width="200"
                            style="padding-right: 12px">{!! form::label('chassis_number','Chassis Number') !!}</td>
                    </tr>
                    <tr>
                        <td width="200" style="padding-right: 12px; font-size:14px; ">{!! form::text('engine_number',$vehicle->engine_number,['class'=>'form-control input-sm','id'=>'engine_number','autocomplete'=>'off','required'=>'required']) !!}
                            @if($errors->has('engine_number'))
                                <span class="label label-danger">{{$errors->first('engine_number')}} </span>
                            @endif
                        </td>
                        <td  width="200"  style=" font-size:14px; padding-right: 12px ">{!! form::text('chassis_number',$vehicle->chassis_number,['class'=>'form-control input-sm','id'=>'chassis_number','autocomplete'=>'off','required'=>'required']) !!}
                            @if($errors->has('chassis_number'))
                                <span class="label label-danger">{{$errors->first('chassis_number')}} </span>
                            @endif
                        </td>
                    </tr>

                </table>


            </div>

            <div class="box-footer">


                <button type="reset" class="btn btn-warning ">
                    Reset
                </button>

                {!! Form::submit('Update',['class'=>'btn btn-primary pull-right']) !!}

            </div>


        </div>
    </div>

    {!! Form::close() !!}

@endsection


