@extends ('layouts.backend')
@section('title','Document Type Choose')
@section('heading','Tick Document type to create new Document.')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li class="active"><strong> Document Type</strong></li>
@endsection
@section('content')
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">
                Borrower Info.
            </h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            @php
                $i=1;
            @endphp
            <table>
                @foreach ($borrowers as $borrower)
                    @if($borrower)
                        <tr>
                            <th style="color: orangered;">Joint Borrower-- {{$i++}}</th>
                        </tr>
                        <tr>
                            <th style="padding-right: 8px; float: right; ">Name:</th>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;">{{$borrower->nepali_name}}</td>
                            <td width="200"></td>
                            <th style="padding-right: 8px; float: right; ">English Name:</th>
                            <td>{{$borrower->english_name}}</td>
                        </tr>
                        <tr>
                            <th style="padding-right: 8px; float: right; ">Client ID:</th>
                            <td>{{$borrower->client_id}}</td>
                            <td width="200"></td>
                            <th style="padding-right: 8px; float: right; ">Gender:</th>
                            <td>
                                @if($borrower->gender=='1')
                                    Male
                                @endif
                                @if($borrower->gender=='0')
                                    Female
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th style="padding-right: 8px; float: right; ">GrandFather/Father Inlaw:
                            </th>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$borrower->grandfather_name}}</td>
                            <td width="200"></td>
                            <th style="padding-right: 8px; float: right; ">Relation</th>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$borrower->grandfather_relation}}</td>
                        </tr>
                        <tr>
                            <th style="padding-right: 8px; float: right; ">Father:</th>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$borrower->father_name}}</td>
                            <td width="200"></td>
                            <th style="padding-right: 8px; float: right; ">Relation</th>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$borrower->father_relation}}</td>
                        </tr>
                        <tr>
                            <th style="padding-right: 8px; float: right; ">Spouse:</th>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$borrower->spouse_name}}</td>
                            <td width="200"></td>
                            <th style="padding-right: 8px; float: right; ">Relation</th>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$borrower->spouse_relation}}</td>
                        </tr>
                        <tr>
                            <th style="padding-right: 8px; float: right; ">District:</th>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\District::find($borrower->district_id)->name}}
                            </td>
                            <td width="200"></td>
                            <th style="padding-right: 8px; float: right; ">Local Government:</th>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\LocalBodies::find($borrower->local_bodies_id)->name}}</td>
                        </tr>
                        <tr>
                            <th style="padding-right: 8px; float: right; ">Ward No.:</th>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$borrower->wardno}}</td>
                            <td width="200"></td>
                            <th style="padding-right: 8px; float: right; ">Citizenship No.:</th>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$borrower->citizenship_number}}</td>
                        </tr>
                        <tr>
                            <th style="padding-right: 8px; float: right; ">Issued District:</th>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\District::find($borrower->issued_district)->name}}</td>
                            <td width="200"></td>
                            <th style="padding-right: 8px; float: right; ">Issued Date</th>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">

                                {{$borrower->issued_year}}÷{{$borrower->issued_month}}
                                ÷{{$borrower->issued_day}}</td>
                        </tr>

                        <tr>
                            <th style=" padding-right: 8px; float: right; ">Created By:</th>
                            <td>{{App\User::find($borrower->created_by)->name}}</td>
                            <td width="200"></td>
                            <th style="padding-right: 8px; float: right; ">Updated By:</th>

                            <td>
                                @if(isset($borrower->updated_by)) {{App\User::find($borrower->updated_by)->name}}
                                @else
                                    Wasn't Updated Yet !!
                                @endif

                            </td>
                        </tr>

                    @endif
                @endforeach
                <tr>
                    <th style="color: #0000cc;">Loan Details</th>
                </tr>
                <tr>
                    <th style="padding-right: 8px; float: right; ">Loan Amount:
                    </th>
                    <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$loan->loan_amount}}</td>
                    <td width="200"></td>
                    <th style="padding-right: 8px; float: right; ">Loan Amount Words:
                    </th>
                    <td colspan="3"
                        style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$loan->loan_amount_words}}</td>

                </tr>

                <tr>
                    <th style="padding-right: 8px; float: right; ">Offer Letter Date:
                    </th>
                    <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">  {{$loan->offerletter_year}}
                        ÷{{$loan->offerletter_month}}
                        ÷{{$loan->offerletter_day}}</td>
                    <td width="200"></td>
                    <th style="padding-right: 8px; float: right; ">Branch:
                    </th>
                    <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\Branch::find($loan->branch_id)->location}}</td>
                </tr>
                <tr>
                    <th style="padding-right: 8px; float: right; ">Document Status:
                    </th>
                    <td><span class="label label-success">  {{$loan->document_status}}</span>
                    </td>
                    <td width="200"></td>
                    @if($loan->approved_by)
                    <th style="padding-right: 8px; float: right; ">Approved By:
                    </th>
                    <td><span class="label label-info">  {{App\User::find($loan->approved_by)->name}}
                            , <span style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:12px; ">{{App\Branch::find(App\User::find($loan->approved_by)->branch)->location}}</span> branch</span>
                    </td>
                        @endif
                </tr>


            </table>

            <h3 class="box-title">
                Please Tick the type of documents to be generated.
            </h3>

            {!! Form::open(['route' => ['joint.all_in_one',$bid],'method'=>'post','class'=>'form-inline']) !!}

            <div class="row">
                <div class="col-md-4">
                    <div class="box box-success collapsed-box box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">Basic Legal Documents</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                            class="fa fa-plus"></i>
                                </button>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table>
                                <tr>
                                    <td>
                                        @if($promissory_note=='1')
                                            <div class="form-group ">
                                                {!! form::checkbox('promissory_note','1',false,['class'=>'icheckbox_flat-green']) !!}
                                                {!! form::label('promissory_note','Promissory Note',['class'=>'control-label']) !!}


                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @if($loan_deed=='1')
                                            <div class="form-group ">
                                                {!! form::checkbox('loan_deed','1',false,['class'=>'icheckbox_flat-green']) !!}
                                                {!! form::label('loan_deed','Loan Deed',['class'=>'control-label']) !!}

                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @if($guarantor=='1')
                                            <div class="form-group">
                                                {!! form::checkbox('guarantor','1',false,['class'=>'icheckbox_flat-green']) !!}
                                                {!! form::label('guarantor','Guarantee Deed',['class'=>'control-label']) !!}

                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @if($manjurinama_of_hire_purchase=='1')
                                            <div class="form-group">
                                                {!! form::checkbox('manjurinama_of_hire_purchase','1',false,['class'=>'icheckbox_flat-green']) !!}
                                                {!! form::label('manjurinama_of_hire_purchase','Hire Purchase Manjurinama',['class'=>' control-label']) !!}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @if($mortgage_deed=='1')
                                            <div class="form-group">
                                                {!! form::checkbox('mortgage_deed','1',false,['class'=>'icheckbox_flat-green']) !!}
                                                {!! form::label('mortgage_deed','Mortgage Deed',['class'=>'control-label']) !!}


                                            </div>
                                        @endif

                                    </td>
                                </tr>
                                <tr>
                                    <td>                @if($share_pledge_deed=='1')
                                            <div class="form-group">
                                                {!! form::checkbox('share_pledge_deed','1',false,['class'=>'icheckbox_flat-green']) !!}
                                                {!! form::label('share_pledge_deed','Share Pledge Deed',['class'=>'control-label']) !!}


                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @if($hypo_of_stock=='1')
                                            <div class="form-group">
                                                {!! form::checkbox('hypo_of_stock','1',false,['class'=>'icheckbox_flat-green']) !!}
                                                {!! form::label('hypo_of_stock','Hypo of Stock',['class'=>'control-label']) !!}

                                            </div>
                                        @endif

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @if($anusuchi18=='1')
                                            <div class="form-group">
                                                {!! form::checkbox('anusuchi18','1',false,['class'=>'icheckbox_flat-green']) !!}
                                                {!! form::label('anusuchi18','Anusuchi 18',['class'=>' control-label']) !!}

                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @if($anusuchi19=='1')
                                            <div class="form-group">
                                                {!! form::checkbox('anusuchi19','1',false,['class'=>'icheckbox_flat-green']) !!}
                                                {!! form::label('anusuchi19','Anusuchi 19',['class'=>' control-label']) !!}


                                            </div>
                                        @endif
                                    </td>
                                </tr>

                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->


                <div class="col-md-4">
                    <div class="box box-success collapsed-box box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">Optional Legal Documents (Case Basis)
                            </h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                            class="fa fa-plus"></i>
                                </button>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table>
                                <tr>
                                    <td>
                                        @if($tieup_deed=='1')
                                            <div class="form-group">
                                                {!! form::checkbox('tieup_deed','1',false,['class'=>'icheckbox_flat-green']) !!}
                                                {!! form::label('tieup_deed','Tieup Deed',['class'=>' control-label']) !!}

                                            </div>
                                        @endif

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @if($consent_of_property_owner=='1')
                                            <div class="form-group">
                                                {!! form::checkbox('consent_of_property_owner','1',false,['class'=>'icheckbox_flat-green']) !!}
                                                {!! form::label('consent_of_property_owner','Consent of Property Owner',['class'=>'control-label']) !!}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @if($swap_commitment_letter=='1')
                                            <div class="form-group">
                                                {!! form::checkbox('swap_commitment_letter','1',false,['class'=>'icheckbox_flat-green']) !!}
                                                {!! form::label('swap_commitment_letter','SWAP Commitment Letter',['class'=>' control-label']) !!}

                                            </div>
                                        @endif

                                    </td>
                                </tr>

                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->

                <div class="col-md-4">
                    <div class="box box-success collapsed-box box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">Correspondance letters</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                            class="fa fa-plus"></i>
                                </button>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table>
                                <tr>
                                    <td>
                                        @if($vehicle_registration_letter=='1')
                                            <div class="form-group">
                                                {!! form::checkbox('vehicle_registration_letter','1',false,['class'=>'icheckbox_flat-green']) !!}
                                                {!! form::label('vehicle_registration_letter','Vehicle Registration Letter',['class'=>'control-label']) !!}

                                            </div>
                                        @endif

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @if($vehicle_fukka_letter=='1')
                                            <div class="form-group">
                                                {!! form::checkbox('vehicle_fukka_letter','1',false,['class'=>'icheckbox_flat-green']) !!}
                                                {!! form::label('vehicle_fukka_letter','Vehicle Release Letter',['class'=>'control-label']) !!}
                                            </div>
                                        @endif

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @if($rokka_letter_malpot=='1')
                                            <div class="form-group">
                                                {!! form::checkbox('rokka_letter_malpot','1',false,['class'=>'icheckbox_flat-green']) !!}
                                                {!! form::label('rokka_letter_malpot','Land Lein Mark Letter',['class'=>' control-label']) !!}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @if($release_letter_malpot=='1')
                                            <div class="form-group">
                                                {!! form::checkbox('release_letter_malpot','1',false,['class'=>'icheckbox_flat-green']) !!}
                                                {!! form::label('release_letter_malpot','Land Release Letter',['class'=>' control-label']) !!}

                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @if($share_rokka_letter=='1')
                                            <div class="form-group ">
                                                {!! form::checkbox('share_rokka_letter','1',false,['class'=>'icheckbox_flat-green']) !!}
                                                {!! form::label('share_rokka_letter','Share Lein Mark Letter',['class'=>' control-label']) !!}
                                            </div>
                                        @endif

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @if($share_release_letter=='1')
                                            <div class="form-group ">
                                                {!! form::checkbox('share_release_letter','1',false,['class'=>'icheckbox_flat-green']) !!}
                                                {!! form::label('share_release_letter','Share Release Letter',['class'=>' control-label']) !!}


                                            </div>
                                        @endif

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @if($bonus_right_cash_divident=='1')
                                            <div class="form-group ">
                                                {!! form::checkbox('bonus_right_cash_divident','1',false,['class'=>'icheckbox_flat-green']) !!}
                                                {!! form::label('bonus_right_cash_divident','Bonus Right Cash Divident',['class'=>' control-label']) !!}
                                            </div>
                                        @endif

                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>




                <div class="box-footer col-md-10">
                    {!! Form::submit('Generate Document',['class'=>'btn btn-primary pull-right']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>


    </div>
    <!-- /.box-body -->
@endsection
