<!-- Share -->
<div class="modal fade" id="Share" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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
                <div class="after-add-more11 col-sm-12 ">

                    <table>

                        <div class="widget-head"><h4 class="form-control col-sm-10"
                                                     style="background-color: #2aabd2;">
                                Enter Share Details</h4></div>

                        <tr>
                            <td width="200"> {!! form::label('share_client_id','Share Client ID') !!}</td>
                            <td width="200"> {!! form::label('insin','ISIN Name') !!}</td>
                            <td width="200"> {!! form::label('share_type','Share Type') !!}</td>

                        </tr>
                        <tr>
                            <td style="padding-right: 12px; font-size:14px; ">{!! form::text('client_id[]','',['class'=>'form-control input-sm','id'=>'instclientid','autocomplete'=>'off']) !!}</td>
                            <td style=" padding-right: 12px; font-size:14px; ">{!! form::select('isin[]',$isin,'',['class'=>'form-control input-sm','id'=>'isin','autocomplete'=>'off','required'=>'required']) !!}</td>
                            <td style=" padding-right: 12px; font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{!! form::select('share_type[]',[';fwf/)f z]o/'=>';fwf/)f z]o/',';:+yfks z]o/'=>';:+yfks z]o/','cu|flwsf/ z]o/'=>'cu|flwsf/ z]o/','C)Fkq'=>'C)Fkq'],';fwf/)f z]o/',['class'=>'form-control input-sm','id'=>'share_type','autocomplete'=>'off']) !!}</td>
                        </tr>

                        <tr>
                            <td width="200"> {!! form::label('dpid','DPID') !!}</td>
                            <td width="300"> {!! form::label('kitta ','Kitta') !!}</td>
                        </tr>
                        <tr>
                            <td style="padding-right: 12px; font-size:14px; ">{!! form::text('dpid[]','',['class'=>'form-control input-sm','id'=>'dpid','autocomplete'=>'off','required'=>'required']) !!}</td>
                            <td style="padding-right: 12px; font-size:14px; ">{!! form::text('kitta[]','',['class'=>'form-control input-sm','id'=>'kitta','autocomplete'=>'off','required'=>'required']) !!}</td>
                            <td><a class="btn btn-success add-more11" title="Add field"><i
                                            class="glyphicon glyphicon-plus"></i>Add More </a></td>
                        </tr>
                    </table>
                    <br>

                </div>
                <!-- Copy Fields-These are the fields which we get through jquery and then add after the above input,-->
                <div class="copy-fields11 hide col-sm-12">
                    <div class="control-group">
                        <table>

                            <div class="widget-head"><h5 class="form-control col-sm-10"
                                                         style="background-color: #2aabd2;">
                                    Enter Share Details Again</h5></div>

                            <tr>
                                <td width="200">Share Client Id</td>
                                <td width="200">ISIN</td>
                                <td width="200">Share Type</td>
                            </tr>
                            <tr>
                                <td style="padding-right: 12px; font-size:14px; ">{!! form::text('client_id[]','',['class'=>'form-control input-sm','id'=>'instclientid','autocomplete'=>'off']) !!}</td>
                                <td style=" padding-right: 12px;  font-size:14px; ">{!! form::select('isin[]',$isin,'',['class'=>'form-control input-sm','id'=>'isin','autocomplete'=>'off']) !!}</td>
                                <td style=" padding-right: 12px; font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{!! form::select('share_type[]',[';fwf/)f z]o/'=>';fwf/)f z]o/',';:+yfks z]o/'=>';:+yfks z]o/','cu|flwsf/ z]o/'=>'cu|flwsf/ z]o/','C)Fkq'=>'C)Fkq'],';fwf/)f z]o/',['class'=>'form-control input-sm','id'=>'share_type','autocomplete'=>'off']) !!}</td>

                            </tr>

                            <tr>
                                <td width="200">DP Id</td>
                                <td width="300">Kitta</td>
                            </tr>
                            <tr>
                                <td style="padding-right: 12px;  font-size:14px; ">{!! form::text('dpid[]','',['class'=>'form-control input-sm','id'=>'dpid','autocomplete'=>'off']) !!}</td>
                                <td style="padding-right: 12px; font-size:14px; ">{!! form::text('kitta[]','',['class'=>'form-control input-sm','id'=>'kitta','autocomplete'=>'off']) !!}</td>
                                <td>
                                    <a class="btn btn-danger remove" title="Remove field11"><i
                                                class="glyphicon glyphicon-remove"></i>Remove</a>
                                </td>
                            </tr>
                        </table>


                        <br>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                {!! Form::submit('Save',['class'=>'btn btn-primary']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>