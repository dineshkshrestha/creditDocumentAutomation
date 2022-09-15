@extends ('layouts.backend')
@section('title','Final Loan Details')
@section('heading','Edit or Add Loan Details')
@section('small_heading','Loan Details')
@section('borrower')
    {{App\PersonalBorrower::find($bid)->english_name}}
@endsection
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li><a href="{{route('joint_borrower.index')}}">Borrower</a></li>
    <li><a href="{{route('joint_guarantor.index',$bid)}}">Guarantor</a></li>
    <li><a href="{{route('joint_property.index',$bid)}}">Property</a></li>
    <li><a href="{{route('joint_facilities.index',$bid)}}">Facilities</a></li>
    <li class="active"><strong>Loan Details</strong></li>
@endsection

@section('content')
    <div class="col-sm-8">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">

                </h3>
                <span><a class="btn btn-info btn-xm pull-right" href="{{route('personal_loan.proceed',$bid)}}">                 <i
                                class="fa fa-save fa-fw"></i>Save and Review Entered Data</a></span>
            </div>
            <!-- /.box-header -->

            <div class="box-body">

                {!! Form::open(['route' => 'corporate_loan.store','method'=>'post'],['class'=>'form-horizontal']) !!}
                {!! form::hidden('bid',$bid,[]) !!}
                @if($loan)
                    {!! form::hidden('id',$loan->id,[]) !!}
                    <table>

                        <tr>
                            <td width="200"
                                style="padding-right: 12px">  {!! form::select('loan_type',$facility,$loan->loan_type_id,['class'=>'form-control input-sm','id'=>'facility','autocomplete'=>'off','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}
                                @if($errors->has('loan_type'))
                                    <span class="label label-danger">{{$errors->first('loan_type')}} </span>
                                @endif

                            </td>

                            <td style="padding-right: 12px">  {!! form::text('amount',$loan->loan_amount,['placeholder'=>'Please enter loan amount.','class'=>'form-control input-sm ','id'=>'number','autocomplete'=>'off']) !!}
                                @if($errors->has('amount'))
                                    <span class="label label-danger">{{$errors->first('amount')}} </span>
                                @endif
                            </td>

                            <td style="padding-right: 12px"><input type="button" class="btn btn-success btn-sm ">
                            </td>
                        </tr>


                        <tr>
                            <td style="padding-right: 12px">  {!! form::label('words','In Words',[]) !!}</td>
                        <tr>
                            <td colspan="3" width="600"
                                style=" padding-right: 12px; font-size:14px; ">  {!! form::text('words',$loan->loan_amount_words,['class'=>'form-control input-sm','id'=>'result','autocomplete'=>'off','style'=>" font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; "]) !!}

                                @if($errors->has('words'))
                                    <span class="label label-danger">{{$errors->first('words')}} </span>
                                @endif
                            </td>

                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td width="200" style="padding-right: 12px">
                                <b> {!! form::label('date','Offer Letter Date') !!}</b>
                            </td>
                            <td width="100"
                                style="padding-right: 12px">  {!! form::label('offerletter_year','Year') !!}</td>
                            <td width="100"
                                style="padding-right: 12px">  {!! form::label('offerletter_month','Month') !!}</td>
                            <td width="100"
                                style="padding-right: 12px">  {!! form::label('offerletter_day','Day') !!}</td>
                        </tr>
                        <tr>
                            <td style="padding-right: 12px">  {!! form::label('bs','B.S.') !!}</td>
                            <td style="padding-right: 12px"> {!! form::select('offerletter_year',['1980'=>'1980','1981'=>'1981',
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
                        '2044'=>'2044','2045'=>'2045','2046'=>'2046','2047'=>'2048','2049'=>'2049',
                        '2050'=>'2050','2051'=>'2051','2052'=>'2052','2053'=>'2053','2054'=>'2054',
                        '2055'=>'2055','2056'=>'2056','2057'=>'2057','2058'=>'2058','2059'=>'2059',
                        '2060'=>'2060','2061'=>'2061',
                        '2062'=>'2062','2063'=>'2063','2064'=>'2064','2065'=>'2065',
                        '2066'=>'2066','2067'=>'2067','2068'=>'2068','2069'=>'2069',
                        '2070'=>'2070','2071'=>'2071','2072'=>'2072','2073'=>'2073',
                        '2074'=>'2074','2075'=>'2075','2076'=>'2076'],$loan->offerletter_year,['class'=>'form-control input-sm','id'=>'offerletter_year','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}</td>

                            <td style="padding-right: 12px">
                                {!! form::select('offerletter_month',['1'=>' बैशाख', '2'=>'जेष्ठ', '3'=>'अषाढ ', '4'=>'श्रावण', '5'=>'भदाै',
                                                                '6'=>'असाेज ', '7'=>' कार्तिक ', '8'=>'मंसीर ', '9'=>' पाैष ', '10'=>' माघ', '11'=>'फाल्गुन', '12'=>' चैत्र'],
                                                                $loan->offerletter_month,['class'=>'form-control input-sm','id'=>'offerletter_month']) !!}
                            </td>
                            <td style="padding-right: 12px">   {!! form::select('offerletter_day',['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8',
                        '9'=>'9','10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18',
                        '19'=>'19','20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24','25'=>'25','26'=>'26','27'=>'27','28'=>'28',
                        '29'=>'29','30'=>'30','31'=>'31','32'=>'32'],$loan->offerletter_day,['class'=>'form-control input-sm','id'=>'offerletter_day','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td>{!! form::label('branch_id','Branch') !!}</td>
                            <td>{!! form::label('loan_purpose','Loan Purpose') !!}</td>
                        </tr>
                        <tr>
                            <td style="padding-right: 12px">{!! form::select('branch_id',$branch,
                                        $loan->branch_id,['class'=>'form-control input-sm','id'=>'branch_id','style'=>" font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; "]) !!}
                                @if($errors->has('branch_id'))
                                    <span class="label label-danger">{{$errors->first('branch_id')}} </span>
                                @endif
                            </td>
                            <td style="padding-right: 12px">
                                {!! form::text('loan_purpose',$loan->loan_purpose,['class'=>'form-control input-sm','id'=>'result','autocomplete'=>'off','style'=>" font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; "]) !!}
                                @if($errors->has('loan_purpose'))
                                    <span class="label label-danger">{{$errors->first('loan_purpose')}} </span>
                                @endif
                            </td>

                        </tr>


                    </table>
                @else
                    <table>

                        <tr>
                            <td width="200"
                                style="padding-right: 12px;">  {!! form::label('loan_type','Loan Type') !!}</td>
                            <td width="200"
                                style="padding-right: 12px;">  {!! form::label('amount','Loan Amount') !!}</td>
                        </tr>

                        <tr>
                            <td width="200"
                                style="padding-right: 12px">  {!! form::select('loan_type',$facility,'',['class'=>'form-control input-sm','id'=>'facility','autocomplete'=>'off','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}
                                @if($errors->has('loan_type'))
                                    <span class="label label-danger">{{$errors->first('loan_type')}} </span>
                                @endif
                            </td>
                            <td style="padding-right: 12px">  {!! form::text('amount','',['placeholder'=>'Please enter loan amount.','class'=>'form-control input-sm ','id'=>'number','autocomplete'=>'off']) !!}

                                @if($errors->has('amount'))
                                    <span class="label label-danger">{{$errors->first('amount')}} </span>
                                @endif
                            </td>

                            <td style="padding-right: 12px"><input type="button" class="btn btn-success btn-sm "
                                                                   value="Convert to word"
                                                                   onclick="convert_no_to_words()"></td>
                        </tr>


                        <tr>
                            <td style="padding-right: 12px">  {!! form::label('words','In Words',[]) !!}</td>
                        <tr>
                            <td colspan="3" width="600"
                                style=" padding-right: 12px; font-size:14px; ">  {!! form::text('words','',['class'=>'form-control input-sm','id'=>'result','autocomplete'=>'off','style'=>" font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;"]) !!}

                                @if($errors->has('words'))
                                    <span class="label label-danger">{{$errors->first('words')}} </span>
                                @endif

                            </td>

                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td width="200" style="padding-right: 12px">
                                <b> {!! form::label('date','Offer Letter Date') !!}</b>
                            </td>
                            <td width="100"
                                style="padding-right: 12px">  {!! form::label('offerletter_year','Year') !!}</td>
                            <td width="100"
                                style="padding-right: 12px">  {!! form::label('offerletter_month','Month') !!}</td>
                            <td width="100"
                                style="padding-right: 12px">  {!! form::label('offerletter_day','Day') !!}</td>
                        </tr>
                        <tr>
                            <td style="padding-right: 12px">  {!! form::label('bs','B.S.') !!}</td>
                            <td style="padding-right: 12px"> {!! form::select('offerletter_year',['1980'=>'1980','1981'=>'1981',
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
                        '2044'=>'2044','2045'=>'2045','2046'=>'2046','2047'=>'2048','2049'=>'2049',
                        '2050'=>'2050','2051'=>'2051','2052'=>'2052','2053'=>'2053','2054'=>'2054',
                        '2055'=>'2055','2056'=>'2056','2057'=>'2057','2058'=>'2058','2059'=>'2059',
                        '2060'=>'2060','2061'=>'2061',
                        '2062'=>'2062','2063'=>'2063','2064'=>'2064','2065'=>'2065',
                        '2066'=>'2066','2067'=>'2067','2068'=>'2068','2069'=>'2069',
                        '2070'=>'2070','2071'=>'2071','2072'=>'2072','2073'=>'2073',
                        '2074'=>'2074','2075'=>'2075','2076'=>'2076'],'2075',['class'=>'form-control input-sm','id'=>'offerletter_year','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}</td>

                            <td style="padding-right: 12px">
                                {!! form::select('offerletter_month',['1'=>' बैशाख', '2'=>'जेष्ठ', '3'=>'अषाढ ', '4'=>'श्रावण', '5'=>'भदाै',
                                                                '6'=>'असाेज ', '7'=>' कार्तिक ', '8'=>'मंसीर ', '9'=>' पाैष ', '10'=>' माघ', '11'=>'फाल्गुन', '12'=>' चैत्र'],
                                                                '',['class'=>'form-control input-sm','id'=>'offerletter_month']) !!}
                            </td>
                            <td style="padding-right: 12px">   {!! form::select('offerletter_day',['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8',
                        '9'=>'9','10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18',
                        '19'=>'19','20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24','25'=>'25','26'=>'26','27'=>'27','28'=>'28',
                        '29'=>'29','30'=>'30','31'=>'31','32'=>'32'],'',['class'=>'form-control input-sm','id'=>'offerletter_day','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td>{!! form::label('branch_id','Branch') !!}</td>
                            <td>{!! form::label('loan_purpose','Loan Purpose') !!}</td>
                        </tr>
                        <tr>
                            <td style="padding-right: 12px">{!! form::select('branch_id',$branch,
                                        '',['class'=>'form-control input-sm','id'=>'branch_id','style'=>" font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; "]) !!}
                                @if($errors->has('branch_id'))
                                    <span class="label label-danger">{{$errors->first('branch_id')}} </span>
                                @endif
                            </td>
                            <td style="padding-right: 12px">
                                {!! form::text('loan_purpose','',['class'=>'form-control input-sm','id'=>'result','autocomplete'=>'off','style'=>" font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; "]) !!}
                                @if($errors->has('loan_purpose'))
                                    <span class="label label-danger">{{$errors->first('loan_purpose')}} </span>
                                @endif
                            </td>

                        </tr>


                    </table>
                @endif
                <div class="box-footer">


                    {!! Form::submit('Save',['class'=>'btn btn-primary']) !!}

                </div>


                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{asset('assets\backend\dist\js\number to word.js')}}"></script>
    <script> function convert_no_to_words() {
            document.getElementById('result').value = translate_nepali_num_into_words(document.getElementById('number').value);
        }
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });

    </script>
@endsection