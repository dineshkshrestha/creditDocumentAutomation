@extends ('layouts.backend')
@section('title',' Edit Land Property')
@section('heading','Edit Land')
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
    <li class="active"><strong>Land Edit</strong></li>
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
                {!! Form::open(['route' =>['joint_review.personal_land_update',$land->id],'method'=>'post','novalidate'=>'novalidate','class'=>'form-horizontal','enctype'=>'multipart/form-data']) !!}
                <input type="hidden" name="_method" value="put">
                <input type="hidden" name="id" value="{{$land->id}}">
                <input type="hidden" name="bid" value="{{$bid}}">

                <table>
                    <div class="widget-head">
                        <h4 class="form-control col-sm-10"
                            style="background-color: #2aabd2;">
                            Edit Land Details</h4></div>
                    <tr>
                        <td width="150"
                            style="padding-right: 12px">  {!! form::label('district_id','District') !!}</td>
                        <td width="150"
                            style="padding-right: 12px">  {!! form::label('localgovt_id','Local Government') !!}</td>
                        <td width="100"
                            style="padding-right: 12px">  {!! form::label('wardno','Ward No.') !!}</td>
                    </tr>
                    <tr>
                        <td style="padding-right: 12px">  {!! form::select('district_id',$district,$land->district_id,['class'=>'form-control input-sm','id'=>'district_id','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                            @if($errors->has('district_id'))
                                <span class="label label-danger">{{$errors->first('district_id')}} </span>
                            @endif</td>
                        <td style="padding-right: 12px">  {!! form::select('local_body_id',$localbody,$land->local_bodies_id,['class'=>'form-control input-sm','id'=>'localbody_id','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                            @if($errors->has('local_body_id'))
                                <span class="label label-danger">{{$errors->first('local_body_id')}} </span>
                            @endif</td>
                        <td style="padding-right: 12px"> {!! form::select('wardno',['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9',
                                        '10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18','19'=>'19',
                                        '20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24','25'=>'25','26'=>'26','27'=>'27','28'=>'28','29'=>'29',
                                        '30'=>'30','31'=>'31','32'=>'32','33'=>'33','34'=>'34','35'=>'35','36'=>'36','37'=>'37','38'=>'38', '39'=>'39',
                                         '40'=>'40', '41'=>'41', '42'=>'42', '43'=>'43', '44'=>'44', '45'=>'45', '46'=>'46', '47'=>'47', '48'=>'48', '49'
                                         =>'49', '50'=>'50', '51'=>'51', '52'=>'52', '53'=>'53', '54'=>'54', '55'=>'55', '56'=>'56', '57'=>'57', '58'=>'58',
                                          '59'=>'59', '60'=>'60','================'=>'=============='],$land->wardno,['class'=>'form-control input-sm col-sm-2','id'=>'wardno','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                            @if($errors->has('wardno'))
                                <span>{{$errors->first('wardno')}} </span>
                            @endif</td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td width="200"
                            style="padding-right: 12px">{!! form::label('sheet_no','Sheet No.') !!}</td>
                        <td width="200"
                            style="padding-right: 12px">{!! form::label('kitta_no','Kitta No.') !!}</td>
                    </tr>
                    <tr>
                        <td style="padding-right: 12px; ">{!! form::text('sheet_no',$land->sheet_no,['class'=>'form-control input-sm','id'=>'sheet_no','style'=>'font-size:14px; font-family:Bishallb,FONTASY_ HIMALI_ TT;','autocomplete'=>'off','required'=>'required']) !!}
                            @if($errors->has('sheet_no'))
                                <span class="label label-danger">{{$errors->first('sheet_no')}} </span>
                            @endif
                        </td>
                        <td style="padding-right: 12px;">{!! form::text('kitta_no',$land->kitta_no,['class'=>'form-control input-sm','id'=>'kitta_no','style'=>'font-size:14px; font-family:Bishallb,FONTASY_ HIMALI_ TT;','autocomplete'=>'off','required'=>'required']) !!}
                            @if($errors->has('kitta_no'))
                                <span class="label label-danger">{{$errors->first('kitta_no')}} </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="200"
                            style="padding-right: 12px">{!! form::label('area','Area') !!}</td>
                        <td width="300"
                            style="padding-right: 12px">{!! form::label('remarks','Remarks') !!}</td>
                    </tr>
                    <tr>
                        <td style=" padding-right: 12px; ">{!! form::text('area',$land->area,['class'=>'form-control input-sm','style'=>'font-size:14px; font-family:Bishallb,FONTASY_ HIMALI_ TT;','id'=>'area','autocomplete'=>'off','required'=>'required']) !!}
                            @if($errors->has('area'))
                                <span class="label label-danger">{{$errors->first('area')}} </span>
                            @endif</td>
                        <td style=" padding-right: 12px;">{!! form::text('remarks',$land->remarks,['class'=>'form-control input-sm','style'=>'font-size:14px; font-family:Bishallb,FONTASY_ HIMALI_ TT;','id'=>'remarks','autocomplete'=>'off','required'=>'required']) !!}
                            @if($errors->has('remarks'))
                                <span class="label label-danger">{{$errors->first('remarks')}} </span>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2"
                            style="padding-right: 12px">{!! form::label('Malpot','Land Revenue Office') !!}</td>
                    </tr>
                    <tr>
                        <td colspan="2"
                            style="padding-right: 12px;">{!! form::text('malpot',$land->malpot,['class'=>'form-control input-sm','style'=>'font-size:14px; font-family:Bishallb,FONTASY_ HIMALI_ TT;','id'=>'malpot','autocomplete'=>'off','required'=>'required','placeholder'=>'मालपाेत कार्यलय राख्नुहाेस्']) !!}
                            @if($errors->has('malpot'))
                                <span class="label label-danger">{{$errors->first('malpot')}} </span>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td>   {!! form::label('stat','Status',['class'=>'col-sm-2 control-label']) !!}</td>
                    </tr>
                    <tr>
                        <td>
                            @if($land->status==1)
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
@section('js')


    <script type="text/javascript">
        $('#district_id').change(function () {

            var district_id = $(this).val();
            console.log(district_id);
            $.ajax({
                url: '/select_local_body',
                data: {'district_id': district_id},
                method: 'post',
                success: function (resp) {
                    $('#localbody_id').html('');
                    $('#localbody_id').html(resp);
                }
            })
        })
    </script>
@endsection