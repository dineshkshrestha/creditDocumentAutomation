@extends ('layouts.backend')
@section('title',' Corporate Guarantor Edit ')
@section('heading','Corporate Guarantor')
@section('small_heading','Edit')
@section('borrower')
    {{App\PersonalBorrower::find($bid)->english_name}}
@endsection
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li><a href="{{route('personal_borrower.index')}}">Borrower</a></li>
    <li><a href="{{route('personal_guarantor.index',$bid)}}">Guarantor</a></li>
    <li><a href="{{route('personal_property.index',$bid)}}">Property</a></li>
    <li><a href="{{route('personal_facilities.index',$bid)}}">Facilities</a></li>
    <li><a href="{{route('personal_review.index',$bid)}}">Review</a></li>
    <li class="active"><strong>Edit Corporate Guarantor</strong></li>

@endsection
@section('content')

    <div class="box box-default">
        {!! Form::open(['route' =>['personal_review.corporate_guarantor_update',$guarantor->id],'method'=>'post','novalidate' => 'novalidate','class'=>'form-horizontal','enctype'=>'multipart/form-data']) !!}
        <div class="box-body">
            <input type="hidden" name="_method" value="put">
            <input type="hidden" name="id" value="{{$guarantor->id}}">
            <input type="hidden" name="bid" value="{{$bid}}">

            <table>

                <div class="widget-head"><h4 class="form-control col-sm-10" style="background-color: #2aabd2;">
                        Personal
                        Details</h4></div>

                <tr>
                    <td style="padding-right: 12px">  {!! form::label('english_name','Full Name(English)') !!}</td>
                    <td style="padding-right: 12px">  {!! form::label('nepali_name','Full Name(नेपालीमा)') !!}</td>
                    <td style="padding-right: 12px">  {!! form::label('client_id','Client ID') !!}</td>
                </tr>
                <tr>
                    <td style="padding-right: 12px">  {!! form::text('english_name',$guarantor->english_name,['class'=>'form-control input-sm','id'=>'ename','autocomplete'=>'off','style'=>"width:250px; font-size:14px;",'required'=>'required']) !!}
                        @if($errors->has('english_name'))
                            <span class="label label-danger">{{$errors->first('english_name')}} </span>
                        @endif
                    </td>
                    <td style="padding-right: 12px">  {!! form::text('nepali_name',$guarantor->nepali_name,['class'=>'form-control input-sm','id'=>'name','autocomplete'=>'off','required'=>'required','style'=>"width:250px;font-family:Bishallb,FONTASY_ HIMALI_ TT; width:300px font-size:14px; "]) !!}
                        @if($errors->has('nepali_name'))
                            <span class="label label-danger">{{$errors->first('nepali_name')}} </span>
                        @endif
                    </td>
                    <td style="padding-right: 12px">  {!! form::text('client_id',$guarantor->client_id,['class'=>'form-control input-sm','id'=>'name','autocomplete'=>'off','required'=>'required','style'=>"width:250px;font-family:Bishallb,FONTASY_ HIMALI_ TT; width:300px font-size:14px; "]) !!}
                        @if($errors->has('client_id'))
                            <span class="label label-danger">{{$errors->first('client_id')}} </span>
                        @endif
                    </td>
                </tr>
            </table>
            <table>
                <div class="widget-head"><h4 class="form-control col-sm-10" style="background-color: #2aabd2;">
                        Registration
                        Details</h4></div>
                <tr>
                    <td style="padding-right: 12px">  {!! form::label('ministry_id','Ministry') !!}</td>
                    <td style="padding-right: 12px">  {!! form::label('department_id','Department') !!}</td>
                    <td style="padding-right: 12px">  {!! form::label('regestration_number','Registration Number') !!}</td>
                </tr>
                <tr>
                    <td style="padding-right: 12px">
                        {!! form::select('ministry_id',$ministry,$guarantor->ministry_id,['class'=>'form-control input-sm','id'=>'ministry_id','required'=>'required','autocomplete'=>'off','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}
                        @if($errors->has('ministry_id'))
                            <span>{{$errors->first('ministry_id')}} </span>
                        @endif
                    </td>
                    <td style="padding-right: 12px">
                        {!! form::select('department_id',$department,$guarantor->department_id,['class'=>'form-control input-sm','id'=>'department_id','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                        @if($errors->has('department_id'))
                            <span>{{$errors->first('department_id')}} </span>
                        @endif
                    </td>
                    <td style="padding-right: 12px; width:200px; ">  {!! form::text('registration_number',$guarantor->registration_number,['class'=>'form-control input-sm','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','id'=>'clientid','autocomplete'=>'off']) !!}
                        @if($errors->has('registration_number'))
                            <span class="label label-danger">{{$errors->first('registration_number')}} </span>
                        @endif
                    </td>

                </tr>
            </table>
            <table>
                <tr>
                    <td style="padding-right: 12px" width="100">
                        <b> {!! form::label('date','Date of Registration') !!}</b></td>
                    <td style="padding-right: 12px" width="100">  {!! form::label('reg_year','Year') !!}</td>
                    <td style="padding-right: 12px" width="100">  {!! form::label('dob_month','Month') !!}</td>
                    <td style="padding-right: 12px" width="100">  {!! form::label('dob_day','Day') !!}</td>
                </tr>
                <tr>
                    <td style="padding-right: 12px">  {!! form::label('bs','B.S.') !!}</td>
                    <td style="padding-right: 12px">  {!! form::select('reg_year',['1980'=>'1980','1981'=>'1981',
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
                                        '2074'=>'2074','2075'=>'2075','2076'=>'2076'],$guarantor->reg_year,['class'=>'form-control input-sm','id'=>'dyear','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}</td>

                    <td style="padding-right: 12px">
                        {!! form::select('reg_month',['1'=>' बैशाख', '2'=>'जेष्ठ', '3'=>'अषाढ ', '4'=>'श्रावण', '5'=>'भदाै',
                         '6'=>'असाेज ', '7'=>' कार्तिक ', '8'=>'मंसीर ', '9'=>' पाैष ', '10'=>' माघ', '11'=>'फाल्गुन', '12'=>' चैत्र'],
                         $guarantor->reg_month,['class'=>'form-control input-sm','id'=>'name','required'=>'required']) !!}</td>
                    <td style="padding-right: 12px">  {!! form::select('reg_day',['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8',
                                        '9'=>'9','10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18',
                                        '19'=>'19','20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24','25'=>'25','26'=>'26','27'=>'27','28'=>'28',
                                        '29'=>'29','30'=>'30','31'=>'31','32'=>'32'],$guarantor->reg_day,['class'=>'form-control input-sm','id'=>'name','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}</td>
                </tr>
            </table>

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
                    <td style="padding-right: 12px">  {!! form::select('district_id',$district,$guarantor->district_id,['class'=>'form-control input-sm','id'=>'district_id','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                        @if($errors->has('district_id'))
                            <span class="label label-danger">{{$errors->first('district_id')}} </span>
                        @endif</td>
                    <td style="padding-right: 12px">  {!! form::select('local_bodies_id',$localbody,$guarantor->local_bodies_id,['class'=>'form-control input-sm','id'=>'localbody_id','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                        @if($errors->has('local_bodies_id'))
                            <span class="label label-danger">{{$errors->first('local_bodies_id')}} </span>
                        @endif</td>
                    <td style="padding-right: 12px"> {!! form::select('wardno',['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9',
                                        '10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18','19'=>'19',
                                        '20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24','25'=>'25','26'=>'26','27'=>'27','28'=>'28','29'=>'29',
                                        '30'=>'30','31'=>'31','32'=>'32','33'=>'33','34'=>'34','35'=>'35','36'=>'36','37'=>'37','38'=>'38', '39'=>'39',
                                         '40'=>'40', '41'=>'41', '42'=>'42', '43'=>'43', '44'=>'44', '45'=>'45', '46'=>'46', '47'=>'47', '48'=>'48', '49'
                                         =>'49', '50'=>'50', '51'=>'51', '52'=>'52', '53'=>'53', '54'=>'54', '55'=>'55', '56'=>'56', '57'=>'57', '58'=>'58',
                                          '59'=>'59', '60'=>'60','================'=>'=============='],$guarantor->wardno,['class'=>'form-control input-sm col-sm-2','id'=>'wardno','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                        @if($errors->has('wardno'))
                            <span class="label label-danger">{{$errors->first('wardno')}} </span>
                        @endif</td>
                    <td style="padding-right: 12px"> {!! form::text('phone',$guarantor->phone,['class'=>'form-control input-sm col-sm-2','id'=>'phone','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                        @if($errors->has('phone'))
                            <span class="label label-danger">{{$errors->first('phone')}} </span>
                        @endif</td>
                </tr>

            </table>

            <table>
                <div class="widget-head"><h4 class="form-control col-sm-10" style="background-color: #2aabd2;">
                        Authorized Person Details</h4></div>

                <tr>
                    <td style="padding-right: 12px">  {!! form::label('english_name','Full Name(English)') !!}</td>
                    <td style="padding-right: 12px">  {!! form::label('nepali_name','Full Name(नेपालीमा)') !!}</td>

                    <td style="padding-right: 12px">{!! form::label('gender','Gender') !!}</td>
                </tr>
                <tr>
                    <td style="padding-right: 12px">  {!! form::text('a_english_name',$authorized_person->english_name,['class'=>'form-control input-sm','id'=>'ename','autocomplete'=>'off','style'=>"width:250px; font-size:14px;",'required'=>'required']) !!}
                        @if($errors->has('a_english_name'))
                            <span class="label label-danger">{{$errors->first('a_english_name')}} </span>
                        @endif
                    </td>
                    <td style="padding-right: 12px">  {!! form::text('a_nepali_name',$authorized_person->nepali_name,['class'=>'form-control input-sm','id'=>'name','autocomplete'=>'off','required'=>'required','style'=>"width:250px;font-family:Bishallb,FONTASY_ HIMALI_ TT; width:300px font-size:14px; "]) !!}
                        @if($errors->has('a_nepali_name'))
                            <span class="label label-danger">{{$errors->first('a_nepali_name')}} </span>
                        @endif
                    </td>
                    <td style="padding-right: 12px">  {!! form::select('a_gender',['1'=>'Male','0'=>'Female'],$authorized_person->gender,['class'=>'form-control input-sm','id'=>'gender','style'=>"width:100px",'required'=>'required']) !!}</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="padding-right: 12px">{!! form::label('post','Post') !!}</td>
                    <td style="padding-right: 12px" width="100">
                        <b> {!! form::label('a_date','Date of Birth') !!}</b></td>
                    <td style="padding-right: 12px" width="100">  {!! form::label('a_dob_year','Year') !!}</td>
                    <td style="padding-right: 12px" width="100">  {!! form::label('a_dob_month','Month') !!}</td>
                    <td style="padding-right: 12px" width="100">  {!! form::label('a_dob_day','Day') !!}</td>
                </tr>
                <tr>
                    <td style="padding-right: 12px">
                        {!! form::select('a_post',['k|f]kfO^/'=>'k|f]kfO^/',';fem]bf/'=>';fem]bf/',';+rfns'=>';+rfns','sd{rf/L'=>'sd{rf/L','cWoIf'=>'cWoIf','=========================='=>'=========================='],$authorized_person->post,['class'=>'form-control input-sm','id'=>'post','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT;width:200px; font-size:14px;','autocomplete'=>'off']) !!}      </td>
                    <td style="padding-right: 12px">  {!! form::label('a_bs','B.S.') !!}</td>
                    <td style="padding-right: 12px">  {!! form::select('a_dob_year',['1980'=>'1980','1981'=>'1981',
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
                                        '2074'=>'2074','2075'=>'2075','2076'=>'2076'],$authorized_person->dob_year,['class'=>'form-control input-sm','id'=>'dyear','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                    </td>

                    <td style="padding-right: 12px">
                        {!! form::select('a_dob_month',['1'=>' बैशाख', '2'=>'जेष्ठ', '3'=>'अषाढ ', '4'=>'श्रावण', '5'=>'भदाै',
                         '6'=>'असाेज ', '7'=>' कार्तिक ', '8'=>'मंसीर ', '9'=>' पाैष ', '10'=>' माघ', '11'=>'फाल्गुन', '12'=>' चैत्र'],
                         $authorized_person->dob_month,['class'=>'form-control input-sm','id'=>'name','required'=>'required']) !!}
                    </td>
                    <td style="padding-right: 12px">  {!! form::select('a_dob_day',['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8',
                                        '9'=>'9','10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18',
                                        '19'=>'19','20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24','25'=>'25','26'=>'26','27'=>'27','28'=>'28',
                                        '29'=>'29','30'=>'30','31'=>'31','32'=>'32'],$authorized_person->dob_day,['class'=>'form-control input-sm','id'=>'name','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                    </td>
                </tr>
            </table>
            <br>
            <table>

                <div class="widget-head"><h4 class="form-control col-sm-10" style="background-color: #2aabd2;">
                        Authorized Person Address</h4></div>
                <tr>
                    <td style="padding-right: 12px"
                        width="200">  {!! form::label('a_district_id','District') !!}</td>
                    <td style="padding-right: 12px"
                        width="150">  {!! form::label('a_local_bodies_id','Local Government') !!}</td>
                    <td style="padding-right: 12px" width="100">  {!! form::label('a_wardno','Ward No.') !!}</td>

                </tr>
                <tr>
                    <td style="padding-right: 12px">  {!! form::select('a_district_id',$district,$authorized_person->district_id,['class'=>'form-control input-sm','id'=>'district_id1','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                        @if($errors->has('a_district_id'))
                            <span class="label label-danger">{{$errors->first('a_district_id')}} </span>
                        @endif</td>
                    <td style="padding-right: 12px">  {!! form::select('a_local_bodies_id',$localbody,$authorized_person->local_bodies_id,['class'=>'form-control input-sm','id'=>'localbody_id1','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                        @if($errors->has('a_local_bodies_id'))
                            <span class="label label-danger">{{$errors->first('a_local_bodies_id')}} </span>
                        @endif</td>
                    <td style="padding-right: 12px"> {!! form::select('a_wardno',['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9',
                '10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18','19'=>'19',
                '20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24','25'=>'25','26'=>'26','27'=>'27','28'=>'28','29'=>'29',
                '30'=>'30','31'=>'31','32'=>'32','33'=>'33','34'=>'34','35'=>'35','36'=>'36','37'=>'37','38'=>'38', '39'=>'39',
                '40'=>'40', '41'=>'41', '42'=>'42', '43'=>'43', '44'=>'44', '45'=>'45', '46'=>'46', '47'=>'47', '48'=>'48', '49'
                =>'49', '50'=>'50', '51'=>'51', '52'=>'52', '53'=>'53', '54'=>'54', '55'=>'55', '56'=>'56', '57'=>'57', '58'=>'58',
                '59'=>'59', '60'=>'60','================'=>'=============='],$authorized_person->wardno,['class'=>'form-control input-sm col-sm-2','id'=>'wardno','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                        @if($errors->has('a_wardno'))
                            <span class="label label-danger">{{$errors->first('a_wardno')}} </span>
                        @endif</td>
                </tr>
            </table>
            <br>

            <table>

                <div class="widget-head"><h4 class="form-control col-sm-10" style="background-color: #2aabd2;">
                        Authorized Person Citizenship Details</h4></div>
                <tr>
                    <td style="padding-right: 12px"
                        width="150">  {!! form::label('a_citizenship_number',' Citizenship Number') !!}</td>
                    <td style="padding-right: 12px"
                        width="100">  {!! form::label('a_issued_district','Issued District') !!}</td>

                </tr>
                <tr>
                    <td style="padding-right: 12px"> {!! form::text('a_citizenship_number',$authorized_person->citizenship_number,['class'=>'form-control input-sm','id'=>'citizenshipno','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','required'=>'required','autocomplete'=>'off']) !!}
                        @if($errors->has('a_citizenship_number'))
                            <span class="label label-danger">{{$errors->first('a_citizenship_number')}} </span>
                        @endif</td>
                    <td style="padding-right: 12px">  {!! form::select('a_issued_district',$district,$authorized_person->issued_district,['class'=>'form-control input-sm','id'=>'name','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                        @if($errors->has('a_issued_district'))
                            <span class="label label-danger">{{$errors->first('a_issued_district')}} </span>
                        @endif </td>
                </tr>
            </table>
            <table>
                <tr>
                    <b>
                        <td style="padding-right: 12px"
                            width="100">  {!! form::label('a_date','Issued Date') !!}</td>
                    </b>
                    <td style="padding-right: 12px" width="100">  {!! form::label('a_issued_year','Year') !!}</td>
                    <td style="padding-right: 12px" width="100">  {!! form::label('a_issued_month','Month') !!}</td>
                    <td style="padding-right: 12px" width="100">  {!! form::label('a_issued_day','Day') !!}</td>
                </tr>
                <tr>
                    <td style="padding-right: 12px">  {!! form::label('a_bs','B.S.') !!}</td>
                    <td style="padding-right: 12px">  {!! form::select('a_issued_year',['1980'=>'1980','1981'=>'1981',
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
                '2074'=>'2074','2075'=>'2075','2076'=>'2076'],$authorized_person->isssued_year,['class'=>'form-control input-sm','id'=>'dyear','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                        @if($errors->has('a_issued_year'))
                            <span class="label label-danger">Error!! {{$errors->first('a_issued_year')}} </span><br>
                        @endif   </td>

                    <td style="padding-right: 12px">
                        {!! form::select('a_issued_month',['1'=>' बैशाख', '2'=>'जेष्ठ', '3'=>'अषाढ ', '4'=>'श्रावण', '5'=>'भदाै',
                        '6'=>'असाेज ', '7'=>' कार्तिक ', '8'=>'मंसीर ', '9'=>' पाैष ', '10'=>' माघ', '11'=>'फाल्गुन', '12'=>' चैत्र'],
                        $authorized_person->issued_month,['class'=>'form-control input-sm','id'=>'name','required'=>'required']) !!}
                    </td>
                    <td style="padding-right: 12px">  {!! form::select('a_issued_day',['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8',
                '9'=>'9','10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18',
                '19'=>'19','20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24','25'=>'25','26'=>'26','27'=>'27','28'=>'28',
                '29'=>'29','30'=>'30','31'=>'31','32'=>'32'],$authorized_person->issued_day,['class'=>'form-control input-sm','id'=>'name','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                    </td>
                </tr>
            </table>
            {{--<br>--}}
            <div class="widget-head">
                <h4 class="form-control col-sm-10" style="background-color: #2aabd2;">
                    Authorized Person Three Generation Details </h4></div>
            <table>
                <tr>
                    <td style="padding-right: 12px"
                        width="250">  {!! form::label('a_grandfather_name','(Grand Father/Father InLaw) Name') !!}</td>
                    <td style="padding-right: 12px">  {!! form::label('a_grandfather_relation','Relation with guarantor') !!}</td>
                </tr>
                <tr>
                    <td style="padding-right: 12px"> {!! form::text('a_grandfather_name',$authorized_person->grandfather_name,['class'=>'form-control input-sm','id'=>'gfname','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                        @if($errors->has('a_grandfather_name'))
                            <span class="label label-danger">{{$errors->first('a_grandfather_name')}} </span>
                        @endif</td>
                    <td style="padding-right: 12px">  {!! form::select('a_grandfather_relation',['gflt'=>'gflt','gfltgL'=>'gfltgL','a"xf/L'=>'a"xf/L'],$authorized_person->grandfather_relation,['class'=>'form-control input-sm form-inline','id'=>'name','required'=>'required','autocomplete'=>'off','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}
                        @if($errors->has('a_grandfather_relation'))
                            <span class="label label-danger">{{$errors->first('a_grandfather_relation')}} </span>
                        @endif
                    </td>

                </tr>
                <tr>
                    <td style="padding-right: 12px"
                        width="250">  {!! form::label('a_father_name','Father Name') !!}</td>
                    <td style="padding-right: 12px">  {!! form::label('a_father_relation','Relation with guarantor') !!}</td>

                </tr>
                <tr>
                    <td style="padding-right: 12px">  {!! form::text('a_father_name',$authorized_person->father_name,['class'=>'form-control input-sm','id'=>'father_name','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                        @if($errors->has('a_father_name'))
                            <span class="label label-danger">{{$errors->first('a_father_name')}} </span>
                        @endif
                    </td>
                    <td style="padding-right: 12px">
                        {!! form::select('a_father_relation',['%f]/f '=>'%f]/f ','%f]/L '=>'%f]/L '],$authorized_person->father_relation,['class'=>'form-control input-sm','id'=>'father-relation','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                        @if($errors->has('a_father_relation'))
                            <span class="label label-danger">{{$errors->first('a_father_relation')}} </span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding-right: 12px"
                        width="250">  {!! form::label('a_spouse_name','Spouse Name') !!}</td>
                    <td style="padding-right: 12px">  {!! form::label('a_spouse_relation','Relation with guarantor') !!}</td>

                </tr>
                <tr>
                    <td style="padding-right: 12px">  {!! form::text('a_spouse_name',$authorized_person->spouse_name,['class'=>'form-control input-sm','id'=>'spouse_name','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                        @if($errors->has('a_spouse_name'))
                            <span class="label label-danger">{{$errors->first('a_spouse_name')}} </span>
                        @endif
                    </td>
                    <td style="padding-right: 12px">
                        {!! form::select('a_spouse_relation',['klt '=>'klt ',' klTg'=>'klTg'],$authorized_person->spouse_relation,['class'=>'form-control input-sm','id'=>'spouse_relation','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                        @if($errors->has('a_spouse_relation'))
                            <span class="label label-danger">{{$errors->first('a_spouse_relation')}} </span>
                        @endif
                    </td>
                </tr>
            </table>

            <table>
                <tr>
                    <td>   {!! form::label('status','Status',['class'=>'col-sm-2 control-label']) !!}</td>
                </tr>
                <tr>
                    <td>
                        @if($guarantor->status==1)
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
    </div>

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
    <script>
        $('#district_id1').change(function () {
            var district_id = $(this).val();
            console.log(district_id);
            $.ajax({
                url: '/select_local_body',
                data: {'district_id': district_id},
                method: 'post',
                success: function (resp) {
                    $('#localbody_id1').html('');
                    $('#localbody_id1').html(resp);
                }
            })
        })
    </script>
@endsection