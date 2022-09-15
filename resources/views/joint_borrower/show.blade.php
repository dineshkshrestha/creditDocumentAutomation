@extends ('layouts.backend')
@section('title',' Joint Borrower View ')
@section('heading','Joint Borrower')
@section('small_heading','View')
@section('breadcrumb')
    <li>        <a href="{{route('home')}}">Dashboard</a></li>
    <li>        <a href="{{route('joint_borrower.index')}}">View All Borrowers</a></li>
    <li class="active">        <strong>View Borrower</strong></li>
@endsection
@section('content')

    <div class="box box-default">
        <div class="box-header with-border">

        </div>
        <!-- /.box-header -->

        <div class="row">
            <div class="col-md-12">

                <!-- /.box-header -->
                <div class="box-body">
                    <div class="box-header with-border">
                        <h5 class="box-title">
                            Borrower Details
                        </h5>
                    </div>
                    <div class="box-body">
                        <a class="btn btn-warning btn-xs pull-right"
                           href={{route('joint_borrower.edit',[$borrower->id])}}><i
                                    class="fa fa-edit"></i>Edit</a>
                        <table>
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

                            </tr>
                            <tr>
                                <th style=" padding-right: 8px; float: right; ">Phone:</th>
                                <td>{{$borrower->phone}}</td>

                            </tr>  <tr>
                                <th style=" padding-right: 8px; float: right; ">Borrower Type:</th>
                                <td>Single</td>
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


                        </table>


                    </div>
                </div>
            </div>



        </div>
    </div>



@endsection