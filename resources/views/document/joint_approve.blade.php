@extends ('layouts.backend')
@section('title','Approve Document')
@section('heading','Review and Approve Details')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li class="active"><strong>Approve Document</strong></li>
@endsection
@section('css')
    <style>
        .subform {
            color: #0000cc;
            font-family: 'Inconsolata', 'Fira Mono';
            font-size: 16px;
        }

        .red_number {
            color: red;
            font-family: "Arial Black";
            font-size: 13px;
        }
    </style>
@endsection
@section('content')
    {!! Form::open(['route'=>['joint.reject',$bid],'method'=>'post'],['class'=>'form-horizontal']) !!}
    @include('modal.reject_modal')
    <div class="box box-default">
        <div class="box-header with-border">
            <h4 class="box-title">
              </h4>
            <span>
      <table class="pull-right">
        <tr>
         <td>
             <a data-toggle="modal" data-target="#reject" href="#"
                class="btn btn-danger btn-xm pull-right">
                           <i class="fa fa-times-circle-o" style="font-size:20px;"></i><b>REJECT</b></a>
        </td>
            <td width="10">
        </td>
            <td>
             <a class="btn btn-success btn-xm pull-right" href="{{route('joint.approve_select',$bid)}}">
                           <i class="fa fa-check-circle-o" style="font-size:20px;"></i><b>APPROVE</b></a>
             </td> </tr></table>
               </span>
            @if($errors->has('reject'))
                <span class="label label-danger">{{$errors->first('reject')}} </span>
            @endif
        </div>
        <!-- /.box-header -->

        <div class="row">
            <div class="col-md-12">
            @php
                $isn=1;
            @endphp
            <!-- /.box-header -->
                <div class="box-body">
                    <div class="box-group" id="accordion">
                        <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                        <div class="panel box box-warning">
                            <div class="box-header with-border">
                                <h5 class="box-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#borrower">
                                        Joint Borrower Details
                                        <i class="glyphicon glyphicon-menu-down"></i></a>
                                </h5>
                            </div>
                            <div id="borrower" class="panel-collapse collapse in">
                                <div class="box-body">

                                    @foreach ($borrowers as $b)
                                        @if($b)
                                            <b class="red_number">Joint
                                                Borrower-
                                                {{$isn++}}
                                            </b>

                                            <table>

                                                <tr>
                                                    <th style="padding-right: 8px; float: right; ">Name:</th>
                                                    <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;">{{$b->nepali_name}}</td>
                                                    <td width="200"></td>
                                                    <th style="padding-right: 8px; float: right; ">English Name:</th>
                                                    <td>{{$b->english_name}}</td>
                                                </tr>
                                                <tr>
                                                    <th style="padding-right: 8px; float: right; ">Client ID:</th>
                                                    <td>{{$b->client_id}}</td>
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

                                                        {{$b->issued_year}}??{{$b->issued_month}}
                                                        ??{{$b->issued_day}}</td>
                                                </tr>
                                                <tr>
                                                    <th style=" padding-right: 8px; float: right; ">Date of Birth:</th>
                                                    <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">
                                                        {{$b->dob_year}}??{{$b->dob_month}}
                                                        ??{{$b->dob_day}}</td>
                                                    <td width="200"></td>
                                                    <th style="padding-right: 8px; float: right; ">Age:</th>
                                                    <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">
                                                        @php
                                                            $dob_year=$b->dob_year;

                                                           echo $age= $cyear-$dob_year;

                                                        @endphp jif{
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="padding-right: 8px; float: right; ">Phone:</th>
                                                    <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">
                                                        {{$b->phone}}
                                                    </td>

                                                    <td width="200"></td>
                                                    <th style="padding-right: 8px; float: right; ">Status:</th>
                                                    <td>
                                                        @if($b->status==1)
                                                            <label class="label label-success label-xs">Active</label>

                                                        @else
                                                            <label class="label label-danger label-xs">Inactive</label>
                                                        @endif
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <th style=" padding-right: 8px; float: right; ">Created By:</th>
                                                    <td>{{App\User::find($b->created_by)->name}}</td>
                                                    <td width="200"></td>
                                                    <th style="padding-right: 8px; float: right; ">Updated By:</th>

                                                    <td>
                                                        @if(isset($b->updated_by)) {{App\User::find($b->updated_by)->name}}
                                                        @else
                                                            Wasn't Updated Yet !!
                                                        @endif

                                                    </td>
                                                </tr>


                                            </table>
                                        @endif
                                    @endforeach

                                </div>
                            </div>
                        </div>


                        <div class="panel box box-success">
                            <div class="box-header with-border">
                                <h5 class="box-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#guarantor">
                                        List of all Guarantors<i class="glyphicon glyphicon-menu-down"></i>
                                    </a>
                                </h5>
                            </div>
                            <div id="guarantor" class="panel-collapse collapse">
                                <div class="box-body">
                                    {{--checks whether there is personal guarantor --}}
                                    @if($personal_guarantor)
                                        <div class="box-header with-border">
                                            <h6 class="box-title">
                                                <a data-toggle="collapse" class="subform" data-parent=""
                                                   href="#personal_guarantor">
                                                    Personal Guarantor Details
                                                    <i class="fa fa-chevron-down"></i></a>
                                            </h6>
                                        </div>
                                        <div id="personal_guarantor" class="panel-collapse collapse">
                                            <div class="box-body">
                                                @php
                                                    $a=1;
                                                @endphp


                                                @foreach($personal_guarantor as $b)
                                                    <b class="red_number">Personal Guarantor- {{$a++}}
                                                    </b>


                                                    <table>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">Name:</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;">{{$b->nepali_name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">English
                                                                Name:
                                                            </th>
                                                            <td>{{$b->english_name}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">Client ID:
                                                            </th>
                                                            <td>{{$b->client_id}}</td>
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
                                                            <th style="padding-right: 8px; float: right; ">
                                                                GrandFather:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$b->grandfather_name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Relation</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$b->grandfather_relation}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">
                                                                Father:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$b->father_name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Relation</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$b->father_relation}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">
                                                                Spouse Name:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$b->spouse_name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Relation</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$b->spouse_relation}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">District:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\District::find($b->district_id)->name}}
                                                            </td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Local
                                                                Government:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\LocalBodies::find($b->local_bodies_id)->name}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">Ward No.:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$b->wardno}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Citizenship
                                                                No.:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$b->citizenship_number}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">Issued
                                                                District:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\District::find($b->issued_district)->name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Issued Date
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">

                                                                {{$b->issued_year}}??{{$b->issued_month}}
                                                                ??{{$b->issued_day}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style=" padding-right: 8px; float: right; ">Date of
                                                                Birth:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">
                                                                {{$b->dob_year}}??{{$b->dob_month}}
                                                                ??{{$b->dob_day}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Age:</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">
                                                                @php
                                                                    $dob_year=$b->dob_year;

                                                                   echo $age= $cyear-$dob_year;

                                                                @endphp jif{
                                                            </td>
                                                        </tr>
                                                        <tr>

                                                            <th style="padding-right: 8px; float: right; ">Status:</th>
                                                            <td>
                                                                @if($b->status==1)
                                                                    <label class="label label-success label-xs">Active</label>

                                                                @else
                                                                    <label class="label label-danger label-xs">Inactive</label>
                                                                @endif
                                                            </td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Phone:</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">
                                                                {{$b->phone}}
                                                            </td>

                                                        </tr>
                                                        <tr>
                                                            <th style=" padding-right: 8px; float: right; ">Created
                                                                By:
                                                            </th>
                                                            <td>{{App\User::find($b->created_by)->name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Updated By:
                                                            </th>

                                                            <td>
                                                                @if(isset($b->updated_by)) {{App\User::find($b->updated_by)->name}}
                                                                @else
                                                                    Wasn't Updated Yet !!
                                                                @endif

                                                            </td>
                                                        </tr>


                                                    </table>
                                                    <hr>
                                                @endforeach

                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>


                        <div class="panel box box-danger">
                            <div class="box-header with-border">
                                <h5 class="box-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#property">
                                        List of all the Properties and Property Owner<i
                                                class="glyphicon glyphicon-menu-down"></i>
                                    </a>
                                </h5>
                            </div>
                            <div id="property" class="panel-collapse collapse">
                                <div class="box-body">
                                    @if($personal_land_owner)
                                        <div class="box-header with-border">
                                            <h6 class="box-title">
                                                <a data-toggle="collapse" class="subform" data-parent=""
                                                   href="#personal_land_owner">
                                                    Personal Land Owner
                                                    <i class="fa fa-chevron-down"></i></a>
                                            </h6>
                                        </div>
                                        <div id="personal_land_owner" class="panel-collapse collapse">
                                            <div class="box-body">
                                                @php
                                                    $i=1;
                                                @endphp
                                                @foreach($personal_land_owner as $plo)
                                                    <b class="red_number">Personal Land owner- {{$i++}}
                                                    </b>

                                                    <table>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">Name:</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;">{{$plo->nepali_name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">English
                                                                Name:
                                                            </th>
                                                            <td>{{$plo->english_name}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">Client ID:
                                                            </th>
                                                            <td>{{$plo->client_id}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Gender:</th>
                                                            <td>
                                                                @if($plo->gender=='1')
                                                                    Male
                                                                @endif
                                                                @if($plo->gender=='0')
                                                                    Female
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">
                                                                GrandFather:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->grandfather_name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Relation</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->grandfather_relation}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">
                                                                Father:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->father_name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Relation</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->father_relation}}</td>
                                                        </tr>
                                                       <tr>
                                                            <th style="padding-right: 8px; float: right; ">
                                                                Spouse Name:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->spouse_name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Relation</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->spouse_relation}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">District:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\District::find($plo->district_id)->name}}
                                                            </td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Local
                                                                Government:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\LocalBodies::find($plo->local_bodies_id)->name}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">Ward No.:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->wardno}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Citizenship
                                                                No.:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->citizenship_number}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">Issued
                                                                District:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\District::find($plo->issued_district)->name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Issued Date
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">

                                                                {{$plo->issued_year}}??{{$plo->issued_month}}
                                                                ??{{$plo->issued_day}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style=" padding-right: 8px; float: right; ">Date of
                                                                Birth:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">
                                                                {{$plo->dob_year}}??{{$plo->dob_month}}
                                                                ??{{$plo->dob_day}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Age:</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">
                                                                @php
                                                                    $dob_year=$plo->dob_year;

                                                                   echo $age= $cyear-$dob_year;

                                                                @endphp jif{
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">Status:</th>
                                                            <td>
                                                                @if($plo->status==1)
                                                                    <label class="label label-success label-xs">Active</label>

                                                                @else
                                                                    <label class="label label-danger label-xs">Inactive</label>
                                                                @endif
                                                            </td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Phone:</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">
                                                                {{$plo->phone}}
                                                            </td>

                                                        </tr>
                                                        <tr>
                                                            <th style=" padding-right: 8px; float: right; ">Created
                                                                By:
                                                            </th>
                                                            <td>{{App\User::find($plo->created_by)->name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Updated By:
                                                            </th>

                                                            <td>
                                                                @if(isset($plo->updated_by)) {{App\User::find($plo->updated_by)->name}}
                                                                @else
                                                                    Wasn't Updated Yet !!
                                                                @endif

                                                            </td>
                                                        </tr>


                                                    </table>
                                                    <table
                                                            class=" table table-striped table-bordered table-hover dataTables-example">
                                                        <thead>
                                                        <tr>
                                                            <th colspan="8">Assigned Property of this land owner</th>
                                                        </tr>
                                                        <tr>
                                                            <th>SN</th>
                                                            <th>District</th>
                                                            <th>Local Body</th>
                                                            <th>Malpot</th>
															<th>Ward No</th>
                                                            <th>Sheet No</th>
                                                            <th>Kitta No</th>
                                                            <th>Area</th>
                                                            <th>Status</th>

                                                        </tr>

                                                        </thead>
                                                        <tbody>
                                                        @php
                                                            $sn=1;
                                                        @endphp
                                                        @foreach($borrower->jointborrowerpersonalland as $land)
                                                            @if($land->property_owner_id==$plo->id)
                                                                <tr>
                                                                    <td>{{$sn++}}</td>
                                                                    <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{App\District::find($land->district_id)->name}}</td>
                                                                    <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{App\LocalBodies::find($land->local_bodies_id)->name}}</td>
 <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$land->malpot}}</td>
  <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$land->wardno}}</td>
                                                                                                                                      
																   <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$land->sheet_no}}</td>
                                                                    <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$land->kitta_no}}</td>
                                                                    <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$land->area}}</td>
                                                                    <td> @if($land->status==1)
                                                                            <label class="btn btn-success btn-xs">Active</label>
                                                                        @else
                                                                            <label class="btn btn-danger btn-xs">Inactive</label>
                                                                        @endif
                                                                    </td>

                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        </tbody>

                                                    </table>
                                                    <hr>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    @if($personal_share_owner)
                                        <div class="box-header with-border">
                                            <h6 class="box-title">
                                                <a data-toggle="collapse" class="subform" data-parent=""
                                                   href="#personal_share_owner">
                                                    Personal Share Owner
                                                    <i class="fa fa-chevron-down"></i></a>
                                            </h6>
                                        </div>
                                        <div id="personal_share_owner" class="panel-collapse collapse">
                                            <div class="box-body">
                                                @php
                                                    $sharesn=1;
                                                @endphp
                                                @foreach($personal_share_owner as $plo)
                                                    <b class="red_number">Personal Share owner- {{$sharesn++}}
                                                    </b>

                                                    <table>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">Name:</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;">{{$plo->nepali_name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">English
                                                                Name:
                                                            </th>
                                                            <td>{{$plo->english_name}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">Client ID:
                                                            </th>
                                                            <td>{{$plo->client_id}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Gender:</th>
                                                            <td>
                                                                @if($plo->gender=='1')
                                                                    Male
                                                                @endif
                                                                @if($plo->gender=='0')
                                                                    Female
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">
                                                                GrandFather:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->grandfather_name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Relation</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->grandfather_relation}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">
                                                                Father:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->father_name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Relation</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->father_relation}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">
                                                                Spouse Name:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->spouse_name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Relation</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->spouse_relation}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">District:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\District::find($plo->district_id)->name}}
                                                            </td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Local
                                                                Government:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\LocalBodies::find($plo->local_bodies_id)->name}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">Ward No.:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->wardno}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Citizenship
                                                                No.:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->citizenship_number}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">Issued
                                                                District:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\District::find($plo->issued_district)->name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Issued Date
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">

                                                                {{$plo->issued_year}}??{{$plo->issued_month}}
                                                                ??{{$plo->issued_day}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style=" padding-right: 8px; float: right; ">Date of
                                                                Birth:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">
                                                                {{$plo->dob_year}}??{{$plo->dob_month}}
                                                                ??{{$plo->dob_day}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Age:</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">
                                                                @php
                                                                    $dob_year=$plo->dob_year;

                                                                   echo $age= $cyear-$dob_year;

                                                                @endphp jif{
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">Status:</th>
                                                            <td>
                                                                @if($plo->status==1)
                                                                    <label class="label label-success label-xs">Active</label>

                                                                @else
                                                                    <label class="label label-danger label-xs">Inactive</label>
                                                                @endif
                                                            </td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Phone:</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">
                                                                {{$plo->phone}}
                                                            </td>

                                                        </tr>
                                                        <tr>
                                                            <th style=" padding-right: 8px; float: right; ">Created
                                                                By:
                                                            </th>
                                                            <td>{{App\User::find($plo->created_by)->name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Updated By:
                                                            </th>

                                                        </tr>


                                                    </table>
                                                    <table
                                                            class=" table table-striped table-bordered table-hover dataTables-example">
                                                        <thead>
                                                        <tr>
                                                            <th colspan="7">Available Share properties</th>
                                                        </tr>
                                                        <tr>
                                                            <th>SN</th>
                                                            <th>Client ID</th>
                                                            <th>DPID</th>
                                                            <th>Kitta No</th>
                                                            <th>ISIN</th>
                                                            <th>Status</th>

                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @php
                                                            $sn=1;
                                                        @endphp
                                                        @foreach($borrower->jointborrowerpersonalshare as $share)


                                                            @if($share->property_owner_id==$plo->id)
                                                                <tr>

                                                                    <td>{{$sn++}}</td>
                                                                    <td>{{$share->client_id}}</td>
                                                                    <td>{{$share->dpid}}</td>
                                                                    <td>{{$share->kitta}}</td>
                                                                    <td>{{App\RegisteredCompany::find($share->isin)->isin   }}</td>
                                                                    <td>
                                                                        @if($share->status==1)
                                                                            <label class="btn btn-success btn-xs">Active</label>

                                                                        @else
                                                                            <label class="btn btn-danger btn-xs">Inactive</label>
                                                                        @endif
                                                                    </td>

                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        </tbody>

                                                    </table>
                                                    <hr>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if($joint_property_owner)
                                        <div class="box-header with-border">
                                            <h6 class="box-title">
                                                <a data-toggle="collapse" class="subform" data-parent=""
                                                   href="#joint_land_owner">
                                                    Joint Land Owner
                                                    <i class="fa fa-chevron-down"></i></a>
                                            </h6>
                                            <span>                                                        <a href=""
                                                                                                             class="btn btn-danger btn-xs pull-right">
                                                            <form action="{{route('joint_review.joint_property_owner_destroy',[$bid])}}"
                                                                  method="post">
                                                                {{csrf_field()}}
                                                                <input type="hidden" name="_method" value="delete">
                                                                <button type="submit" class="btn btn-danger btn-xs"
                                                                        style="padding: 0; outline:none;border: none;background: none;"
                                                                        onclick="return confirm('Are you sure to delete?')">
                                                                    <i
                                                                            class="fa fa-trash fa-fw"></i>Delete
                                                                </button>
                                                            </form>
                                                        </a>
</span>
                                        </div>
                                        <div id="joint_land_owner" class="panel-collapse collapse">
                                            <div class="box-body">
                                                @php
                                                    $i=1;
                                                @endphp
                                                @foreach($joint_property_owner as $plo)
                                                @if($plo)
                                                    <b class="red_number">Joint Owner- {{$i++}}
                                                    </b>


                                                    <table>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">Name:</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;">{{$plo->nepali_name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">English
                                                                Name:
                                                            </th>
                                                            <td>{{$plo->english_name}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">Client ID:
                                                            </th>
                                                            <td>{{$plo->client_id}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Gender:</th>
                                                            <td>
                                                                @if($plo->gender=='1')
                                                                    Male
                                                                @endif
                                                                @if($plo->gender=='0')
                                                                    Female
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">
                                                                GrandFather:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->grandfather_name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Relation</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->grandfather_relation}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">
                                                                Father:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->father_name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Relation</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->father_relation}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">District:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\District::find($plo->district_id)->name}}
                                                            </td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Local
                                                                Government:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\LocalBodies::find($plo->local_bodies_id)->name}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">Ward No.:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->wardno}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Citizenship
                                                                No.:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$plo->citizenship_number}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">Issued
                                                                District:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\District::find($plo->issued_district)->name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Issued Date
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">

                                                                {{$plo->issued_year}}??{{$plo->issued_month}}
                                                                ??{{$plo->issued_day}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style=" padding-right: 8px; float: right; ">Date of
                                                                Birth:
                                                            </th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">
                                                                {{$plo->dob_year}}??{{$plo->dob_month}}
                                                                ??{{$plo->dob_day}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Age:</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">
                                                                @php
                                                                    $dob_year=$plo->dob_year;

                                                                   echo $age= $cyear-$dob_year;

                                                                @endphp jif{
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th style="padding-right: 8px; float: right; ">Status:</th>
                                                            <td>
                                                                @if($plo->status==1)
                                                                    <label class="label label-success label-xs">Active</label>

                                                                @else
                                                                    <label class="label label-danger label-xs">Inactive</label>
                                                                @endif
                                                            </td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Phone:</th>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">
                                                                {{$plo->phone}}
                                                            </td>

                                                        </tr>
                                                        <tr>
                                                            <th style=" padding-right: 8px; float: right; ">Created
                                                                By:
                                                            </th>
                                                            <td>{{App\User::find($plo->created_by)->name}}</td>
                                                            <td width="200"></td>
                                                            <th style="padding-right: 8px; float: right; ">Updated By:
                                                            </th>

                                                            <td>
                                                                @if(isset($plo->updated_by)) {{App\User::find($plo->updated_by)->name}}
                                                                @else
                                                                    Wasn't Updated Yet !!
                                                                @endif

                                                            </td>
                                                        </tr>


                                                    </table>
                                                @endif
                                                        @endforeach

                                                <table
                                                        class=" table table-striped table-bordered table-hover dataTables-example">
                                                    <thead>
                                                    <tr>
                                                        <th colspan="8">Assigned Property of this land owner</th>
                                                    </tr>
                                                    <tr>
                                                        <th>SN</th>
                                                        <th>District</th>
                                                        <th>Local Body</th>
														<th>Ward No</th>
                                                        <th>Sheet No</th>
                                                        <th>Kitta No</th>
                                                        <th>Area</th>
                                                        <th>Status</th>

                                                    </tr>

                                                    </thead>
                                                    <tbody>
                                                    @php
                                                        $sn=1;
                                                    @endphp
                                                    @foreach($jointp as $land)
                                                        <tr>
                                                            <td>{{$sn++}}</td>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{App\District::find($land->district_id)->name}}</td>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{App\LocalBodies::find($land->local_bodies_id)->name}}</td>
 <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$land->wardno}}</td>
                                                                                                                              
														   <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$land->sheet_no}}</td>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$land->kitta_no}}</td>
                                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$land->area}}</td>
                                                            <td> @if($land->status==1)
                                                                    <label class="btn btn-success btn-xs">Active</label>
                                                                @else
                                                                    <label class="btn btn-danger btn-xs">Inactive</label>
                                                                @endif
                                                            </td>

                                                        </tr>
                                                    @endforeach
                                                    </tbody>

                                                </table>
                                                <hr>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>


                        <div class="panel box box-success">
                            <div class="box-header with-border">
                                <h5 class="box-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#hirepurchase">
                                        Hire Purchase Details<i class="glyphicon glyphicon-menu-down"></i>
                                    </a>
                                </h5>
                            </div>
                            <div id="hirepurchase" class="panel-collapse collapse">
                                <div class="box-body">

                                    @php($i=1)
                                    <table class="table table-striped table-bordered table-hover dataTables-example">

                                        <tr>
                                            <th>S.N</th>
                                            <th>Model No.</th>
                                            <th>Engine No.</th>
                                            <th>Registration No.</th>
                                            <th>Chassis No.</th>

                                        </tr>
                                        @foreach($hirepurchase as $h)
                                            <tr>
                                                <td>{{$i++}}</td>
                                                <td>{{$h->model_number}}</td>
                                                <td>{{$h->engine_number}}</td>
                                                <td style='font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;'>{{$h->registration_number}}</td>
                                                <td>{{$h->chassis_number}}</td>


                                            </tr>
                                        @endforeach


                                    </table>


                                </div>
                            </div>
                        </div>
                        <div class="panel box box-success">
                            <div class="box-header with-border">
                                <h5 class="box-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#facilities">
                                        List of all Facilities<i class="glyphicon glyphicon-menu-down"></i>
                                    </a>
                                </h5>
                            </div>
                            <div id="facilities" class="panel-collapse collapse">
                                <div class="box-body">

                                    @php($i=1)
                                    <table class="table table-striped table-bordered table-hover dataTables-example">

                                        <tr>
                                            <th>S.N</th>
                                            <th>Facility</th>
                                            <th>Amount</th>
                                            <th>Rate</th>
                                            <th>Remarks</th>
                                            <th>Valid Till/ Tenure</th>
                                            <th>status</th>

                                        </tr>
                                        @foreach($facilities as $f)
                                            <tr>
                                                <td>{{$i++}}</td>
                                                <td style='font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;'>{{App\Facility::find($f->facility_id)->name}}</td>
                                                <td>{{$f->amount}}
                                                 @if($f->within==1)
                                                    <label class="label label-primary">
                                                    Within
                                                    </label>
                                                    @endif</td>
                                                <td style='font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;'>{{$f->rate}}</td>
                                                <td style='font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;'>{{$f->remarks}}</td>
                                                <td>
                                                    @if($f->tyear && $f->tmonth && $f->tday)
                                                        {{$f->tyear}}/{{$f->tmonth}}/{{$f->tday}}
                                                    @elseif($f->tenure)
                                                        <span style='font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;'>
                                {{$f->tenure}}</span>
                                                    @else
                                                        <span class="label label-danger label-xm">Error! Not Assigned, Please Edit this facility.</span>
                                                    @endif


                                                </td>
                                                <td>
                                                    @if($f->status==1)
                                                        <label class="label label-success label-xs">Active</label>

                                                    @else
                                                        <label class="label label-danger label-xs">Inactive</label>
                                                    @endif
                                                </td>

                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="panel box box-danger">
                            <div class="box-header with-border">
                                <h5 class="box-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#loan">
                                        Loan Details<i class="glyphicon glyphicon-menu-down"></i>
                                    </a>
                                </h5>
                            </div>
                            <div id="loan" class="panel-collapse collapse">
                                <div class="box-body">

                                    <table>
                                        <tr>
                                            <th style="padding-right: 8px; float: right; ">Loan Amount:
                                            </th>
                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$loan->loan_amount}}</td>
                                            <td width="200"></td>
                                        </tr>
                                        <tr>
                                            <th style="padding-right: 8px; float: right; ">Loan Amount Words:
                                            </th>
                                            <td colspan="3"
                                                style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{$loan->loan_amount_words}}</td>
                                        </tr>
                                        <tr>
                                            <th style="padding-right: 8px; float: right; ">Offer Letter Date:
                                            </th>
                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">  {{$loan->offerletter_year}}
                                                ??{{$loan->offerletter_month}}
                                                ??{{$loan->offerletter_day}}</td>
                                            <td width="200"></td>
                                            <th style="padding-right: 8px; float: right; ">Branch:
                                            </th>
                                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{App\Branch::find($loan->branch_id)->location}}</td>
                                        </tr>
                                        <tr>
                                            <th style="padding-right: 8px; float: right; ">Document Status:
                                            </th>
                                            <td><span class="label label-danger">  {{$loan->document_status}}</span>
                                            </td>
                                            <td width="200"></td>
                                            <th style="padding-right: 8px; float: right; ">Remarks:
                                            </th>
                                            <td><span class="label label-danger">  {{$loan->document_remarks}}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col -->

        <!-- /.col -->
        <!-- /.row -->
        <!-- END ACCORDION & CAROUSEL-->


        <div class="box-footer">


        </div>


    </div>

@endsection
@section('js')
    <script src="{{asset('assets/backend/bower_components/ckeditor/ckeditor.js')}}"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });

        $(function () {
            // Replace the <textarea id="editor1"> with a CKEditor
            // instance, using default configuration.
            CKEDITOR.replace('editor1')
            //bootstrap WYSIHTML5 - text editor
            $('.textarea').wysihtml5()
        })

    </script>
@endsection