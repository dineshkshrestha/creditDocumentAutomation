@extends ('layouts.backend')
@section('title',' Facility Edit ')
@section('heading','Banking Facility')
@section('small_heading','Edit')
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
    <li class="active"><strong>Edit Facilities</strong></li>

@endsection
@section('content')
    @php
        $a = $facility->amount;
        $b = str_replace( ',', '', $a );
        if( is_numeric( $b ) ) {
        $a = $b;
        }
    @endphp


    <div class="col-md-10">
        <div class="box box-default ">
            <div class=" with-border">
            </div>
            <div class="box-body">
                @php($id=($facility->id))
                {!! Form::open(['route' =>['joint_review.facilities_update',$id],'method'=>'post','novalidate' => 'novalidate','class'=>'form-horizontal','enctype'=>'multipart/form-data']) !!}
                <input type="hidden" name="_method" value="put">
                <input type="hidden" name="id" value="{{$facility->id}}">
                <input type="hidden" name="bid" value="{{$bid}}">



                <table>

                    <div class="widget-head"><h4 class="form-control col-sm-10" style="background-color: #2aabd2;">
                            Facilities
                            Details</h4></div>
                    <tr>
                        <td style="padding-right: 12px">  {!! form::label('facility','Facilities') !!}</td>
                        <td style="padding-right: 12px">  {!! form::label('amount','Amount') !!}</td>
                        <td style="padding-right: 12px">  {!! form::label('within','Within') !!}</td>

                    </tr>
                    <tr>
                        <td style="padding-right: 12px">  {!! form::select('facility',$facilities,$facility->facility_id,['class'=>'form-control input-sm','id'=>'facility','autocomplete'=>'off','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}</td>
                        @if($errors->has('facility'))
                            <span class="label label-danger">{{$errors->first('facility')}} </span>
                        @endif



                        <td style="padding-right: 12px">  {!! form::number('amount',$a,['required'=>'required','class'=>'form-control input-sm','id'=>'amount','min'=>'0','max'=>'99999999999','autocomplete'=>'off','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}</td>
                        @if($errors->has('amount'))
                            <span class="label label-danger">{{$errors->first('amount')}} </span>
                        @endif


                        @if($facility->within=='1')
                            <td style="padding-right: 12px">  {!! form::checkbox('within','1',true) !!}</td>
                        @else

                            <td style="padding-right: 12px">  {!! form::checkbox('within','1',false) !!}</td>
                        @endif

                    </tr>

                    <tr>
                        <td style="padding-right: 12px">  {!! form::label('rate','Rate(%)') !!}</td>
                        <td style="padding-right: 12px">  {!! form::label('remarks','Remarks') !!}</td>
                        <td style="padding-right: 12px">  {!! form::label('tenure','Tenure') !!}</td>
                    </tr>
                    <tr>


                        <td style="padding-right: 12px">  {!! form::text('rate',$facility->rate,['required'=>'required','class'=>'form-control input-sm','id'=>'rate','min'=>'0','max'=>'100','autocomplete'=>'off','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}</td>
                        @if($errors->has('rate'))
                            <span class="label label-danger">{{$errors->first('rate')}} </span>
                        @endif
                        <td style="padding-right: 12px">  {!! form::text('remarks',$facility->remarks,['class'=>'form-control input-sm','id'=>'amount','autocomplete'=>'off','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}</td>
                        @if($errors->has('remarks'))
                            <span class="label label-danger">{{$errors->first('remarks')}} </span>
                        @endif

                        <td style="padding-right: 12px">  {!! form::text('tenure',$facility->tenure,['class'=>'form-control input-sm','id'=>'tenure','autocomplete'=>'off','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','placeholder'=>'4 aif{, lbg, dlxgf']) !!}</td>
                        @if($errors->has('tenure'))
                            <span class="label label-danger">{{$errors->first('tenure')}} </span>
                        @endif

                    </tr>
                </table>
                <table>
                    <tr>
                        <td width="100" style="padding-right: 12px"><b> {!! form::label('date','Expiry Date') !!}</b>
                        </td>
                        <td width="100" style="padding-right: 12px">  {!! form::label('tyear','Year') !!}</td>
                        <td width="100" style="padding-right: 12px">  {!! form::label('tmonth','Month') !!}</td>
                        <td width="100" style="padding-right: 12px">  {!! form::label('tday','Day') !!}</td>
                    </tr>
                    <tr>
                        <td style="padding-right: 12px">  {!! form::label('bs','A.D.') !!}</td>
                        <td style="padding-right: 12px">  {!! form::select('tyear',[''=>'','2010'=>'2010','2011'=>'2011','2012'=>'2012','2013'=>'2013',
                                        '2014'=>'2014','2015'=>'2015','2016'=>'2016','2017'=>'2017','2018'=>'2018',
                                        '2019'=>'2019','2020'=>'2020','2021'=>'2021','2022'=>'2022','2023'=>'2023',
                                        '2024'=>'2024','2025'=>'2025','2026'=>'2026','2027'=>'2027','2028'=>'2028',
                                        '2029'=>'2029','2030'=>'2030','2031'=>'2031','2032'=>'2032','2033'=>'2033',
                                        '2034'=>'2034','2035'=>'2035','2036'=>'2036','2037'=>'2037','2038'=>'2038',
                                        '2039'=>'2039','2040'=>'2040','2041'=>'2041','2042'=>'2042','2043'=>'2043',
                                        '2044'=>'2044','2045'=>'2045','2046'=>'2046','2047'=>'2048','2049'=>'2049',
                                        '2050'=>'2050','2051'=>'2051','2052'=>'2052','2053'=>'2053','2054'=>'2054',
                                        '2055'=>'2055','2056'=>'2056','2057'=>'2057','2058'=>'2058','2059'=>'2059',
                                        '2060'=>'2060','2061'=>'2061',
                                        '2062'=>'2062','2063'=>'2063','2064'=>'2064','2065'=>'2065',
                                        '2066'=>'2066','2067'=>'2067','2068'=>'2068','2069'=>'2069',
                                        '2070'=>'2070','2071'=>'2071','2072'=>'2072','2073'=>'2073',
                                        '2074'=>'2074','2075'=>'2075'],$facility->tyear,['class'=>'form-control input-sm','id'=>'dyear','style'=>'font-size:14px;','autocomplete'=>'off']) !!}
                            @if($errors->has('tyear'))
                                <span class="label label-danger">{{$errors->first('tyear')}} </span>
                            @endif
                        </td>

                        <td style="padding-right: 12px">
                            {!! form::select('tmonth',[''=>'','1'=>'JAN', '2'=>'FEB', '3'=>'MAR', '4'=>'APR', '5'=>'MAY',
                             '6'=>'JUN', '7'=>'JUL', '8'=>'AUG', '9'=>'SEPT', '10'=>'OCT', '11'=>'NOV', '12'=>'DEC'],
                             $facility->tmonth,['class'=>'form-control input-sm','id'=>'name']) !!}
                            @if($errors->has('tmonth'))
                                <span class="label label-danger">{{$errors->first('tmonth')}} </span>
                            @endif
                        </td>
                        <td style="padding-right: 12px">  {!! form::select('tday',[''=>'','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8',
                                        '9'=>'9','10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18',
                                        '19'=>'19','20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24','25'=>'25','26'=>'26','27'=>'27','28'=>'28',
                                        '29'=>'29','30'=>'30','31'=>'31','32'=>'32'],$facility->tday,['class'=>'form-control input-sm','id'=>'name','style'=>'font-size:14px;','autocomplete'=>'off']) !!}
                            @if($errors->has('tday'))
                                <span class="label label-danger">{{$errors->first('tday')}} </span>
                            @endif
                        </td>
                    </tr>
                </table>


                <tr>
                        <td width="100" style="padding-right: 12px; float: right;">{!! form::label('status','Status') !!}</td>


                        <td width="300">
                            @if($facility->status==1)
                                {!! Form::radio('status', '1', true) !!}Active
                                {!! Form::radio('status', '0') !!}
                                Inactive
                            @else
                                {!! Form::radio('status', '1') !!}Active
                                {!! Form::radio('status', '0', true) !!} Inactive
                            @endif

                        </td>
                    </tr>
                </table>




            </div>

            <div class="box-footer">


                <button type="reset" class="btn btn-warning ">
                    Reset
                </button>

                {!! Form::submit('Save',['class'=>'btn btn-primary pull-right']) !!}

            </div>


            {!! Form::close() !!}

        </div></div>
@endsection
