@extends ('layouts.backend')
@section('title',' Borrower and Loan List ')
@section('heading','Select Borrower')
@section('small_heading','Please Select Borrower To Generate Document')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li class="active"><strong>Select Borrower to generate document  </strong></li>
@endsection
@section('css')
    <link rel="stylesheet"
          href="{{asset('assets/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">

@endsection
@section('js')
    <script src="{{asset('assets/backend/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>

    <script>
        $(document).ready(function () {
            $('#make').DataTable();
        });
    </script>
@endsection
@section('content')
    <div class="box box-default">
        <div class="box-header with-border">
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table id="make" class="table table-striped table-bordered table-hover dataTables-example">
                <thead>
                <tr>
                    <th>SN</th>
                    <th>English Name</th>
                    <th>Nepali name</th>
                    <th>clientId</th>
                    <th>Cizn/Reg. No.</th>
                    <th>Birth/Reg. Date</th>
                    <th>Action</th>
                    <th>Document Status</th>
                </tr>
                </thead>
                <tbody>
                @php($i=1)
                @foreach($ploan as $a)
                    <tr>@php($bid=$a->id)
                        <td>{{$i++}}</td>
                        <td>{{$a->english_name}}</td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->nepali_name}}</td>
                        <td>{{$a->client_id}}</td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->citizenship_number}}</td>

                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->dob_day}}
                            ÷{{$a->dob_month}}÷{{$a->dob_year}}</td>
                        <td>
                            <div class="btn-group">
                                <a class="btn btn-primary  btn-xs dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-user fa-fw"></i>
                                    Action <span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>  <a cla href="{{route('personal.choose',$a->id)}}">
                                            <i class="fa fa-check fa-fw"></i>Generate Document</a>
                                    </li>
                                    <li>
                                        <a href="{{route('personal_facilities.index',$a->id)}}" class="btn-xs"> <i
                                                    class="fa fa-pencil fa-fw"></i>Edit</a>
                                    </li>
                                    <li>

                                        <a href="">
                                            <form action="{{route('document.personal_delete',$a->id)}}" method="post">
                                                {{csrf_field()}}
                                                <input type="hidden" name="_method" value="delete">
                                                <button type="submit" class="btn btn-default btn-xs"
                                                        style="padding: 0; outline:none;border: none;background: none;"
                                                        onclick="return confirm('Are you sure to delete?')"><i
                                                            class="fa fa-trash fa-fw"></i>Delete
                                                </button>
                                            </form>
                                        </a>
                                    </li>
                                </ul>
                            </div>


                        </td>
                        <td ><span class="label label-warning label-sm">{{(\App\PersonalLoan::where([['borrower_id',$a->id]])->first())->document_status}}</span></td>

                    </tr>
                @endforeach

                @foreach($cloan as $a)
                    <tr>@php($bid=$a->id)
                        <td>{{$i++}}</td>
                        <td>{{$a->english_name}}</td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->nepali_name}}</td>
                        <td>{{$a->client_id}}</td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->registration_number}}</td>

                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->reg_day}}
                            ÷{{$a->reg_month}}÷{{$a->reg_year}}</td>

                        <td>
                            <div class="btn-group">
                                <a class="btn btn-primary  btn-xs dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-user fa-fw"></i>
                                    Action <span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>  <a cla href="{{route('corporate.choose',$a->id)}}">
                                            <i class="fa fa-check fa-fw"></i>Generate Document</a>
                                    </li>
                                    <li>
                                        <a href="{{route('corporate_facilities.index',$a->id)}}" class="btn-xs"> <i
                                                    class="fa fa-pencil fa-fw"></i>Edit</a>
                                    </li>
                                    <li>
                                        <a href="">
                                            <form action="{{route('document.corporate_delete',$a->id)}}" method="post">
                                                {{csrf_field()}}
                                                <input type="hidden" name="_method" value="delete">
                                                <button type="submit" class="btn btn-default btn-xs"
                                                        style="padding: 0; outline:none;border: none;background: none;"
                                                        onclick="return confirm('Are you sure to delete?')"><i
                                                            class="fa fa-trash fa-fw"></i>Delete
                                                </button>
                                            </form>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                        </td>
                        <td ><span class="label label-warning label-sm">{{(\App\CorporateLoan::where([['borrower_id',$a->id]])->first())->document_status}}</span></td>

                    </tr>
                @endforeach

                @foreach($jloan as $a)
                    <tr>
                        @php($bid=$a->id)
                        <td>{{$i++}}</td>
                        <td>
                            @if($a->joint1)
                                {{App\PersonalBorrower::find($a->joint1)->english_name}},
                            @endif
                            @if($a->joint2)
                                {{App\PersonalBorrower::find($a->joint2)->english_name}},
                            @endif
                            @if($a->joint3)
                                {{App\PersonalBorrower::find($a->joint2)->english_name}}
                            @endif
                        </td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">
                            @if($a->joint1)
                                {{App\PersonalBorrower::find($a->joint1)->nepali_name}},
                            @endif
                            @if($a->joint2)
                                {{App\PersonalBorrower::find($a->joint2)->nepali_name}},
                            @endif
                            @if($a->joint3)
                                {{App\PersonalBorrower::find($a->joint2)->nepali_name}}
                            @endif
                        </td>
                        <td>
                            @if($a->joint1)
                                {{App\PersonalBorrower::find($a->joint1)->client_id}},
                            @endif
                            @if($a->joint2)
                                {{App\PersonalBorrower::find($a->joint2)->client_id}},
                            @endif
                            @if($a->joint3)
                                {{App\PersonalBorrower::find($a->joint2)->client_id}}
                            @endif


                            </td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">
                            @if($a->joint1)
                                {{App\PersonalBorrower::find($a->joint1)->citizenship_number}},
                            @endif
                            @if($a->joint2)
                                {{App\PersonalBorrower::find($a->joint2)->citizenship_number}},
                            @endif
                            @if($a->joint3)
                                {{App\PersonalBorrower::find($a->joint2)->citizenship_number}}
                            @endif
                        </td>

                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">

                        </td>
                        <td>
                            <div class="btn-group">
                                <a class="btn btn-primary  btn-xs dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-user fa-fw"></i>
                                    Action <span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>  <a cla href="{{route('joint.choose',$a->id)}}">
                                            <i class="fa fa-check fa-fw"></i>Generate Document</a>
                                    </li>
                                    <li>
                                        <a href="{{route('joint_facilities.index',$a->id)}}" class="btn-xs"> <i
                                                    class="fa fa-pencil fa-fw"></i>Edit</a>
                                    </li>
                                    <li>

                                        <a href="">
                                            <form action="{{route('document.joint_delete',$a->id)}}" method="post">
                                                {{csrf_field()}}
                                                <input type="hidden" name="_method" value="delete">
                                                <button type="submit" class="btn btn-default btn-xs"
                                                        style="padding: 0; outline:none;border: none;background: none;"
                                                        onclick="return confirm('Are you sure to delete?')"><i
                                                            class="fa fa-trash fa-fw"></i>Delete
                                                </button>
                                            </form>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                        </td>
                        <td ><span class="label label-warning label-sm">{{(\App\JointLoan::where([['borrower_id',$a->id]])->first())->document_status}}</span></td>

                    </tr>
                @endforeach


                </tbody>
            </table>


        </div>


    </div>
    <!-- /.box-body -->

@endsection
