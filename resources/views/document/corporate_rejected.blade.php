@extends ('layouts.backend')
@section('title','Rejected')
@section('heading','Rejected Documents')
{{--@section('small_heading','')--}}
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li class="active"><strong>View Rejection Reason</strong></li>
@endsection

@section('content')
    <div class="col-sm-10">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <span class="" style="color: #0a0a0a"><i class="fa fa-info-circle fa-fw"></i>  <a
                                class="btn btn-warning btn-xs pull-right"
                                href={{route('corporate_review.index',$bid)}}><i
                                    class="fa fa-edit"></i>Edit details</a>
                    Here are the list of Rejected Documents, Please edit the document as per the remarks
           .
            </span>
                </h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="form-group">


                    <b>
                        Rejection Reason:</b>
                    <span style="color: red;">
                {!! html_entity_decode( $loan->document_remarks)!!}

</span>
                </div>
                <hr>
                <h4 style="color: #2aabd2">Borrower Information</h4>


                <table>
                    <tr>
                        <th style="padding-right: 8px; float: right; ">Name:</th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;">{{$borrower->nepali_name}}</td>
                        <td width="200"></td>
                        <th style="padding-right: 8px; float: right; ">English
                            Name:
                        </th>
                        <td>{{$borrower->english_name}}</td>
                    </tr>
                    <tr>
                        <th style="padding-right: 8px; float: right; ">Client ID:
                        </th>
                        <td>{{$borrower->client_id}}</td>
                        <td width="200"></td>

                        <th style="padding-right: 8px; float: right; ">Registration
                            Number
                        </th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$borrower->registration_number}}
                        </td>
                    </tr>
                    <tr>
                        <th style="padding-right: 8px; float: right; ">Registration
                            Date
                        </th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;">{{$borrower->reg_year}}
                            ÷{{$borrower->reg_month}}
                            ÷{{$borrower->reg_day}}</td>
                        <td width="200"></td>
                        <th style="padding-right: 8px; float: right; ">Ministry</th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\Ministry::find($borrower->ministry_id)->name}}</td>
                    </tr>
                    <tr>
                        <th style="padding-right: 8px; float: right; ">Phone:</th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">
                            {{$borrower->phone}}
                        </td>
                        <td width="200"></td>
                        <th style="padding-right: 8px; float: right; ">Department
                        </th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\Department::find($borrower->department_id)->name}}</td>
                    </tr>
                    <tr>
                        <th style="padding-right: 8px; float: right; ">District:
                        </th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\District::find($borrower->district_id)->name}}</td>
                        <td width="200"></td>
                        <th style="padding-right: 8px; float: right; ">Local
                            Government:
                        </th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\LocalBodies::find($borrower->local_bodies_id)->name}}</td>
                    </tr>
                    <tr>
                        <th style="padding-right: 8px; float: right; ">Ward No.:
                        </th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$borrower->wardno}}</td>
                        <td width="200"></td>
                        <th style="padding-right: 8px; float: right; ">Status:</th>
                        <td>
                            @if($borrower->status==1)
                                <label class="label label-success label-xs">Active</label>

                            @else
                                <label class="label label-danger label-xs">Inactive</label>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th style=" padding-right: 8px; float: right; ">Created
                            By:
                        </th>
                        <td>{{App\User::find($borrower->created_by)->name}}</td>
                        <td width="200"></td>
                        <th style="padding-right: 8px; float: right; ">Updated By:
                        </th>

                        <td>
                            @if(isset($borrower->updated_by)) {{App\User::find($borrower->updated_by)->name}}
                            @else
                                Wasn't Updated Yet !!
                            @endif

                        </td>
                    </tr>


                </table>

                <table>
                    @php
                        $b=\App\AuthorizedPerson::find($borrower->authorized_person_id);
                    @endphp
                    <tr>
                        <Th colspan="2" style="color: orangered"><b><u>Authorized Person Details</u></b></Th>
                    </tr>
                    <tr>
                        <th style="padding-right: 8px; float: right; ">Name:</th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;">{{$b->nepali_name}}</td>
                        <td width="200"></td>
                        <th style="padding-right: 8px; float: right; ">English Name:</th>
                        <td>{{$b->english_name}}</td>
                    </tr>
                    <tr>
                        <th style="padding-right: 8px; float: right; ">Post:</th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;">{{$b->post}}</td>
                        <td width="200"></td>
                        <th style="padding-right: 8px; float: right; ">Gender:</th>
                        <td>
                            @if($b->gender=='1')
                                Male
                            @endif
                            @if($b->gender=='0')
                                Female
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th style="padding-right: 8px; float: right; ">GrandFather:
                        </th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$b->grandfather_name}}</td>
                        <td width="200"></td>
                        <th style="padding-right: 8px; float: right; ">Relation</th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$b->grandfather_relation}}</td>
                    </tr>
                    <tr>
                        <th style="padding-right: 8px; float: right; ">Father:</th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$b->father_name}}</td>
                        <td width="200"></td>
                        <th style="padding-right: 8px; float: right; ">Relation</th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$b->father_relation}}</td>
                    </tr>
                    <tr>
                        <th style="padding-right: 8px; float: right; ">Spouse:</th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$b->spouse_name}}</td>
                        <td width="200"></td>
                        <th style="padding-right: 8px; float: right; ">Relation</th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$b->spouse_relation}}</td>
                    </tr>
                    <tr>
                        <th style="padding-right: 8px; float: right; ">District:</th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\District::find($b->district_id)->name}}
                        </td>
                        <td width="200"></td>
                        <th style="padding-right: 8px; float: right; ">Local Government:</th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\LocalBodies::find($b->local_bodies_id)->name}}</td>
                    </tr>
                    <tr>
                        <th style="padding-right: 8px; float: right; ">Ward No.:</th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$b->wardno}}</td>
                        <td width="200"></td>
                        <th style="padding-right: 8px; float: right; ">Citizenship No.:</th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$b->citizenship_number}}</td>
                    </tr>
                    <tr>
                        <th style="padding-right: 8px; float: right; ">Issued District:</th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\District::find($b->issued_district)->name}}</td>
                        <td width="200"></td>
                        <th style="padding-right: 8px; float: right; ">Issued Date</th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">

                            {{$b->issued_year}}÷{{$b->issued_month}}
                            ÷{{$b->issued_day}}</td>
                    </tr>
                    <tr>
                        <th style=" padding-right: 8px; float: right; ">Date of Birth:</th>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">
                            {{$b->dob_year}}÷{{$b->dob_month}}
                            ÷{{$b->dob_day}}</td>
                        <td width="200"></td>

                    </tr>


                </table>


                <div class="box-footer">


                </div>


            </div>
        </div>
    </div>
@endsection
