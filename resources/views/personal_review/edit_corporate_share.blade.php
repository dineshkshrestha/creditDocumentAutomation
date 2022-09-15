@extends ('layouts.backend')
@section('title',' Edit Share Property')
@section('heading','Edit Property')
@section('small_heading','Please edit the Share details.')
@section('borrower')
    {{App\PersonalBorrower::find($bid)->english_name}}
@endsection
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li><a href="{{route('personal_borrower.index')}}">Borrower</a></li>
    <li><a href="{{route('personal_guarantor.index',$bid)}}">Guarantor</a></li>
    <li><a href="{{route('personal_property.index',$bid)}}">Property</a></li>
    <li><a href="{{route('personal_facilities.index',$bid)}}">Loan Details</a></li>
    <li><a href="{{route('personal_review.index',$bid)}}">Review</a></li>
    <li class="active"><strong>Share Edit</strong></li>
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
                {!! Form::open(['route' =>['personal_review.corporate_share_update',$share->id],'method'=>'post','novalidate'=>'novalidate','class'=>'form-horizontal','enctype'=>'multipart/form-data']) !!}
                <input type="hidden" name="_method" value="put">
                <input type="hidden" name="id" value="{{$share->id}}">
                <input type="hidden" name="bid" value="{{$bid}}">
                <table>

                    <div class="widget-head"><h4 class="form-control col-sm-10"
                                                 style="background-color: #2aabd2;">
                            Edit Share Details</h4></div>

                    <tr>
                        <td width="200">Client Id</td>
                        <td width="200">ISIN</td>
                        <td width="200">Share Type</td>

                    </tr>
                    <tr>
                        <td style="padding-right: 12px; ">{!! form::text('client_id',$share->client_id,['style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ','class'=>'form-control input-sm','id'=>'instclientid','autocomplete'=>'off','required'=>'required']) !!}
                            @if($errors->has('client_id'))
                                <span class="label label-danger">{{$errors->first('client_id')}} </span>
                            @endif
                        </td>
                        <td style=" padding-right: 12px; ">{!! form::select('isin',$isin,$share->isin,['class'=>'form-control input-sm','id'=>'isin','autocomplete'=>'off','required'=>'required']) !!}
                            @if($errors->has('isin'))
                                <span class="label label-danger">{{$errors->first('isin')}} </span>
                            @endif</td>
                        <td style=" padding-right: 12px; font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">
                            {!! form::select('share_type',[';fwf/)f z]o/'=>';fwf/)f z]o/',';:+yfks z]o/'=>';:+yfks z]o/','cu|flwsf/ z]o/'=>'cu|flwsf/ z]o/','C)Fkq'=>'C)Fkq'],$share->share_type,['class'=>'form-control input-sm','id'=>'share_type','autocomplete'=>'off']) !!}</td>
                    </tr>

                    <tr>
                        <td width="200">DP Id</td>
                        <td width="300">Kitta</td>
                    </tr>
                    <tr>
                        <td style="padding-right: 12px;  ">{!! form::text('dpid',$share->dpid,['style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ','class'=>'form-control input-sm','id'=>'dpid','autocomplete'=>'off','required'=>'required']) !!}
                            @if($errors->has('dpid'))
                                <span class="label label-danger">{{$errors->first('dpid')}} </span>
                            @endif</td>
                        <td style="padding-right: 12px; ">{!! form::text('kitta',$share->kitta,['style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ','class'=>'form-control input-sm','id'=>'kitta','autocomplete'=>'off','required'=>'required']) !!}
                            @if($errors->has('kitta'))
                                <span class="label label-danger">{{$errors->first('kitta')}} </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>   {!! form::label('stat','Status',['class'=>'col-sm-2 control-label']) !!}</td>
                    </tr>
                    <tr>
                        <td>
                            @if($share->status==1)
                                {!! Form::radio('stat', '1', true) !!}Active
                                {!! Form::radio('stat', '0') !!}
                                Inactive
                            @else
                                {!! Form::radio('stat', '1') !!}Active
                                {!! Form::radio('stat', '0', true) !!} Inactive
                            @endif
                        </td>
                    </tr>
                </table>
                <br>
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
