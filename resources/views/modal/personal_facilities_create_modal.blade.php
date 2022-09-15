{{--personal Facilities--}}
<div class="modal fade" id="Facilities" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

    <!-- Change class .modal-sm to change the size of the modal -->
    <div class="modal-dialog modal-lg" role="document">


        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title w-100" id="myModalLabel">Add New Facilities Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">


                {!! form::hidden('bid',$bid,['class'=>'form-control','id'=>'bid']) !!}
                <div class="col-sm-12 ">
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
                            <td style="padding-right: 12px">  {!! form::select('facility',$facility,'',['class'=>'form-control input-sm','id'=>'facility','autocomplete'=>'off','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}
                                @if($errors->has('facility'))
                                    <span>{{$errors->first('facility')}} </span>
                                @endif</td>

                            <td style="padding-right: 12px">  {!! form::number('amount','',['required'=>'required','class'=>'form-control input-sm','id'=>'amount','min'=>'0','max'=>'99999999999','autocomplete'=>'off','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}
                                @if($errors->has('amount'))
                                    <span>{{$errors->first('amount')}} </span>
                                @endif
                            </td>
                            <td style="padding-right: 12px">

                                {!! form::checkbox('within','1','0') !!}

                            </td>


                        </tr>

                        <tr>
                            <td style="padding-right: 12px">  {!! form::label('rate','Rate(%)') !!}</td>
                            <td style="padding-right: 12px">  {!! form::label('remarks','Remarks') !!}</td>
                            <td style="padding-right: 12px">  {!! form::label('tenure','Tenure') !!}</td>
                        </tr>
                        <tr>


                            <td style="padding-right: 12px">  {!! form::text('rate','',['required'=>'required','class'=>'form-control input-sm','id'=>'rate','min'=>'0','max'=>'100','autocomplete'=>'off','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}</td>
                            @if($errors->has('rate'))
                                <span>{{$errors->first('rate')}} </span>
                            @endif
                            <td style="padding-right: 12px">  {!! form::text('remarks','',['class'=>'form-control input-sm','id'=>'amount','autocomplete'=>'off','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}</td>
                            @if($errors->has('remarks'))
                                <span>{{$errors->first('remarks')}} </span>
                            @endif

                            <td style="padding-right: 12px">  {!! form::text('tenure','======================================================',['class'=>'form-control input-sm','id'=>'tenure','autocomplete'=>'off','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','placeholder'=>'4 aif{, lbg, dlxgf']) !!}</td>
                            @if($errors->has('tenure'))
                                <span>{{$errors->first('tenure')}} </span>
                            @endif

                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td width="100" style="padding-right: 12px">
                                <b> {!! form::label('date','Expiry Date') !!}</b></td>
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
                                        '2044'=>'2044','2045'=>'2045','2046'=>'2046','2047'=>'2047','2048'=>'2048','2049'=>'2049',
                                        '2050'=>'2050','2051'=>'2051','2052'=>'2052','2053'=>'2053','2054'=>'2054',
                                        '2055'=>'2055','2056'=>'2056','2057'=>'2057','2058'=>'2058','2059'=>'2059',
                                        '2060'=>'2060','2061'=>'2061',
                                        '2062'=>'2062','2063'=>'2063','2064'=>'2064','2065'=>'2065',
                                        '2066'=>'2066','2067'=>'2067','2068'=>'2068','2069'=>'2069',
                                        '2070'=>'2070','2071'=>'2071','2072'=>'2072','2073'=>'2073',
                                        '2074'=>'2074','2075'=>'2075'],'',['class'=>'form-control input-sm','id'=>'dyear','style'=>'font-size:14px;','autocomplete'=>'off']) !!}</td>

                            <td style="padding-right: 12px">
                                {!! form::select('tmonth',[''=>'','1'=>'JAN', '2'=>'FEB', '3'=>'MAR', '4'=>'APR', '5'=>'MAY',
                                 '6'=>'JUN', '7'=>'JUL', '8'=>'AUG', '9'=>'SEPT', '10'=>'OCT', '11'=>'NOV', '12'=>'DEC'],
                                 '',['class'=>'form-control input-sm','id'=>'name']) !!}</td>
                            <td style="padding-right: 12px">  {!! form::select('tday',[''=>'','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8',
                                        '9'=>'9','10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18',
                                        '19'=>'19','20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24','25'=>'25','26'=>'26','27'=>'27','28'=>'28',
                                        '29'=>'29','30'=>'30','31'=>'31','32'=>'32'],'',['class'=>'form-control input-sm','id'=>'name','style'=>'font-size:14px;','autocomplete'=>'off']) !!}</td>
                          </tr>
                    </table>


                    <br>

                </div>


            </div>
            <div class="modal-footer">
                <a class="label label-danger btn-xs pull-left " href="#">
                    <i class="fa fa-info-circle fa-fw"></i>
                    You can enter expiry date or Tenure. One of them is required. If you leave it blank space will be
                    saved*
                </a>


                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                {!! Form::submit('Save',['class'=>'btn btn-primary btn-sm']) !!}

                {!! Form::close() !!}
            </div>
        </div>
    </div>

</div>
