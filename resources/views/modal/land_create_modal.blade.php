{{--land --}}
<div class="modal fade" id="Land" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

    <!-- Change class .modal-sm to change the size of the modal -->
    <div class="modal-dialog modal-lg" role="document">


        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title w-100" id="myModalLabel">Add New Land Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                {{--new form--}}
                <div class="after-add-more col-sm-12 ">
                    <table>
                        <div class="widget-head"><h4 class="form-control col-sm-10"
                                                     style="background-color: #2aabd2;">
                                Enter Land Details</h4></div>
                        <tr>
                            <td width="150"
                                style="padding-right: 12px">  {!! form::label('district_id','District') !!}</td>
                            <td width="150"
                                style="padding-right: 12px">  {!! form::label('localgovt_id','Local Government') !!}</td>
                            <td width="100"
                                style="padding-right: 12px">  {!! form::label('wardno','Ward No.') !!}</td>

                        </tr>
                        <tr>
                            <td style="padding-right: 12px">  {!! form::select('district_id[]',$district,'',['class'=>'form-control input-sm','id'=>'district_id','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                                @if($errors->has('district_id'))
                                    <span>{{$errors->first('district_id')}} </span>
                                @endif</td>
                            <td style="padding-right: 12px">  {!! form::select('local_body_id[]',$localbody,'',['class'=>'form-control input-sm','id'=>'localgovt_id','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                                @if($errors->has('local_body_id'))
                                    <span>{{$errors->first('local_body_id')}} </span>
                                @endif</td>
                            <td style="padding-right: 12px"> {!! form::select('wardno[]',['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9',
                                        '10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18','19'=>'19',
                                        '20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24','25'=>'25','26'=>'26','27'=>'27','28'=>'28','29'=>'29',
                                        '30'=>'30','31'=>'31','32'=>'32','33'=>'33','34'=>'34','35'=>'35','36'=>'36','37'=>'37','38'=>'38', '39'=>'39',
                                         '40'=>'40', '41'=>'41', '42'=>'42', '43'=>'43', '44'=>'44', '45'=>'45', '46'=>'46', '47'=>'47', '48'=>'48', '49'
                                         =>'49', '50'=>'50', '51'=>'51', '52'=>'52', '53'=>'53', '54'=>'54', '55'=>'55', '56'=>'56', '57'=>'57', '58'=>'58',
                                          '59'=>'59', '60'=>'60','================'=>'=============='],'',['class'=>'form-control input-sm col-sm-2','id'=>'wardno','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
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
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; padding-right: 12px; font-size:14px; ">{!! form::text('sheet_no[]','',['class'=>'form-control input-sm','id'=>'sheet_no','autocomplete'=>'off']) !!}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; padding-right: 12px; font-size:14px; ">{!! form::text('kitta_no[]','',['class'=>'form-control input-sm','id'=>'kitta_no','autocomplete'=>'off','required'=>'required']) !!}</td>
                            <td><a class="btn btn-success add-more" title="Add field"><i
                                            class="glyphicon glyphicon-plus"></i> Add More </a></td>
                        </tr>
                        <tr>
                            <td width="200"
                                style="padding-right: 12px">{!! form::label('area','Area') !!}</td>
                            <td width="300"
                                style="padding-right: 12px">{!! form::label('remarks','Remarks') !!}</td>
                        </tr>
                        <tr>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; padding-right: 12px; font-size:14px; ">{!! form::text('area[]','',['class'=>'form-control input-sm','id'=>'area','autocomplete'=>'off','required'=>'required']) !!}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; padding-right: 12px;">{!! form::text('remarks[]','',['class'=>'form-control input-sm','id'=>'remarks','autocomplete'=>'off']) !!}</td>
                        </tr>

                        <tr>
                            <td colspan="2"  style="padding-right: 12px">{!! form::label('Malpot','Land Revenue Office') !!}</td>
                                  </tr>
                        <tr>
                            <td colspan="2" style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; padding-right: 12px;">{!! form::text('malpot[]','',['class'=>'form-control input-sm','id'=>'malpot','autocomplete'=>'off','required'=>'required','placeholder'=>'मालपाेत कार्यलय राख्नुहाेस्']) !!}</td>
                        </tr>






                    </table>
                </div>
                <!-- Copy Fields-These are the fields which we get through jquery and then add after the above input,-->
                <div class="copy-fields hide col-sm-12">
                    <div class="control-group">

                        <table>
                            <div class="widget-head"><h4 class="form-control col-sm-10"
                                                         style="background-color: #2aabd2;">
                                    Enter Land Details Again</h4></div>

                            <tr>
                                <td width="150"
                                    style="padding-right: 12px">  {!! form::label('district_id','District') !!}</td>
                                <td width="150"
                                    style="padding-right: 12px">  {!! form::label('localgovt_id','Local Government') !!}</td>
                                <td width="100"
                                    style="padding-right: 12px">  {!! form::label('wardno','Ward No.') !!}</td>

                            </tr>
                            <tr>
                                <td style="padding-right: 12px">  {!! form::select('district_id[]',$district,'',['class'=>'form-control input-sm','id'=>'district_id','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                                    @if($errors->has('district_id'))
                                        <span>{{$errors->first('district_id')}} </span>
                                    @endif</td>
                                <td style="padding-right: 12px">  {!! form::select('local_body_id[]',$localbody,'',['class'=>'form-control input-sm','id'=>'localgovt_id','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                                    @if($errors->has('local_body_id'))
                                        <span>{{$errors->first('local_body_id')}} </span>
                                    @endif</td>
                                <td style="padding-right: 12px"> {!! form::select('wardno[]',['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9',
                                        '10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18','19'=>'19',
                                        '20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24','25'=>'25','26'=>'26','27'=>'27','28'=>'28','29'=>'29',
                                        '30'=>'30','31'=>'31','32'=>'32','33'=>'33','34'=>'34','35'=>'35','36'=>'36','37'=>'37','38'=>'38', '39'=>'39',
                                         '40'=>'40', '41'=>'41', '42'=>'42', '43'=>'43', '44'=>'44', '45'=>'45', '46'=>'46', '47'=>'47', '48'=>'48', '49'
                                         =>'49', '50'=>'50', '51'=>'51', '52'=>'52', '53'=>'53', '54'=>'54', '55'=>'55', '56'=>'56', '57'=>'57', '58'=>'58',
                                          '59'=>'59', '60'=>'60','================'=>'=============='],'',['class'=>'form-control input-sm col-sm-2','id'=>'wardno','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
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
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; padding-right: 12px; font-size:14px; ">{!! form::text('sheet_no[]','',['class'=>'form-control input-sm','id'=>'sheet_no','autocomplete'=>'off']) !!}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; padding-right: 12px; font-size:14px; ">{!! form::text('kitta_no[]','',['class'=>'form-control input-sm','id'=>'kitta_no','autocomplete'=>'off']) !!}</td>
                                <td>
                                    <a class="btn btn-danger remove" title="Remove field"><i
                                                class="glyphicon glyphicon-remove"></i> Remove</a></td>
                            </tr>
                            <tr>
                                <td width="200"
                                    style="padding-right: 12px">{!! form::label('area','Area') !!}</td>
                                <td width="300"
                                    style="padding-right: 12px">{!! form::label('remarks','Remarks') !!}</td>
                            </tr>
                            <tr>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; padding-right: 12px; font-size:14px; ">{!! form::text('area[]','',['class'=>'form-control input-sm','id'=>'area','autocomplete'=>'off']) !!}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; padding-right: 12px;">{!! form::text('remarks[]','',['class'=>'form-control input-sm','id'=>'remarks','autocomplete'=>'off']) !!}</td>
                            </tr>

                            <tr>
                                <td colspan="2"  style="padding-right: 12px">{!! form::label('Malpot','Land Revenue Office') !!}</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; padding-right: 12px;">{!! form::text('malpot[]','',['class'=>'form-control input-sm','id'=>'malpot','autocomplete'=>'off','placeholder'=>'मालपाेत कार्यलय राख्नुहाेस्']) !!}</td>
                            </tr>

                        </table>


                        <br>
                    </div>
                </div>


                <br>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                {!! Form::submit('Save',['class'=>'btn btn-primary']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>