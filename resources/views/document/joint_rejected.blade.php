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
                                href={{route('joint_review.index',$bid)}}><i
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
                @php
                    $i=1;
                @endphp
                <table>
                    @foreach ($borrowers as $borrower)

                        @if($borrower)

                        <tr>
                            <th style="color:orangered">Joint Borrower- {{$i++}}</th>
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
                            <th style="padding-right: 8px; float: right; ">GrandFather:
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
                            <th style=" padding-right: 8px; float: right; ">Date of Birth:</th>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">
                                {{$borrower->dob_year}}÷{{$borrower->dob_month}}
                                ÷{{$borrower->dob_day}}</td>
                            <td width="200"></td>
                            <th style="padding-right: 8px; float: right; ">Phone:</th>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">
                                {{$borrower->phone}}
                            </td>

                        </tr>
@endif
                    @endforeach
                </table>


                <div class="box-footer">


                </div>


            </div>
        </div>
    </div>
@endsection
