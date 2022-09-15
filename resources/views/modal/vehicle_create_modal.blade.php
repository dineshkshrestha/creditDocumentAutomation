{{--auto loan for hire purchase --}}
<div class="modal fade" id="vehicle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

    <!-- Change class .modal-sm to change the size of the modal -->
    <div class="modal-dialog modal-lg" role="document">


        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title w-100" id="myModalLabel">Add New Vehicle</h4>
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
                                Enter Vehicle Details</h4></div>
                        <tr>

                            <td width="150"
                                style="padding-right: 12px">  {!! form::label('model_number','Model Number') !!}</td>
                            <td width="100"
                                style="padding-right: 12px">  {!! form::label('registration_number','Registration Number') !!}</td>

                        </tr>
                        <tr>

                            <td style="padding-right: 12px">  {!! form::text('model_number[]','',['class'=>'form-control input-sm','id'=>'model_number','required'=>'required','autocomplete'=>'off']) !!}
                                @if($errors->has('model_number'))
                                    <span>{{$errors->first('model_number')}} </span>
                                @endif</td>
                            <td style="padding-right: 12px"> {!! form::text('registration_number[]','',['class'=>'form-control input-sm col-sm-2','id'=>'registration_number','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                                @if($errors->has('registration_number'))
                                    <span>{{$errors->first('registration_number')}} </span>
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
                            <td style="padding-right: 12px;  ">{!! form::text('engine_number[]','',['class'=>'form-control input-sm','id'=>'engine_number','autocomplete'=>'off','required'=>'required']) !!}</td>
                            <td style=" padding-right: 12px;  ">{!! form::text('chassis_number[]','',['class'=>'form-control input-sm','id'=>'chassis_number','autocomplete'=>'off','required'=>'required']) !!}</td>
                            <td><a class="btn btn-success add-more" title="Add field"><i
                                            class="glyphicon glyphicon-plus"></i> Add More </a></td>
                        </tr>
                    </table>
                </div>
                <!-- Copy Fields-These are the fields which we get through jquery and then add after the above input,-->
                <div class="copy-fields hide col-sm-12">
                    <div class="control-group">

                        <table>
                            <div class="widget-head"><h4 class="form-control col-sm-10"
                                                         style="background-color: #2aabd2;">
                                    Enter Vehicle Details Again</h4></div>
                            <tr>
                                <td width="150"
                                    style="padding-right: 12px">  {!! form::label('model_number','Model Number') !!}</td>
                                <td width="100"
                                    style="padding-right: 12px">  {!! form::label('registration_number','Registration Number') !!}</td>

                            </tr>
                            <tr>
                                <td style="padding-right: 12px">  {!! form::text('model_number[]','',['class'=>'form-control input-sm','id'=>'model_number','autocomplete'=>'off']) !!}
                                    @if($errors->has('model_number'))
                                        <span>{{$errors->first('model_number')}} </span>
                                    @endif</td>
                                <td style="padding-right: 12px"> {!! form::text('registration_number[]','',['class'=>'form-control input-sm col-sm-2','id'=>'registration_number','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                                    @if($errors->has('registration_number'))
                                        <span>{{$errors->first('registration_number')}} </span>
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
                                <td style="padding-right: 12px;  ">{!! form::text('engine_number[]','',['class'=>'form-control input-sm','id'=>'engine_number','autocomplete'=>'off']) !!}</td>
                                <td style=" padding-right: 12px; ">{!! form::text('chassis_number[]','',['class'=>'form-control input-sm','id'=>'chassis_number','autocomplete'=>'off']) !!}</td>
                            <td>
                                <a class="btn btn-danger remove" title="Remove field"><i
                                            class="glyphicon glyphicon-remove"></i> Remove</a></td>
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

        </div>
    </div>
</div>
{!! Form::close() !!}