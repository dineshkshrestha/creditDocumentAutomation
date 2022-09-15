@extends ('layouts.backend')
@section('title',' Joint Borrower Edit ')
@section('heading','Joint Borrower')
@section('small_heading','Edit')
@section('borrower')
    {{--@php--}}
        {{--$b=App\JointBorrower::find($bid);--}}
    {{--dd($b);--}}
    {{--@endphp--}}
    {{--@if($b->joint1)--}}

        {{--{{App\PersonalBorrower::find($b->joint1)->english_name}}--}}
    {{--@endif--}}
    {{--@if($b->joint2)--}}
        {{--,{{App\PersonalBorrower::find($b->joint2)->english_name}}--}}
    {{--@endif--}}
    {{--@if($b->joint3)--}}
        {{--,{{App\PersonalBorrower::find($b->joint3)->english_name}}--}}
    {{--@endif--}}
@endsection
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li><a href="{{route('joint_borrower.index')}}">Borrower</a></li>
    <li><a href="{{route('joint_guarantor.index',$bid)}}">Guarantor</a></li>
    <li><a href="{{route('joint_property.index',$bid)}}">Property</a></li>
    <li><a href="{{route('joint_facilities.index',$bid)}}">Facilities</a></li>
    <li><a href="{{route('joint_review.index',$bid)}}">Review</a></li>

    <li class="active"><strong>Edit Borrower</strong></li>
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
                {!! Form::open(['route' =>['joint_review.update',$borrower->id],'method'=>'post','class'=>'form-horizontal','novalidate'=>'novalidate','enctype'=>'multipart/form-data']) !!}
                <input type="hidden" name="_method" value="put">
                <input type="hidden" name="id" value="{{$borrower->id}}">
                <input type="hidden" name="bid" value="{{$bid}}">
                <table>
                    <div class="widget-head"><h4 class="form-control col-sm-10" style="background-color: #2aabd2;">
                            Personal
                            Details</h4></div>

                    <tr>
                        <td style="padding-right: 12px">  {!! form::label('english_name','Full Name(English)') !!}</td>
                        <td style="padding-right: 12px">  {!! form::label('nepali_name','Full Name(नेपालीमा)') !!}</td>

                        <td style="padding-right: 12px">{!! form::label('gender','Gender') !!}</td>
                    </tr>
                    <tr>
                        <td style="padding-right: 12px">  {!! form::text('english_name',$borrower->english_name,['class'=>'form-control input-sm','id'=>'ename','autocomplete'=>'off','style'=>"width:250px; font-size:14px;",'required'=>'required']) !!}
                            @if($errors->has('english_name'))
                                <span class="label label-danger">{{$errors->first('english_name')}} </span>
                            @endif
                        </td>
                        <td style="padding-right: 12px">  {!! form::text('nepali_name',$borrower->nepali_name,['class'=>'form-control input-sm','id'=>'name','autocomplete'=>'off','required'=>'required','style'=>"width:250px;font-family:Bishallb,FONTASY_ HIMALI_ TT; width:300px font-size:14px; "]) !!}
                            @if($errors->has('nepali_name'))
                                <span class="label label-danger">{{$errors->first('nepali_name')}} </span>
                            @endif
                        </td>
                        <td style="padding-right: 12px">  {!! form::select('gender',['1'=>'Male','0'=>'Female'],$borrower->gender,['class'=>'form-control input-sm','id'=>'gender','style'=>"width:100px",'required'=>'required']) !!}</td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td style="padding-right: 12px">  {!! form::label('client_id','Client ID') !!}</td>
                        <td style="padding-right: 12px" width="100"><b> {!! form::label('date','Date of Birth') !!}</b>
                        </td>
                        <td style="padding-right: 12px" width="100">  {!! form::label('dob_year','Year') !!}</td>
                        <td style="padding-right: 12px" width="100">  {!! form::label('dob_month','Month') !!}</td>
                        <td style="padding-right: 12px" width="100">  {!! form::label('dob_day','Day') !!}</td>
                    </tr>
                    <tr>
                        <td style="padding-right: 12px; width:200px; ">  {!! form::text('client_id',$borrower->client_id,['class'=>'form-control input-sm','id'=>'clientid','autocomplete'=>'off']) !!}</td>
                        @if($errors->has('client_id'))
                            <span class="label label-danger">{{$errors->first('client_id')}} </span>
                        @endif
                        <td style="padding-right: 12px">  {!! form::label('bs','B.S.') !!}</td>
                        <td style="padding-right: 12px">  {!! form::select('dob_year',['1980'=>'1980','1981'=>'1981',
                                        '1982'=>'1982','1983'=>'1983','1984'=>'1984','1985'=>'1985',
                                        '1986'=>'1986','1987'=>'1987','1988'=>'1988','1989'=>'1989','1990'=>'1990','1991'=>'1991',
                                        '1992'=>'1992','1993'=>'1993','1994'=>'1994','1995'=>'1995',
                                        '1996'=>'1996','1997'=>'1997','1998'=>'1998','1999'=>'1999','2000'=>'2000','2001'=>'2001',
                                        '2002'=>'2002','2003'=>'2003','2004'=>'2004','2005'=>'2005',
                                        '2006'=>'2006','2007'=>'2007','2008'=>'2008','2009'=>'2009',
                                        '2010'=>'2010','2011'=>'2011','2012'=>'2012','2013'=>'2013',
                                        '2014'=>'2014','2015'=>'2015','2016'=>'2016','2017'=>'2017','2018'=>'2018',
                                        '2019'=>'2019','2020'=>'2020','2021'=>'2021','2022'=>'2022','2023'=>'2023',
                                        '2024'=>'2024','2025'=>'2025','2026'=>'2026','2027'=>'2027','2028'=>'2028',
                                        '2029'=>'2029','2030'=>'2030','2031'=>'2031','2032'=>'2032','2033'=>'2033',
                                        '2034'=>'2034','2035'=>'2035','2036'=>'2036','2037'=>'2037','2038'=>'2038',
                                        '2039'=>'2039','2040'=>'2040','2041'=>'2041','2042'=>'2042','2043'=>'2043',
                                        '2044'=>'2044','2045'=>'2045','2046'=>'2046','2047'=>'2047','2048'=>'2048','2049'=>'2049',
                                        '2050'=>'2050','2051'=>'2051','2052'=>'2052','2053'=>'2053','2054'=>'2054',
                                        '2055'=>'2055','2056'=>'2056','2057'=>'2057','2058'=>'2058','2059'=>'2059',
                                        '2060'=>'2060','2061'=>'2061',
                                        '2062'=>'2062','2063'=>'2063','2064'=>'2064','2065'=>'2065',
                                        '2066'=>'2066','2067'=>'2067','2068'=>'2068','2069'=>'2069',
                                        '2070'=>'2070','2071'=>'2071','2072'=>'2072','2073'=>'2073',
                                        '2074'=>'2074','2075'=>'2075'],$borrower->dob_year,['class'=>'form-control input-sm','id'=>'dyear','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}</td>

                        <td style="padding-right: 12px">
                            {!! form::select('dob_month',['1'=>' बैशाख', '2'=>'जेष्ठ', '3'=>'अषाढ ', '4'=>'श्रावण', '5'=>'भदाै',
                             '6'=>'असाेज ', '7'=>' कार्तिक ', '8'=>'मंसीर ', '9'=>' पाैष ', '10'=>' माघ', '11'=>'फाल्गुन', '12'=>' चैत्र'],
                             $borrower->dob_month,['class'=>'form-control input-sm','id'=>'name','required'=>'required']) !!}</td>
                        <td style="padding-right: 12px">  {!! form::select('dob_day',['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8',
                                        '9'=>'9','10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18',
                                        '19'=>'19','20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24','25'=>'25','26'=>'26','27'=>'27','28'=>'28',
                                        '29'=>'29','30'=>'30','31'=>'31','32'=>'32'],$borrower->dob_day,['class'=>'form-control input-sm','id'=>'name','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}</td>
                    </tr>
                </table>
                <br>
                <table>

                    <div class="widget-head"><h4 class="form-control col-sm-10" style="background-color: #2aabd2;">
                            Address</h4></div>
                    <tr>
                        <td style="padding-right: 12px" width="200">  {!! form::label('district_id','District') !!}</td>
                        <td style="padding-right: 12px"
                            width="150">  {!! form::label('local_bodies_id','Local Government') !!}</td>
                        <td style="padding-right: 12px" width="100">  {!! form::label('wardno','Ward No.') !!}</td>
                        <td style="padding-right: 12px" width="100">  {!! form::label('phone','Phone No.') !!}</td>

                    </tr>
                    <tr>
                        <td style="padding-right: 12px">  {!! form::select('district_id',$district,$borrower->district_id,['class'=>'form-control input-sm','id'=>'district_id','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                            @if($errors->has('district_id'))
                                <span class="label label-danger">{{$errors->first('district_id')}} </span>
                            @endif</td>
                        <td style="padding-right: 12px">  {!! form::select('local_bodies_id',$localbody,$borrower->local_bodies_id,['class'=>'form-control input-sm','id'=>'localbody_id','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                            @if($errors->has('local_bodies_id'))
                                <span class="label label-danger">{{$errors->first('local_bodies_id')}} </span>
                            @endif</td>
                        <td style="padding-right: 12px"> {!! form::select('wardno',['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9',
                                        '10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18','19'=>'19',
                                        '20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24','25'=>'25','26'=>'26','27'=>'27','28'=>'28','29'=>'29',
                                        '30'=>'30','31'=>'31','32'=>'32','33'=>'33','34'=>'34','35'=>'35','36'=>'36','37'=>'37','38'=>'38', '39'=>'39',
                                         '40'=>'40', '41'=>'41', '42'=>'42', '43'=>'43', '44'=>'44', '45'=>'45', '46'=>'46', '47'=>'47', '48'=>'48', '49'
                                         =>'49', '50'=>'50', '51'=>'51', '52'=>'52', '53'=>'53', '54'=>'54', '55'=>'55', '56'=>'56', '57'=>'57', '58'=>'58',
                                          '59'=>'59', '60'=>'60','================'=>'=============='],$borrower->wardno,['class'=>'form-control input-sm col-sm-2','id'=>'wardno','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                            @if($errors->has('wardno'))
                                <span class="label label-danger">{{$errors->first('wardno')}} </span>
                            @endif</td>
                        <td style="padding-right: 12px"> {!! form::text('phone',$borrower->phone,['class'=>'form-control input-sm col-sm-2','id'=>'phone','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                            @if($errors->has('phone'))
                                <span class="label label-danger">{{$errors->first('phone')}} </span>
                            @endif</td>
                    </tr>
                </table>
                <br>

                <table>

                    <div class="widget-head"><h4 class="form-control col-sm-10" style="background-color: #2aabd2;">
                            Citizenship Details</h4></div>
                    <tr>
                        <td style="padding-right: 12px"
                            width="150">  {!! form::label('citizenship_number',' Citizenship Number') !!}</td>
                        <td style="padding-right: 12px"
                            width="100">  {!! form::label('issued_district','Issued District') !!}</td>

                    </tr>
                    <tr>
                        <td style="padding-right: 12px"> {!! form::text('citizenship_number',$borrower->citizenship_number,['class'=>'form-control input-sm','id'=>'citizenshipno','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','required'=>'required','autocomplete'=>'off']) !!}
                            @if($errors->has('citizenship_number'))
                                <span class="label label-danger">{{$errors->first('citizenship_number')}} </span>
                            @endif
                        </td>
                        <td style="padding-right: 12px">  {!! form::select('issued_district',$district,$borrower->issued_district,['class'=>'form-control input-sm','id'=>'name','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                            @if($errors->has('issued_district'))
                                <span class="label label-danger">{{$errors->first('issued_district')}} </span>
                            @endif
                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <b>
                            <td style="padding-right: 12px" width="100">  {!! form::label('date','Issued Date') !!}</td>
                        </b>
                        <td style="padding-right: 12px" width="100">  {!! form::label('issued_year','Year') !!}</td>
                        <td style="padding-right: 12px" width="100">  {!! form::label('issued_month','Month') !!}</td>
                        <td style="padding-right: 12px" width="100">  {!! form::label('issued_day','Day') !!}</td>
                    </tr>
                    <tr>
                        <td style="padding-right: 12px">  {!! form::label('bs','B.S.') !!}</td>
                        <td style="padding-right: 12px">  {!! form::select('issued_year',['1980'=>'1980','1981'=>'1981',
                                        '1982'=>'1982','1983'=>'1983','1984'=>'1984','1985'=>'1985',
                                        '1986'=>'1986','1987'=>'1987','1988'=>'1988','1989'=>'1989','1990'=>'1990','1991'=>'1991',
                                        '1992'=>'1992','1993'=>'1993','1994'=>'1994','1995'=>'1995',
                                        '1996'=>'1996','1997'=>'1997','1998'=>'1998','1999'=>'1999','2000'=>'2000','2001'=>'2001',
                                        '2002'=>'2002','2003'=>'2003','2004'=>'2004','2005'=>'2005',
                                        '2006'=>'2006','2007'=>'2007','2008'=>'2008','2009'=>'2009',
                                        '2010'=>'2010','2011'=>'2011','2012'=>'2012','2013'=>'2013',
                                        '2014'=>'2014','2015'=>'2015','2016'=>'2016','2017'=>'2017','2018'=>'2018',
                                        '2019'=>'2019','2020'=>'2020','2021'=>'2021','2022'=>'2022','2023'=>'2023',
                                        '2024'=>'2024','2025'=>'2025','2026'=>'2026','2027'=>'2027','2028'=>'2028',
                                        '2029'=>'2029','2030'=>'2030','2031'=>'2031','2032'=>'2032','2033'=>'2033',
                                        '2034'=>'2034','2035'=>'2035','2036'=>'2036','2037'=>'2037','2038'=>'2038',
                                        '2039'=>'2039','2040'=>'2040','2041'=>'2041','2042'=>'2042','2043'=>'2043',
                                        '2044'=>'2044','2045'=>'2045','2046'=>'2046','2047'=>'2047','2048'=>'2048','2049'=>'2049',
                                        '2050'=>'2050','2051'=>'2051','2052'=>'2052','2053'=>'2053','2054'=>'2054',
                                        '2055'=>'2055','2056'=>'2056','2057'=>'2057','2058'=>'2058','2059'=>'2059',
                                        '2060'=>'2060','2061'=>'2061',
                                        '2062'=>'2062','2063'=>'2063','2064'=>'2064','2065'=>'2065',
                                        '2066'=>'2066','2067'=>'2067','2068'=>'2068','2069'=>'2069',
                                        '2070'=>'2070','2071'=>'2071','2072'=>'2072','2073'=>'2073',
                                        '2074'=>'2074','2075'=>'2075'],$borrower->issued_year,['class'=>'form-control input-sm','id'=>'dyear','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                            @if($errors->has('issued_year'))
                                <span class="label label-danger">Error!! {{$errors->first('issued_year')}} </span><br>
                            @endif</td>

                        <td style="padding-right: 12px">
                            {!! form::select('issued_month',['1'=>' बैशाख', '2'=>'जेष्ठ', '3'=>'अषाढ ', '4'=>'श्रावण', '5'=>'भदाै',
                             '6'=>'असाेज ', '7'=>' कार्तिक ', '8'=>'मंसीर ', '9'=>' पाैष ', '10'=>' माघ', '11'=>'फाल्गुन', '12'=>' चैत्र'],
                             $borrower->issued_month,['class'=>'form-control input-sm','id'=>'name','required'=>'required']) !!}</td>
                        <td style="padding-right: 12px">  {!! form::select('issued_day',['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8',
                                        '9'=>'9','10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18',
                                        '19'=>'19','20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24','25'=>'25','26'=>'26','27'=>'27','28'=>'28',
                                        '29'=>'29','30'=>'30','31'=>'31','32'=>'32'],$borrower->issued_day,['class'=>'form-control input-sm','id'=>'name','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}</td>
                    </tr>
                </table>
                <br>
                <table>

                    <div class="widget-head"><h4 class="form-control col-sm-10" style="background-color: #2aabd2;">
                            Three Generation Details </h4></div>
                    <tr>
                        <td style="padding-right: 12px"
                            width="250">  {!! form::label('grandfather_name','(Grand Father/Father InLaw) Name') !!}</td>
                        <td style="padding-right: 12px">  {!! form::label('grandfather_relation','Relation with Borrower') !!}</td>
                    </tr>
                    <tr>
                        <td style="padding-right: 12px"> {!! form::text('grandfather_name',$borrower->grandfather_name,['class'=>'form-control input-sm','id'=>'gfname','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                            @if($errors->has('grandfather_name'))
                                <span class="label label-danger">{{$errors->first('grandfather_name')}} </span>
                            @endif</td>
                        <td style="padding-right: 12px">  {!! form::select('grandfather_relation',['gflt'=>'gflt','gfltgL'=>'gfltgL','a"xf/L'=>'a"xf/L'],$borrower->grandfather_relation,['class'=>'form-control input-sm form-inline','id'=>'name','required'=>'required','autocomplete'=>'off','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}
                            @if($errors->has('grandfather_relation'))
                                <span class="label label-danger">{{$errors->first('grandfather_relation')}} </span>
                            @endif</td>

                    </tr>
                    <tr>
                        <td style="padding-right: 12px"
                            width="250">  {!! form::label('father_name','Father Name') !!}</td>
                        <td style="padding-right: 12px">  {!! form::label('father_relation-relation','Relation with Borrower') !!}</td>
                    </tr>
                    <tr>
                        <td style="padding-right: 12px">  {!! form::text('father_name',$borrower->father_name,['class'=>'form-control input-sm','id'=>'father_name','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                            @if($errors->has('father_name'))
                                <span class="label label-danger">{{$errors->first('father_name')}} </span>
                            @endif
                        </td>
                        <td style="padding-right: 12px">
                            {!! form::select('father_relation',['%f]/f '=>'%f]/f ','%f]/L '=>'%f]/L ',' klTg'=>'klTg'],$borrower->father_relation,['class'=>'form-control input-sm','id'=>'father-relation','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                            @if($errors->has('father_relation'))
                                <span class="label label-danger">{{$errors->first('father_relation')}} </span>
                            @endif
                        </td>
                    </tr>
                    <tr>

                        <td style="padding-right: 12px"
                            width="250">  {!! form::label('spouse_name','Spouse Name') !!}</td>
                        <td style="padding-right: 12px">  {!! form::label('spouse_relation','Relation with Borrower') !!}</td>

                    </tr>
                    <tr>
                        <td style="padding-right: 12px">  {!! form::text('spouse_name',$borrower->spouse_name,['class'=>'form-control input-sm','id'=>'spouse_name','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                            @if($errors->has('spouse_name'))
                                <span class="label label-danger">{{$errors->first('spouse_name')}} </span>
                            @endif
                        </td>
                        <td style="padding-right: 12px">
                            {!! form::select('spouse_relation',['klt '=>'klt ',' klTg'=>'klTg'],$borrower->spouse_relation,['class'=>'form-control input-sm','id'=>'spouse_relation','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                            @if($errors->has('spouse_relation'))
                                <span class="label label-danger">{{$errors->first('spouse_relation')}} </span>
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